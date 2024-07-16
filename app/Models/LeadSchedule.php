<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_date','message'];

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }
}
