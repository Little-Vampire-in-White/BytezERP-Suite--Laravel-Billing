<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Http\Request;

class ExternalIntegrationController extends Controller
{
    /**
     * Fetch invoices for the connected ERP account.
     */
    public function getInvoices(Request $request)
    {
        // We filter by the authenticated user to maintain the 
        // "User-Only Invoices" feature implemented in your project.
        $invoices = Invoice::where('user_id', $request->user()->id)
            ->with(['client', 'items'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $invoices
        ]);
    }

    /**
     * Fetch clients to sync with Bytez-ERP.
     */
    public function getClients()
    {
        $clients = Client::all();

        return response()->json([
            'status' => 'success',
            'data' => $clients
        ]);
    }
}