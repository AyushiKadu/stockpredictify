<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Portfolio;
use App\Models\Holding;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // REAL STOCKS + SECTORS (NSE India)
        $stockSectorMap = [
            'INFY'       => 'IT',
            'TCS'        => 'IT',
            'WIPRO'      => 'IT',
            'HCLTECH'    => 'IT',
            'TECHM'      => 'IT',

            'HDFCBANK'   => 'Banking',
            'ICICIBANK'  => 'Banking',
            'KOTAKBANK'  => 'Banking',
            'AXISBANK'   => 'Banking',
            'SBIN'       => 'Banking',

            'RELIANCE'   => 'Energy',
            'ONGC'       => 'Energy',
            'BPCL'       => 'Energy',
            'GAIL'       => 'Energy',
            'IOC'        => 'Energy',

            'TATAMOTORS' => 'Automobile',
            'MARUTI'     => 'Automobile',
            'M&M'        => 'Automobile',
            'HEROMOTOCO' => 'Automobile',
            'BAJAJ-AUTO' => 'Automobile',

            'HINDUNILVR' => 'FMCG',
            'ITC'        => 'FMCG',
            'BRITANNIA'  => 'FMCG',
            'NESTLEIND'  => 'FMCG',

            'SUNPHARMA'  => 'Pharma',
            'CIPLA'      => 'Pharma',
            'DRREDDY'    => 'Pharma',
            'DIVISLAB'   => 'Pharma',

            'BHARTIARTL' => 'Telecom',
            'VODAFONE'   => 'Telecom',

            'TATASTEEL'  => 'Metals',
            'JSWSTEEL'   => 'Metals',
            'HINDALCO'   => 'Metals',
        ];

        $stocks = array_keys($stockSectorMap);

        // CREATE MANY PORTFOLIOS
        for ($i = 1; $i <= 15; $i++) {

            $portfolio = Portfolio::create([
                'name' => $faker->company() . ' Portfolio',
                'risk_score' => $faker->randomElement(['Low', 'Medium', 'High']),
            ]);

            // HOLDINGS PER PORTFOLIO
            for ($j = 1; $j <= rand(4, 8); $j++) {

                $symbol = $faker->randomElement($stocks);
                $sector = $stockSectorMap[$symbol];

                // Buy price
                $buy = $faker->randomFloat(2, 100, 3000);

                // Live price (simulate variation)
                $current = round($buy * $faker->randomFloat(3, 0.95, 1.15), 2);

                Holding::create([
                    'portfolio_id'  => $portfolio->id,
                    'symbol'        => $symbol,
                    'quantity'      => $faker->numberBetween(5, 150),
                    'buy_price'     => round($buy, 2),
                    'current_price' => $current,
                    'sector'        => $sector,
                ]);
            }
        }
    }
}
