<?php

namespace App\Console\Commands;

use App\Models\Payments\CustomerPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AdjustBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:adjust-balance {type} {date}';

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

        if (!in_array($this->argument('type'), CustomerPayment::PAYMENT_METHODS)) {
            $this->echo("Invalid Type");
            return Command::FAILURE;
        }

        $startDate = Carbon::parse($this->argument('date'));

        $paymentsToAdjust = CustomerPayment::from($startDate)->PaymentMethod($this->argument('type'))->get();
        $i = 0;
        foreach ($paymentsToAdjust as $p) {
            if ($i++ == 0)
                $p->resetBalance();
            else $p->recalculateBalance();
        }
    }
}
