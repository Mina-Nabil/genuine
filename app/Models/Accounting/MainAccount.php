<?php

namespace App\Models\Accounting;

use App\Models\Users\AppLog;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MainAccount extends Model
{

    use HasFactory;

    const MORPH_TYPE = 'main_account';

    protected $table = 'main_accounts';
    protected $fillable = ['code', 'name', 'desc', 'type'];
    public $timestamps = false;


    const TYPE_EXPENSE = 'expense';
    const TYPE_REVENUE = 'revenue';
    const TYPE_ASSET = 'asset';
    const TYPE_LIABILITY = 'liability';
    const TYPE_EQUITY = 'equity';
    const TYPES = [
        self::TYPE_EXPENSE,
        self::TYPE_REVENUE,
        self::TYPE_ASSET,
        self::TYPE_LIABILITY,
        self::TYPE_EQUITY,
    ];

    ////static functions
    public static function newMainAccount($name, $type, $desc = null, $is_seeding = false): self|false
    {
        /** @var User */
        $loggedInUser = Auth::user();
        if (!$is_seeding && !$loggedInUser->can('create', Account::class)) return false;

        $newType = new self([
            'name' => $name,
            'type' => $type,
            'desc' => $desc,
        ]);
        try {
            $newType->save();
            AppLog::info('Created account type', loggable: $newType);
            return $newType;
        } catch (Exception $e) {
            report($e);
            AppLog::error("Can't create account type", desc: $e->getMessage());
            return false;
        }
    }

    public static function getTypeByArabicName($name)
    {
        switch ($name) {
            case 'اصول':
                return self::TYPE_ASSET;
            case 'حقوق ملكية':
                return self::TYPE_EQUITY;
            case 'خصوم':
                return self::TYPE_LIABILITY;
            case 'المصروفات':
                return self::TYPE_EXPENSE;
            case 'ايرادات':
                return self::TYPE_REVENUE;
            default:
                return self::TYPE_ASSET;
        }
    }

    public static function getNextCode()
    {
        return (DB::table("main_accounts")
            ->selectRaw("MAX(code) as max_code")
            ->first()?->max_code ?? 0) + 1;
    }


    ////model functions
    public function editInfo($name, $type, $desc = null): bool
    {
        $this->update([
            'name' => $name,
            'type' => $type,
            'desc' => $desc,
        ]);
        try {
            AppLog::info('Updating account type', loggable: $this);
            return $this->save();
        } catch (Exception $e) {
            report($e);
            AppLog::error("Can't edit account type", desc: $e->getMessage(), loggable: $this);
            return false;
        }
    }

    public function scopeSearch($query, $term)
    {
        return $query
            ->where('name', 'LIKE', '%' . $term . '%')
            ->orWhere('desc', 'LIKE', '%' . $term . '%')
            ->orderBy('id', 'desc'); // Order by the newest added
    }

    ////relations
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
