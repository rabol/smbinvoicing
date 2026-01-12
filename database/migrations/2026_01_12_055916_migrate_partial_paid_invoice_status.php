<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing invoices with status 'invoiced' that have partial payments
        // to the new 'partial_paid' status
        DB::table('invoices')
            ->where('status', 'invoiced')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('payments')
                    ->whereColumn('payments.invoice_id', 'invoices.id');
            })
            ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.id) > 0')
            ->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = invoices.id) < invoices.total_amount')
            ->update(['status' => 'partial_paid']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert partial_paid status back to invoiced
        DB::table('invoices')
            ->where('status', 'partial_paid')
            ->update(['status' => 'invoiced']);
    }
};
