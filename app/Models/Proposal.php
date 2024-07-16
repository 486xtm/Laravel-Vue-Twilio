<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    use HasFactory;
    protected $fillable = ['group','quota','proposalType','price','comission','observation','lead','status','user' ];
    
    public function receivable(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }

    public function proposalType(): HasOne
    {
        return $this->hasOne(ProposalType::class);
    }
}
