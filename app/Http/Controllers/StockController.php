<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StockController extends Controller
{
    /**
     * ============================
     * BASIC STOCK PREDICT VIEW
     * ============================
     */
    public function index()
    {
        return view('stocks.index');
    }

    /**
     * Fetch 1-month price chart from Yahoo Finance
     * Send cleaned prices to Flask /predict for next-day prediction
     */
    public function fetch(Request $request)
    {
        $symbol = strtoupper(trim($request->input('symbol')));
        $yahooSymbol = $symbol . ".NS"; // For NSE India stocks only

        $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$yahooSymbol}?interval=1d&range=1mo";

        try {
            $response = Http::withoutVerifying()->get($url);

            if ($response->failed()) {
                return back()->with('error', 'Failed to fetch data from Yahoo Finance.');
            }
    
            $json = $response->json();

            if (!isset($json['chart']['result'][0])) {
                return back()->with('error', 'Stock not found or invalid symbol.');
            }

            $result = $json['chart']['result'][0];

            $timestamps = $result['timestamp'] ?? [];
            $prices = $result['indicators']['quote'][0]['close'] ?? [];
            $cleanPrices = array_values(array_filter($prices)); // Remove nulls

            if (count($cleanPrices) < 3) {
                return back()->with('error', 'Not enough valid price data to analyze.');
            }

            // Send prices to Flask /predict
            try {
                $flask = Http::post('http://127.0.0.1:5000/predict', [
                    'data' => $cleanPrices
                ]);

                $predicted = $flask->json()['predicted_price'] ?? null;
            } catch (\Exception $e) {
                $predicted = null;
            }

            return view('stocks.result', [
                'symbol'     => $symbol,
                'prices'     => $cleanPrices,
                'predicted'  => $predicted,
                'timestamps' => $timestamps
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Unexpected error: ' . $e->getMessage());
        }
    }




    /**
     * ============================
     * SMART AI ANALYSIS VIEW (DARK MODE)
     * ============================
     */
    public function smartForm()
    {
        return view('stocks.smart');
    }

    /**
     * Call Flask AI engine /predict-smart
     * Return advanced prediction + sentiment + risk + news impact
     */
    public function predictSmart(Request $request)
{
    $request->validate([
        'symbol' => 'required|string|max:15'
    ]);    

    $symbol = strtoupper(trim($request->symbol));

    try {
        // Correct Flask port + endpoint
        $response = Http::timeout(12)->post('http://127.0.0.1:5001/predict-smart', [
            'symbol' => $symbol
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error'  => 'ML service error',
                'status' => $response->status(),
                'body'   => $response->body()
            ], 500);
        }

        $data = $response->json();

        // Normalize output for frontend
        return response()->json([
            'symbol'          => $data['symbol'] ?? $symbol,
            'predicted_price' => $data['predicted_price'] ?? null,
            'last_price'      => $data['last_price'] ?? null,
            'avg_sentiment'   => $data['avg_sentiment'] ?? 0,
            'sentiment'       => $data['sentiment'] ?? 'Neutral',
            'risk_score'      => $data['risk_score'] ?? 'Unknown',
            'news_count'      => $data['news_count'] ?? 0,
            'analysis'        => $data['analysis'] ?? 'No analysis available.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error'   => 'Connection to ML server failed',
            'message' => $e->getMessage()
        ], 500);
    }
}
}