<?php

namespace App\Livewire\Reports;

use App\Models\Orders\Order;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSalesReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $page_title = '• Product Sales Report';
    public $search;

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
        $productTotals = Order::productTotals(
            $this->startDate ? Carbon::parse($this->startDate) : null,
            $this->endDate ? Carbon::parse($this->endDate) : null
        )
        ->when($this->search, function($query) {
            $query->where('products.name', 'like', '%' . $this->search . '%');
        })
        ->orderBy('total_quantity', 'desc')
        ->paginate(50);

        return view('livewire.reports.product-sales-report', [
            'productTotals' => $productTotals
        ]);
    }
} 