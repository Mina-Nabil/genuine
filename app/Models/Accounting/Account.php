<?php

namespace App\Models\Accounting;

use App\Models\Business\SoldPolicy;
use App\Models\Orders\Order;
use App\Models\Users\AppLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Account extends Model
{
    use HasFactory;
    const MORPH_TYPE = 'account';

    protected $table = 'accounts';
    protected $fillable = [
        'code',
        'name',
        'desc',
        'nature',
        'main_account_id',
        'parent_account_id', //list of accounts from the same main account
        'limit',
        'balance',
        'foreign_balance',
        'default_currency',
    ];

    const NATURE_CREDIT = 'credit';
    const NATURE_DEBIT = 'debit';
    const NATURES = [self::NATURE_DEBIT, self::NATURE_CREDIT];

    ////static functions
    public static function getEntries($account_id, Carbon $from, Carbon $to, $search = null)
    {
        return DB::table('journal_entries')
            ->join('entry_accounts', 'entry_accounts.journal_entry_id', '=', 'journal_entries.id')
            ->join('entry_titles', 'entry_titles.id', '=', 'journal_entries.entry_title_id')
            ->join('users', 'users.id', '=', 'journal_entries.user_id')
            ->where('entry_accounts.account_id', $account_id)
            ->whereBetween('journal_entries.created_at', [$from->format('Y-m-d H:i'), $to->format('Y-m-d H:i')])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('journal_entries.id', 'like', "%{$search}%")
                    ->orWhere('entry_titles.name', 'like', "%{$search}%")
                    ->orWhere('journal_entries.comment', 'like', "%{$search}%");
                });
            })
            ->groupBy('journal_entries.id')

            ->select('journal_entries.*', 'account_foreign_balance', 'account_balance', 'currency_rate', 'doc_url', 'users.username', 'entry_titles.name')
            ->selectRaw('IF(entry_accounts.nature = "debit" , entry_accounts.amount , 0 ) as debit_amount')
            ->selectRaw('IF(entry_accounts.nature = "credit" , entry_accounts.amount , 0 ) as credit_amount')
            ->selectRaw('IF(entry_accounts.nature = "debit" , entry_accounts.currency_amount , 0 ) as debit_foreign_amount')
            ->selectRaw('IF(entry_accounts.nature = "credit" , entry_accounts.currency_amount , 0 ) as credit_foreign_amount')
            ->get();
    }

    public static function newAccount($code, $name, $nature, $main_account_id, $parent_account_id = null, $desc = null, $is_seeding = false, $default_currency = JournalEntry::CURRENCY_EGP): self|false
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if (!$is_seeding && !$loggedInUser->can('create', self::class)) {
            return false;
        }

        $newAccount = new self([
            'code' => self::getNextCode($main_account_id, $parent_account_id) ?? 1,
            'name' => $name,
            'nature' => $nature,
            'parent_account_id' => $parent_account_id,
            'main_account_id' => $main_account_id,
            'desc' => $desc,
            'balance' => 0,
            'foreign_balance' => 0,
            'default_currency' => $default_currency,
        ]);
        try {
            $newAccount->save();
            AppLog::info('Created account', loggable: $newAccount);
            return $newAccount;
        } catch (Exception $e) {
            report($e);
            AppLog::error("Can't create account", desc: $e->getMessage());
            return false;
        }
    }

    public static function importAccounts($file = null)
    {
        try {
            DB::transaction(function () use ($file) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                self::query()->update([
                    'parent_account_id' => null,
                ]);
                $titles = EntryTitle::all();
                foreach ($titles as $t) {
                    if ($t->id == 1) {
                        continue;
                    }
                    $t->accounts()->sync([]);
                    $t->delete();
                }

                $entries = JournalEntry::all();
                foreach ($entries as $e) {
                    $e->accounts()->sync([]);
                    $e->delete();
                }
                self::query()->delete();
                MainAccount::query()->delete();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                if ($file) {
                    $spreadsheet = IOFactory::load($file);
                } else {
                    $spreadsheet = IOFactory::load(resource_path('import/AccountsTree.xlsx'));
                }
                if (!$spreadsheet) {
                    throw new Exception('Failed to read files content');
                }
                $activeSheet = $spreadsheet->getActiveSheet();
                $highestRow = $activeSheet->getHighestDataRow();
                $found_balances = [];
                $endLoop = false;
                for ($i = 2; $i <= $highestRow; $i++) {
                    $start_char = 'F';
                    $account_name = $activeSheet->getCell('F' . $i)->getValue();

                    while ($account_name == null) {
                        $start_char = chr(ord($start_char) - 1);
                        if ($start_char == 'A') {
                            $endLoop = true;
                            break;
                        }
                        $account_name = $activeSheet->getCell($start_char . $i)->getValue();
                    }
                    if ($endLoop) {
                        break;
                    }
                    $parent_name = $start_char == 'C' ? null : $activeSheet->getCell(chr(ord($start_char) - 1) . $i)->getValue();
                    $main_account_name = $activeSheet->getCell('B' . $i)->getValue();
                    $nature = strtolower($activeSheet->getCell('G' . $i)->getValue());
                    $desc = $activeSheet->getCell('H' . $i)->getValue();
                    $balance = $activeSheet->getCell('I' . $i)->getValue();

                    try {
                        $main_account = MainAccount::firstOrCreate(
                            [
                                'name' => $main_account_name,
                            ],
                            [
                                'code' => MainAccount::getNextCode() ?? 1,
                                'type' => MainAccount::getTypeByArabicName($main_account_name),
                                'desc' => $desc,
                            ],
                        );
                    } catch (QueryException $e) {
                        if ($e->getCode() == 23000) {
                            $main_account = MainAccount::firstOrCreate(
                                [
                                    'name' => $main_account_name,
                                ],
                                [
                                    'code' => MainAccount::getNextCode() ?? 1,
                                    'type' => MainAccount::getTypeByArabicName($main_account_name),
                                    'desc' => $desc,
                                ],
                            );
                        }
                    }

                    if (!$account_name) {
                        continue;
                    }
                    $parent_account = null;
                    if ($parent_name) {
                        $parent_account = self::byName($parent_name)->first();
                    }

                    $tmpAccount = self::newAccount(1, $account_name, $nature, $main_account->id, $parent_account?->id, $desc);

                    if ($balance) {
                        $found_balances[$tmpAccount->id] = [
                            'nature' => $balance > 0 ? $nature : ($nature == 'debit' ? 'credit' : 'debit'),
                            'amount' => abs($balance),
                            'currency' => 'EGP',
                        ];
                    }
                }

                $starting_entry = JournalEntry::newJournalEntry(1, is_seeding: true, accounts: $found_balances);
                if (!$starting_entry) {
                    throw new Exception('Import failed please check balances');
                }
                if (is_string($starting_entry)) {
                    throw new Exception($starting_entry);
                }
            });
        } catch (Exception $e) {
            report($e);
            return false;
        }
        return true;
    }

    public static function getNextCode($main_account_id, $parent_account_id)
    {
        return (DB::table('accounts')->selectRaw('MAX(code) as max_code')->where('parent_account_id', $parent_account_id)->where('main_account_id', $main_account_id)->first()?->max_code ?? 0) + 1;
    }

    ////model functions
    public function downloadAccountDetails(Carbon $from, Carbon $to)
    {
        $debit_entries = $this->debit_entries()->with('entry_title')->between($from, $to)->get();
        $credit_entries = $this->credit_entries()->with('entry_title')->between($from, $to)->get();
        $all_entries = $debit_entries->merge($credit_entries);
        $template = IOFactory::load(resource_path('import/accounting_sheets.xlsx'));
        if (!$template) {
            throw new Exception('Failed to read template file');
        }
        $newFile = $template->copy();
        $activeSheet = $newFile->getSheet(0);

        $activeSheet->getCell('C3')->setValue('تحليلى	' . $this->name);

        $i = 8;
        foreach ($all_entries as $e) {
            $activeSheet->getCell('C' . $i)->setValue($e->id);
            $activeSheet->getCell('D' . $i)->setValue(Carbon::parse($e->created_at)->format('d / M / Y'));

            $activeSheet->getCell('E' . $i)->setValue($e->entry_title->name);

            if ($e->debit_id) {
                $activeSheet->getCell('E' . $i)->setValue($e->amount);
                $activeSheet->getCell('H' . $i)->setValue($e->debit_balance);
            } else {
                $activeSheet->getCell('F' . $i)->setValue($e->amount);
                $activeSheet->getCell('H' . $i)->setValue($e->credit_balance);
            }
            $activeSheet->insertNewRowBefore($i);
        }

        $writer = new Xlsx($newFile);
        $file_path =  '/account_balance.xlsx';
        $public_file_path = storage_path($file_path);
        $writer->save($public_file_path);

        return response()->download($public_file_path)->deleteFileAfterSend(true);
    }

    /** returns new balance after update */
    public function updateBalance($amount, $type, $is_seeding = false)
    {
        if (!$is_seeding) {
            /** @var User */
            $loggedInUser = Auth::user();
            if (!$loggedInUser->can('update', $this)) {
                return false;
            }
        }
        if ($this->nature != $type) {
            $amount = -1 * $amount;
        }

        $this->balance = $this->balance + $amount;
        try {
            $this->save();
            return $this->balance;
        } catch (Exception $e) {
            report($e);
            return 0;
        }
    }

    public function updateForeignBalance($amount, $type, $is_seeding = false)
    {
        if (!$is_seeding) {
            /** @var User */
            $loggedInUser = Auth::user();
            if (!$loggedInUser->can('update', $this)) {
                return false;
            }
        }
        if ($this->nature != $type) {
            $amount = -1 * $amount;
        }

        $this->foreign_balance = $this->foreign_balance + $amount;
        try {
            $this->save();
            return $this->foreign_balance;
        } catch (Exception $e) {
            report($e);
            return 0;
        }
    }

    public function needsApproval($amount)
    {
        return $this->limit <= $amount;
    }

    public function editInfo($code, $name, $nature, $main_account_id, $parent_account_id = null, $desc = null, $default_currency = JournalEntry::CURRENCY_EGP): bool
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if (!$loggedInUser->can('update', $this)) {
            return false;
        }

        try {
            $this->update([
                'code' => $code,
                'name' => $name,
                'nature' => $nature,
                'main_account_id' => $main_account_id,
                'parent_account_id' => $parent_account_id,
                'desc' => $desc,
                'default_currency' => $default_currency,
            ]);
            AppLog::info('Updating account', loggable: $this);
            return $this->save();
        } catch (Exception $e) {
            report($e);
            AppLog::error("Can't edit account", desc: $e->getMessage(), loggable: $this);
            return false;
        }
    }

    ///attributes
    public function getFullCodeAttribute()
    {
        $this->loadMissing('parent_account');
        $this->loadMissing('main_account');

        if (!$this->parent_account) {
            return $this->main_account->code . '-' . $this->code;
        } else {
            return $this->parent_account->full_code . '-' . $this->code;
        }
    }

    public function getTotalBalanceAttribute()
    {
        $this->loadMissing('children_accounts');
        $blnce = 0;
        foreach ($this->children_accounts as $ac) {
            $blnce += $ac->total_balance;
        }
        return $blnce + $this->balance;
    }

    public function getIsForeignAttribute()
    {
        $this->default_currency !== JournalEntry::CURRENCY_EGP;
    }

    public function getHasChildrenAttribute()
    {
        $this->loadCount('children_accounts');
        return $this->children_accounts_count > 0;
    }

    public function getIsTopParentAttribute()
    {
        return is_null($this->parent_account_id);
    }

    ///scopes
    public function scopeByNature($query, $nature)
    {
        return $query->where('nature', $nature);
    }

    public function scopeByName($query, $text)
    {
        return $query->where('accounts.name', '=', "$text");
    }

    public function scopeSearchBy($query, $text)
    {
        return $query->where('accounts.name', 'LIKE', "%$text%");
    }

    public function scopeByMainAccount($query, $main_account_id)
    {
        return $query->where('main_account_id ', $main_account_id);
    }

    public function scopeOrderByCode($query)
    {
        return $query->select('accounts.*')->join('main_accounts', 'main_accounts.id', '=', 'accounts.main_account_id')->orderBy('main_accounts.code')->orderBy('accounts.code');
    }

    public function scopeParentAccounts($query)
    {
        return $query->whereNull('parent_account_id');
    }

    ////relations
    public function credit_entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function debit_entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function main_account()
    {
        return $this->belongsTo(MainAccount::class);
    }

    public function parent_account()
    {
        return $this->belongsTo(self::class, 'parent_account_id');
    }

    public function children_accounts()
    {
        return $this->hasMany(self::class, 'parent_account_id');
    }
}
