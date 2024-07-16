<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadArchiveReason extends Model
{
    use HasFactory;

    protected $fillable = ['reason'];

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }
}
