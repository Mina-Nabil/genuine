<?php

namespace App\Policies;

use App\Models\Users\User;
use App\Models\Materials\SupplierInvoice;
use App\Models\Users\AppLog;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupplierInvoice $supplierInvoice): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can add/edit payments.
     */
    public function pay(User $user, SupplierInvoice $supplierInvoice): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the payment due when the invoice is not paid.
     */
    public function updatePaymentDue(User $user, SupplierInvoice $supplierInvoice): Response
    {
        if (!$supplierInvoice->is_paid) {
            return Response::allow();
        }

        AppLog::error('Supplier invoice is not paid to update payment due', loggable: $supplierInvoice);
        return Response::deny('The invoice is already paid.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupplierInvoice $supplierInvoice): Response
    {
        return Response::allow();
    }

    public function editInfo(User $user, SupplierInvoice $supplierInvoice): bool
    {
        if ($supplierInvoice->payments()->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupplierInvoice $supplierInvoice): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SupplierInvoice $supplierInvoice): Response
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SupplierInvoice $supplierInvoice): Response
    {
        return Response::allow();
    }
}
