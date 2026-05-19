<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'client_id',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'tax',
        'total',
        'currency',
        'logo_path',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the user who created this invoice
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client for this invoice
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get all items for this invoice
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}