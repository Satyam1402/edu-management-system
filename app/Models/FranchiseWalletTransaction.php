<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseWalletTransaction extends Model
{
    protected $fillable = [
        'franchise_id', 'type', 'amount', 'source', 'reference_id', 'meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'amount' => 'decimal:2'
    ];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }
}
