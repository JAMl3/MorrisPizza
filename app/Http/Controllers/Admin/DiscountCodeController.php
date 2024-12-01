<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::latest()->get();
        return view('admin.discount-codes.index', compact('discountCodes'));
    }

    public function create()
    {
        return view('admin.discount-codes.create');
    }

    public function store(Request $request)
    {
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->url());
        \Log::info('Form data:', $request->all());
        
        $validated = $request->validate([
            'code' => 'required|string|unique:discount_codes,code|max:50',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean'
        ]);

        \Log::info('Validated data:', $validated);

        try {
            $discountCode = DiscountCode::create($validated);
            \Log::info('Created discount code:', $discountCode->toArray());
            return redirect()->route('admin.discount-codes.index')
                ->with('success', 'Discount code created successfully');
        } catch (\Exception $e) {
            \Log::error('Error creating discount code: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create discount code: ' . $e->getMessage()]);
        }
    }

    public function edit(DiscountCode $discountCode)
    {
        return view('admin.discount-codes.edit', compact('discountCode'));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code,' . $discountCode->id,
            'description' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean'
        ]);

        if ($request->filled('starts_at')) {
            $validated['starts_at'] = Carbon::parse($request->starts_at);
        }
        
        if ($request->filled('expires_at')) {
            $validated['expires_at'] = Carbon::parse($request->expires_at);
        }

        $discountCode->update($validated);

        return redirect()->route('admin.discount-codes.index')
            ->with('success', 'Discount code updated successfully');
    }

    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();
        return redirect()->route('admin.discount-codes.index')
            ->with('success', 'Discount code deleted successfully');
    }
} 