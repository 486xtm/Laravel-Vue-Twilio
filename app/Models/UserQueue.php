<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQueue extends Model
{
    use HasFactory;

    protected $fillable = ['position', 'lead_id', 'status_id', 'user_id'];
}
