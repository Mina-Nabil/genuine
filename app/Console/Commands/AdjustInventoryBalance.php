<?php

namespace App\Console\Commands;

use App\Models\Payments\CustomerPayment;
use App\Models\Products\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AdjustInventoryBalance extends Command
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
            $this->warn("Invalid date, format should be Y-m-d");
            return Command::FAILURE;
        }

        if (!in_array($this->argument('type'), ['raw_materials', 'product'])) {
            $this->warn("Invalid Type");
            return Command::FAILURE;
        }

        $startDate = Carbon::parse($this->argument('date'));


        $transactionsToAdjust = Transaction::startFrom($startDate)->type($this->argument('type'))->get();

        foreach ($transactionsToAdjust as $ta) {
                $ta->recalculateBalance();
        }

    }
}
