<?php

namespace App\Livewire\Reports;

use App\Models\Payments\CustomerPayment;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\AlertFrontEnd;

class PaymentTitleReport extends Component
{
    use WithPagination, AlertFrontEnd;

    public $startDate;
    public $endDate;
    public $page_title = '• Payment Title Report';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $titleTotals = CustomerPayment::totalsByTitle(
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null
        )->get();

        return view('livewire.reports.payment-title-report', [
            'titleTotals' => $titleTotals
        ]);
    }
} 