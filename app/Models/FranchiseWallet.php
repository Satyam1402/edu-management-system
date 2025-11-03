<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FranchiseWallet extends Model
{
    protected $fillable = ['franchise_id', 'balance'];

    public function franchise()
    {
        return $this->belongsTo(Franchise::class); // Assuming you have Franzise Model
    }

    public function transactions()
    {
        return $this->hasMany(FranchiseWalletTransaction::class, 'franchise_id');
    }
}
