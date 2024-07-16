<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReconciliation extends Model
{
    use HasFactory;

    protected $fillable = ['group','quota','deal', 'parcel', 'total_deal', 'comission', 'sell_date', 'closed_at', 'due_date','total','status_id'];
    
}
