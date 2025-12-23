<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Holding extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'symbol',
        'quantity',
        'buy_price',
        'current_price',
        'sector',
    ];

    // RELATIONSHIP
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    /**
     * MODEL EVENTS
     * Clean data before saving
     */
    protected static function booted(): void
    {
        static::creating(function (Holding $holding) {
            // Ensure symbol is uppercase
            $holding->symbol = strtoupper(trim($holding->symbol));

            // If current_price not set, start with buy_price
            if (is_null($holding->current_price)) {
                $holding->current_price = $holding->buy_price;
            }
        });

        static::updating(function (Holding $holding) {
            // Again keep symbol clean on update
            $holding->symbol = strtoupper(trim($holding->symbol));
        });
    }

    /**
     * ACCESSOR: total current value of this holding
     */
    protected function value(): Attribute
    {
        return Attribute::get(fn () =>
            round($this->quantity * $this->current_price, 2)
        );
    }

    /**
     * ACCESSOR: profit / loss absolute
     */
    protected function gainLoss(): Attribute
    {
        return Attribute::get(fn () =>
            round(($this->current_price - $this->buy_price) * $this->quantity, 2)
        );
    }

    /**
     * ACCESSOR: profit / loss percentage
     */
    protected function gainLossPercent(): Attribute
    {
        return Attribute::get(function () {
            $investment = $this->buy_price * $this->quantity;
            if ($investment <= 0) {
                return 0;
            }
            return round(($this->gain_loss / $investment) * 100, 2);
        });
    }

    /**
     * MUTATOR: Quantity rounded
     */
    protected function quantity(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => round($value)
        );
    }
}
