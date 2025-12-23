<?php

namespace App\Http\Controllers;

use App\Jobs\UpdatePortfolioPricesJob;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class PortfolioController extends Controller
{
    /**
     * List portfolios
     */
    public function index()
    {
        $portfolios = Portfolio::latest()->get();
        return view('portfolios.index', compact('portfolios'));
    }

    /**
     * Create form
     */
    public function create()
    {
        return view('portfolios.create');
    }

    /**
     * Store new portfolio
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Portfolio::create([
            'name' => $request->name,
            'risk_score' => null,
        ]);

        return redirect()->route('portfolios.index')
            ->with('success', 'Portfolio created successfully!');
    }

    /**
     * SHOW ANALYTICS PAGE
     */
    public function show(Portfolio $portfolio)
    {
        $holdings = $portfolio->holdings;

        $totalInvestment = 0;
        $totalCurrentValue = 0;

        foreach ($holdings as $h) {

            $symbol = strtoupper($h->symbol) . ".NS";
            $live = $h->current_price;

            // fetch live price
            try {
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1d";
                $res = Http::withoutVerifying()->get($url)->json();
                $live = $res['chart']['result'][0]['meta']['regularMarketPrice'] ?? $live;
            } catch (\Exception $e) {
            }

            $h->live_price = $live;

            $investment = $h->buy_price * $h->quantity;
            $current    = $live * $h->quantity;

            $totalInvestment  += $investment;
            $totalCurrentValue += $current;
        }

        $profitLoss = $totalCurrentValue - $totalInvestment;
        $profitLossPercent = $totalInvestment > 0
            ? ($profitLoss / $totalInvestment) * 100
            : 0;

        // Chart data
        $chartLabels = $holdings->pluck('symbol');
        $chartData   = $holdings->map(fn ($h) => $h->live_price * $h->quantity);

        return view('portfolios.show', compact(
            'portfolio',
            'holdings',
            'totalInvestment',
            'totalCurrentValue',
            'profitLoss',
            'profitLossPercent',
            'chartLabels',
            'chartData'
        ));
    }

    /**
     * AJAX – return latest prices + summary
     */
    public function prices(Portfolio $portfolio)
    {
        $holdings = $portfolio->holdings;

        $rows = [];
        $totalInvestment = 0;
        $totalCurrentValue = 0;

        foreach ($holdings as $h) {

            $symbol = strtoupper($h->symbol) . ".NS";
            $live = $h->current_price;

            try {
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1d";
                $res = Http::withoutVerifying()->get($url)->json();
                $live = $res['chart']['result'][0]['meta']['regularMarketPrice'] ?? $live;
            } catch (\Exception $e) {
            }

            $h->live_price = $live;

            $investment = $h->buy_price * $h->quantity;
            $currentVal = $live * $h->quantity;

            $gainLoss = $currentVal - $investment;
            $gainLossPercent = $investment > 0 ? ($gainLoss / $investment) * 100 : 0;

            $rows[] = [
                'symbol'            => strtoupper($h->symbol),
                'quantity'          => $h->quantity,
                'buy_price'         => $h->buy_price,
                'live_price'        => $live,
                'sector'            => $h->sector,
                'gainLoss'          => round($gainLoss, 2),
                'gainLossPercent'   => round($gainLossPercent, 2),
            ];

            $totalInvestment  += $investment;
            $totalCurrentValue += $currentVal;
        }

        $profitLoss = $totalCurrentValue - $totalInvestment;
        $profitLossPercent = $totalInvestment > 0
            ? ($profitLoss / $totalInvestment) * 100
            : 0;

        return response()->json([
            'rows' => $rows,
            'summary' => [
                'totalInvestment'    => round($totalInvestment, 2),
                'totalCurrentValue'  => round($totalCurrentValue, 2),
                'profitLoss'         => round($profitLoss, 2),
                'profitLossPercent'  => round($profitLossPercent, 2),
            ],
        ]);
    }

    /**
     * DataTable JSON
     */
    public function holdingsJson(Portfolio $portfolio)
    {
        $data = $portfolio->holdings->map(function ($h) {

            $investment = $h->buy_price * $h->quantity;
            $current    = $h->current_price * $h->quantity;
            $gainLoss   = $current - $investment;
            $gainPct    = $investment > 0 ? ($gainLoss / $investment) * 100 : 0;

            return [
                'symbol'        => strtoupper($h->symbol),
                'quantity'      => $h->quantity,
                'buy_price'     => $h->buy_price,
                'current_price' => $h->current_price,
                'sector'        => $h->sector,
                'gain_loss'     => $gainLoss,
                'gain_percent'  => $gainPct,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Export PDF
     */
    public function exportPdf(Portfolio $portfolio)
    {
        $holdings = $portfolio->holdings;

        $totalInvestment = 0;
        $totalCurrentValue = 0;

        foreach ($holdings as $h) {
            $investment = $h->buy_price * $h->quantity;
            $current    = $h->current_price * $h->quantity;

            $totalInvestment  += $investment;
            $totalCurrentValue += $current;
        }

        $profitLoss = $totalCurrentValue - $totalInvestment;
        $profitLossPercent = $totalInvestment > 0
            ? ($profitLoss / $totalInvestment) * 100
            : 0;

        $pdf = Pdf::loadView('portfolios.pdf', compact(
            'portfolio',
            'holdings',
            'totalInvestment',
            'totalCurrentValue',
            'profitLoss',
            'profitLossPercent'
        ));

        return $pdf->download($portfolio->name . '_report.pdf');
    }

    /**
     * Delete portfolio
     */
    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();  

        return redirect()->route('portfolios.index')
            ->with('success', 'Portfolio deleted!');
    }
}
