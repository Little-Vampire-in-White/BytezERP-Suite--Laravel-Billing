<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address'
    ];

    /**
     * Get all invoices for this client
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}