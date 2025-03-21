<?php

namespace App\Livewire\Materials;

use App\Models\Materials\InvoiceRawMaterial;
use App\Models\Materials\RawMaterial;
use App\Models\Materials\Supplier;
use App\Models\Materials\SupplierInvoice;
use App\Models\Payments\CustomerPayment;
use App\Traits\AlertFrontEnd;
use Livewire\Component;

class InvoiceShow extends Component
{
    use AlertFrontEnd;

    public $invoice;
    public $page_title;

    // remove raw material
    public $returnedRawMateralId;
    public $addRawmaterialModal = false;
    public $searchRawMaterialText;

    //remocve raw material quantity
    public $returnedRawMaterial;
    public $returnedRawMaterialQty;

    public $selectedRawMaterial;
    public $quantity;

    public $payAmountSection = false;
    public $payAmountValue;
    public $payAmountPymtMethod = CustomerPayment::PYMT_CASH;
    public $paidNow = true;
    public $paymentDate;

    public $updateExtraFeeModal = false;
    public $extraFeeAmount;
    public $extraFeeDesc;
    public $confirmRemoveExtraFeeModal = false;


    public $PAY_BY_PAYMENT_METHOD;

    public function openUpdateExtraFeeModal()
    {
        $this->extraFeeAmount = $this->invoice->extra_fee_amount;
        $this->extraFeeDesc = $this->invoice->extra_fee_description;
        $this->updateExtraFeeModal = true;
    }

    public function closeUpdateExtraFeeModal()
    {
        $this->updateExtraFeeModal = false;
    }

    public function updateExtraFee()
    {
        $this->validate(
            [
                'extraFeeAmount' => 'required|numeric|not_in:0',
                'extraFeeDesc' => 'required|string|max:255',
            ],
            [
                'extraFeeAmount.required' => 'Please enter the extra fee amount',
                'extraFeeAmount.numeric' => 'The extra fee amount must be a number',
                'extraFeeAmount.not_in' => 'The extra fee amount must not be zero',
                'extraFeeDesc.required' => 'Please enter the description',
                'extraFeeDesc.string' => 'The description must be a string',
                'extraFeeDesc.max' => 'The description may not be greater than 255 characters',
            ],
        );

        $res = $this->invoice->updateExtraFee($this->extraFeeDesc, $this->extraFeeAmount);

        if ($res) {
            $this->alertSuccess('Extra fee updated successfully');
            $this->closeUpdateExtraFeeModal();
        } else {
            $this->alertFailed();
        }
    }

    public function confirmRemoveExtraFees(){
        $this->confirmRemoveExtraFeeModal = true;
    }

    public function closeConfirmRemoveExtraFees(){
        $this->confirmRemoveExtraFeeModal = false;
    }

    public function removeExtraFee(){
        $res = $this->invoice->removeExtraFee();

        if ($res) {
            $this->alertSuccess('Extra fee removed successfully');
            $this->closeConfirmRemoveExtraFees();
        } else {
            $this->alertFailed();
        }
    }

    public function openPayAmountSection(){
        $this->payAmountSection = true;
    }

    public function closePayAmountSection(){
        $this->reset(['payAmountSection' , 'payAmountValue', 'payAmountPymtMethod', 'paidNow', 'paymentDate']);
    }

    public function payAmount(){

        $this->validate([
            'payAmountValue' => 'required|numeric|min:1|max:' . $this->invoice->remaining_to_pay,
            'payAmountPymtMethod' => 'required|in:' . implode(',', CustomerPayment::PAYMENT_METHODS),
            'paymentDate' => 'nullable|date|required_if:paidNow,false',
        ],[
            'payAmountValue.required' => 'Please enter the amount',
            'payAmountValue.numeric' => 'The amount must be a number',
            'payAmountValue.min' => 'The amount must be at least 1',
            'payAmountValue.max' => 'The amount must be at most ' . $this->invoice->remaining_to_pay,
            'payAmountPymtMethod.required' => 'Please select a payment method',
            'payAmountPymtMethod.in' => 'The selected payment method is invalid',
            'paymentDate.required_if' => 'Please select a payment date',
            'paymentDate.date' => 'The selected payment date is invalid',
        ]);

        $res = $this->invoice->createPayment($this->payAmountValue, $this->payAmountPymtMethod,  now());

        if ($res) {
            $this->alertSuccess('Amount paid successfully');
            $this->closePayAmountSection();
            $this->mount($this->invoice->id);
        } else {
            $this->alertFailed();
        }
    }
    

    public function confirmPayInvoice($method){
        $this->PAY_BY_PAYMENT_METHOD = $method;
    }

    public function closeConfirmPayInvoice(){
        $this->PAY_BY_PAYMENT_METHOD = null;
    }
    

    public function payInvoice()
    {
        $res = $this->invoice->createPayment($this->invoice->remaining_to_pay, $this->PAY_BY_PAYMENT_METHOD, now(), false);

        if ($res) {
            $this->alertSuccess('Invoice paid successfully');
            $this->closeConfirmPayInvoice();
            $this->mount($this->invoice->id);
        } else {
            $this->alertFailed();
        }
    }

    public function openReturnRawMaterialQtyModal($rawMaterialId)
    {
        $this->returnedRawMaterial = InvoiceRawMaterial::where('supplier_invoice_id', $this->invoice->id)
            ->where('raw_material_id', $rawMaterialId)
            ->firstOrFail();
    }

    public function closeReturnRawMaterialQtyModal()
    {
        $this->returnedRawMaterial = null;
    }

    public function returnRawMaterialQty()
    {
        $this->validate(
            [
                'returnedRawMaterialQty' => 'required|numeric|min:1|max:' . $this->returnedRawMaterial->quantity,
            ],
            [
                'returnedRawMaterialQty.required' => 'Please enter the quantity',
                'returnedRawMaterialQty.numeric' => 'The quantity must be a number',
                'returnedRawMaterialQty.min' => 'The quantity must be at least 1',
                'returnedRawMaterialQty.max' => 'The quantity must be at most ' . $this->returnedRawMaterial->quantity,
            ],
        );

        $res = $this->invoice->returnRawMaterial($this->returnedRawMaterial->raw_material_id, $this->returnedRawMaterialQty);

        if ($res) {
            $this->alertSuccess('Raw material quantity retuned successfully');
            $this->closeReturnRawMaterialQtyModal();
        } else {
            $this->alertFailed();
        }
    }

    public function openAddRawMaterialModal()
    {
        $this->addRawmaterialModal = true;
    }

    public function closeAddRawMaterialModal()
    {
        $this->addRawmaterialModal = false;
    }

    public function addRawMaterial()
    {
        $this->validate(
            [
                'selectedRawMaterial' => 'required|exists:raw_materials,id',
                'quantity' => 'required|numeric|min:1',
            ],
            [
                'selectedRawMaterial.required' => 'Please select a raw material',
                'selectedRawMaterial.exists' => 'The selected raw material does not exist',
                'quantity.required' => 'Please enter the quantity',
                'quantity.numeric' => 'The quantity must be a number',
                'quantity.min' => 'The quantity must be at least 1',
            ],
        );

        $res = $this->invoice->addRawMaterial($this->selectedRawMaterial, $this->quantity);

        if ($res) {
            $this->alertSuccess('Raw material added successfully');
            $this->closeAddRawMaterialModal();
        } else {
            $this->alertFailed();
        }
    }

    public function openReturnRawMaterialModal($id)
    {
        $this->returnedRawMateralId = $id;
    }

    public function closeReturnRawMaterialModal()
    {
        $this->returnedRawMateralId = null;
    }

    public function returnRawMaterial()
    {
        $res = $this->invoice->returnAllQuantityOfRawMaterial($this->returnedRawMateralId);

        if ($res) {
            $this->alertSuccess('Raw material retuned successfully');
            $this->closeReturnRawMaterialModal();
        } else {
            $this->alertFailed();
        }
    }

    public function mount($id)
    {
        $this->invoice = SupplierInvoice::findOrFail($id);
        $this->page_title = '• Invoice ' . ($this->invoice->invoice_number ? '• '.$this->invoice->invoice_number : $this->invoice->id);   
    }

    public $isOpenUpdateDue;
    public $editedPaymentDue;

    public function openUpdateDue(){
        $this->isOpenUpdateDue = true;
        $this->editedPaymentDue = $this->invoice->payment_due->todatestring();
    }

    public function closeUpdateDue(){
        $this->reset(['isOpenUpdateDue','editedPaymentDue']);
    }

    public function updatePaymentDue(){
        $this->authorize('updatePaymentDue',$this->invoice);

        $res = $this->invoice->updatePaymentDue($this->editedPaymentDue);

        if ($res) {
            $this->alertSuccess('Payment due updated successfully');
            $this->closeUpdateDue();
            $this->mount($this->invoice->id);
        } else {
            $this->alertFailed();
        }
    }

    public function render()
    {
        $rawMaterials = $this->invoice->supplier
        ->avialableRawMaterials()
        ->search($this->searchRawMaterialText)
        ->take(10)
        ->get();

        $PAYMENT_METHODS =  CustomerPayment::PAYMENT_METHODS;

        return view('livewire.materials.invoice-show', [
            'rawMaterials' => $rawMaterials,
            'PAYMENT_METHODS' => $PAYMENT_METHODS,
        ])->layout('layouts.app', ['page_title' => $this->page_title, 'invoices' => 'active']);
    }
}
