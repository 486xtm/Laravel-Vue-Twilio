<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name','document', 'address', 'number', 'complement', 'region', 'zip', 'city', 'state', 'accountable', 'email', 'phone'];

}
