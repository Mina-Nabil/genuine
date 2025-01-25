<?php

namespace App\Livewire\Materials;

use App\Models\Materials\Supplier;
use App\Models\Payments\CustomerPayment;
use App\Traits\AlertFrontEnd;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierShow extends Component
{
    use AlertFrontEnd , WithPagination;
    public $page_title;

    public $supplier;

    public $editInfoSection = false;
    public $supplierName;
    public $supplierPhone1;
    public $supplierPhone2;
    public $supplierContactName;
    public $supplierContactPhone;

    //balance
    public $isOpenAddToBalance;
    public $AddedAmount;
    public $AddedPaymentMethod;
    public $AddedPaymentDate;
    public $AddedIsNowPaymentDate = true;
    public $AddedPaymentNote;

    public function openEditInfoSection()
    {
        $this->supplierName = $this->supplier->name;
        $this->supplierPhone1 = $this->supplier->phone1;
        $this->supplierPhone2 = $this->supplier->phone2;
        $this->supplierContactName = $this->supplier->contact_name;
        $this->supplierContactPhone = $this->supplier->contact_phone;

        $this->editInfoSection = true;
    }

    public function closeEditInfoSection()
    {
        $this->reset(['supplierName', 'supplierPhone1', 'supplierPhone2', 'supplierContactName', 'supplierContactPhone', 'editInfoSection']);
    }

    public function editInfo()
    {
        $this->validate([
            'supplierName' => 'required|string|max:255',
            'supplierPhone1' => 'required|string|max:255',
            'supplierPhone2' => 'nullable|string|max:255',
            'supplierContactName' => 'nullable|string|max:255',
            'supplierContactPhone' => 'nullable|string|max:255',
        ]);

        $res = $this->supplier->editInfo($this->supplierName, $this->supplierPhone1, $this->supplierPhone2, null, null, $this->supplierContactName, $this->supplierContactPhone);

        if ($res) {
            $this->closeEditInfoSection();
            $this->alertSuccess('Supplier updated!');
        } else {
            $this->alertFailed();
        }
    }

    public function addToBalance()
    {
        $this->authorize('updateSupplierBalance', $this->supplier);

        $paymentDate = null;
        if ($this->AddedIsNowPaymentDate) {
            $paymentDate = now();
        } else {
            $this->validate([
                'AddedPaymentDate' => 'required|date',
            ]);
            $paymentDate = $this->AddedPaymentDate;
        }

        $this->validate([
            'AddedAmount' => 'required|numeric|min:1',
            'AddedPaymentMethod' => 'required|in:' . implode(',', CustomerPayment::PAYMENT_METHODS),
            'AddedPaymentNote' => 'nullable|string',
        ]);

        $res = $this->supplier->deductBalanceWithPayment($this->AddedAmount, $this->AddedPaymentMethod, Carbon::parse($paymentDate), $this->AddedPaymentNote);

        if ($res) {
            $this->closeAddToBalanceSection();
            $this->mount($this->supplier->id);
            $this->alertSuccess('Balance updated!');
        } else {
            $this->alertFailed();
        }
    }

    public function openAddToBalanceSection()
    {
        $this->isOpenAddToBalance = true;
    }

    public function closeAddToBalanceSection()
    {
        $this->reset(['isOpenAddToBalance', 'AddedAmount', 'AddedPaymentMethod', 'AddedPaymentDate', 'AddedIsNowPaymentDate', 'AddedPaymentNote']);
    }

    public function mount($id)
    {
        $this->supplier = Supplier::findOrFail($id);
        $this->page_title = '• Supplier • ' . $this->supplier->name;
    }

    public function render()
    {
        $PAYMENT_METHODS = CustomerPayment::PAYMENT_METHODS;
        $supplierPayments = $this->supplier->payments()->latest()->paginate(5, ['*'], 'paymentsPage');
        $supplierTransactions = $this->supplier->transactions()->latest()->paginate(5, ['*'], 'transactionsPage');
        $supplierMaterials = $this->supplier->rawMaterials()->paginate(10, ['*'], 'materialsPage');

        return view('livewire.materials.supplier-show',[
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
            'supplierPayments' => $supplierPayments,
            'supplierTransactions' => $supplierTransactions,
            'supplierMaterials' => $supplierMaterials
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'suppliers' => 'active']);
    }
}
