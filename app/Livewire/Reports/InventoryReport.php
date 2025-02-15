<?php

namespace App\Livewire\Reports;

use App\Models\Products\Transaction;
use Livewire\Component;
use Carbon\Carbon;

class InventoryReport extends Component
{
    public $creation_date_from;
    public $creation_date_to;
    public $edited_creation_date_from;
    public $edited_creation_date_to;
    public $Edited_creation_date_from_sec = false;

    public function openFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = true;
        $this->edited_creation_date_from = $this->creation_date_from;
        $this->edited_creation_date_to = $this->creation_date_to;
    }

    public function closeFilterCreationDate()
    {
        $this->Edited_creation_date_from_sec = false;
        $this->edited_creation_date_from = null;
        $this->edited_creation_date_to = null;
    }

    public function mount()
    {
        $this->creation_date_from = now()->startOfMonth()->toDateString();
        $this->creation_date_to = now()->toDateString();
    }

    public function setFilterCreationDate()
    {
        $this->creation_date_from = $this->edited_creation_date_from;
        $this->creation_date_to = $this->edited_creation_date_to;
        $this->closeFilterCreationDate();
    }

    public function clearFilterCreationDates()
    {
        $this->reset(['creation_date_from', 'creation_date_to']);
    }

    public function render()
    {
        $inventories = Transaction::productionReport(Carbon::parse($this->creation_date_from), Carbon::parse($this->creation_date_to))->get();
        $total_raw = $inventories->whereNotNull('raw_name')->sum('trans_count');
        $total_prod = $inventories->whereNotNull('prod_name')->sum('trans_count');

        return view('livewire.reports.inventory-report', [
            'inventories' => $inventories,
            'total_raw' => $total_raw,
            'total_prod' => $total_prod,
        ])->layout('layouts.app', ['page_title' => 'Production Report', 'inventoryReport' => 'active']);
    }
}
