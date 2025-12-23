<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function show(Request $request, Portfolio $portfolio)
{
    // 1) Start holdings query
    $holdingsQuery = $portfolio->holdings();

    // ---- FILTER: SECTOR ----
    if ($request->sector) {
        $holdingsQuery->where('sector', $request->sector);
    }

    // ---- FILTER: RISK SCORE ----
    if ($request->risk) {
        $holdingsQuery->where('ai_risk', $request->risk);
    }

    // Load final results
    $holdings = $holdingsQuery->get();

    // ---- Portfolio Calculations ----
    $totalInvestment = $holdings->sum(fn ($h) => $h->buy_price * $h->quantity);
    $totalCurrentValue = $holdings->sum(fn ($h) => $h->current_price * $h->quantity);
    $profitLoss = $totalCurrentValue - $totalInvestment;
    $profitLossPercent = $totalInvestment > 0 ? ($profitLoss / $totalInvestment) * 100 : 0;

    return view('portfolios.show', compact(
        'portfolio',
        'holdings',
        'totalInvestment',
        'totalCurrentValue',
        'profitLoss',
        'profitLossPercent',
        'request'
    ));
}


}
