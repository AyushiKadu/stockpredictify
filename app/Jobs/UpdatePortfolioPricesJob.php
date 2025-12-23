<?php

namespace App\Jobs;

use App\Models\Portfolio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class UpdatePortfolioPricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Portfolio $portfolio;

    public function __construct(Portfolio $portfolio)
    {
        $this->portfolio = $portfolio;
    }

    public function handle(): void
    {
        // Load holdings for this portfolio
        $holdings = $this->portfolio->holdings;

        foreach ($holdings as $holding) {
            try {
                $symbol = strtoupper($holding->symbol) . '.NS';
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1d";

                $json = Http::withoutVerifying()->get($url)->json();
                $price = $json['chart']['result'][0]['meta']['regularMarketPrice']
                          ?? $holding->buy_price;

                $holding->current_price = $price;
                $holding->save();
            } catch (\Throwable $e) {
                // If API fails, keep old current_price
            }
        }
    }
}

