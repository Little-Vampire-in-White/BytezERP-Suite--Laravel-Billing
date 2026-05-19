<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine if the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Check if the user owns this invoice
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine if the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine if the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine if the user can download the invoice PDF.
     */
    public function downloadPdf(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->user_id;
    }

    /**
     * Determine if the user can claim the invoice.
     */
    public function claim(User $user, Invoice $invoice): bool
    {
        // Allow claiming only if invoice has no owner
        return $invoice->user_id === null;
    }
}