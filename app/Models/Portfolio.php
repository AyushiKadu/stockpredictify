<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\UuidTrait;

class Portfolio extends Model
{
    use HasFactory, UuidTrait;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'risk_score',
    ];

    // -------------------------
    // RELATIONSHIP
    // -------------------------
    public function holdings()
    {
        return $this->hasMany(Holding::class);
    }

    // -------------------------
    // ACCESSOR: Total Investment
    // -------------------------
    public function getTotalInvestmentAttribute()
    {
        return $this->holdings->sum(fn ($h) => $h->quantity * $h->buy_price);
    }

    // -------------------------
    // ACCESSOR: Current Value
    // -------------------------
    public function getTotalCurrentValueAttribute()
    {
        return $this->holdings->sum(fn ($h) => $h->quantity * $h->current_price);
    }

    // -------------------------
    // ACCESSOR: Profit/Loss
    // -------------------------
    public function getProfitLossAttribute()
    {
        return $this->total_current_value - $this->total_investment;
    }

    // -------------------------
    // ACCESSOR: Profit/Loss Percentage
    // -------------------------
    public function getProfitLossPercentAttribute()
    {
        if ($this->total_investment <= 0) return 0;

        return ($this->profit_loss / $this->total_investment) * 100;
    }
}
