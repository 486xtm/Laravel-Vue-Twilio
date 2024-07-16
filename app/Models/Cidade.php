<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cidade extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'estado'];

    public function estado(): HasOne
    {
        return $this->hasOne(Estado::class);
    }
}
