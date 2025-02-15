<?php

namespace App\Console\Commands;

use App\Models\Payments\CustomerPayment;
use App\Models\Products\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AdjustBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:adjust-inventory-balance {type} {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!Carbon::canBeCreatedFromFormat($this->argument('date'), "Y-m-d")) {
            $this->echo("Invalid date, format should be Y-m-d");
            return Command::FAILURE;
        }

        if (!in_array($this->argument('type'), ['raw_material', 'product'])) {
            $this->echo("Invalid Type");
            return Command::FAILURE;
        }

        $startDate = Carbon::parse($this->argument('date'));

        $transactionsToAdjust = Transaction::from($startDate)->type($this->argument('type'))->get();
        $i = 0;
        foreach ($transactionsToAdjust as $ta) {
            if ($i++ == 0)
                $ta->resetBalance();
            else $ta->recalculateBalance();
        }

        $ta->inventory->updateOnHandWithNewValue($ta->after, 'Adjusted from manual command')
    }
}
