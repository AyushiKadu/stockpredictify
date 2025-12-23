<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class HoldingController extends Controller
{
    /**
     * Display all holdings for a given portfolio.
     */
    public function index(Portfolio $portfolio)
    {
        // Fetch all holdings belonging to this portfolio
        $holdings = $portfolio->holdings()->get();
    
        return view('holdings.index', compact('portfolio', 'holdings'));
    }

    /**
     * Show the form to create a new holding.
     */
    public function create(Portfolio $portfolio)
    {
        return view('holdings.create', compact('portfolio'));
    }
     
    /**
     * Store a new holding in the database.
     */
    public function store(Request $request, Portfolio $portfolio)
    {
        $request->validate([
            'symbol' => 'required|string|max:10',
            'quantity' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sector' => 'nullable|string|max:50',
            'current_price' => 'nullable|numeric|min:0', 
        ]); 

        Holding::create([
            'portfolio_id' => $portfolio->id,
            'symbol' => strtoupper($request->symbol),
            'quantity' => $request->quantity,
            'buy_price' => $request->buy_price,
            'current_price' => $request->current_price ?? null,
            'sector' => $request->sector,
        ]);

        return redirect()->route('holdings.index', $portfolio->id)
                         ->with('success', 'Holding added successfully!');
    }

    /**
     * Show the form for editing a holding.
     */
    public function edit(Holding $holding)
    {
        $portfolio = $holding->portfolio;
        return view('holdings.edit', compact('holding', 'portfolio'));
    }

    /**
     * Update a holding in storage.
     */
    public function update(Request $request, Holding $holding)
    {
        $request->validate([
            'symbol' => 'required|string|max:10',
            'quantity' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'sector' => 'nullable|string|max:50',
            'current_price' => 'nullable|numeric|min:0',
        ]);

        $holding->update([
            'symbol' => strtoupper($request->symbol),
            'quantity' => $request->quantity,
            'buy_price' => $request->buy_price,
            'sector' => $request->sector,
            'current_price' => $request->current_price,
        ]);

        return redirect()->route('holdings.index', $holding->portfolio_id)
                         ->with('success', 'Holding updated successfully!');
    }

    /**
     * Remove a holding.
     */
    public function destroy(Holding $holding)
    {
        $portfolioId = $holding->portfolio_id;
        $holding->delete();

        return redirect()->route('holdings.index', $portfolioId)
                         ->with('success', 'Holding deleted successfully!');
    }
}
