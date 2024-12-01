<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Cache::get('site_settings', [
            'store_name' => config('app.name'),
            'store_address' => '',
            'store_phone' => '',
            'store_email' => '',
            'delivery_radius' => 5,
            'minimum_order' => 10,
            'delivery_fee' => 2.50,
            'tax_rate' => 20,
            'opening_hours' => [
                'monday' => ['09:00', '22:00'],
                'tuesday' => ['09:00', '22:00'],
                'wednesday' => ['09:00', '22:00'],
                'thursday' => ['09:00', '22:00'],
                'friday' => ['09:00', '23:00'],
                'saturday' => ['10:00', '23:00'],
                'sunday' => ['10:00', '22:00'],
            ],
        ]);

        $discountCodes = DiscountCode::latest()->get();

        return view('admin.settings.index', compact('settings', 'discountCodes'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'store_phone' => 'required|string|max:20',
            'store_email' => 'required|email',
            'delivery_radius' => 'required|numeric|min:0',
            'minimum_order' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'opening_hours' => 'required|array',
        ]);

        Cache::forever('site_settings', $validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully');
    }

    public function storeDiscountCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:discount_codes,code|max:50',
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

        DiscountCode::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Discount code created successfully');
    }

    public function updateDiscountCode(Request $request, DiscountCode $discountCode)
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

        return redirect()->route('admin.settings.index')
            ->with('success', 'Discount code updated successfully');
    }

    public function destroyDiscountCode(DiscountCode $discountCode)
    {
        $discountCode->delete();
        return redirect()->route('admin.settings.index')
            ->with('success', 'Discount code deleted successfully');
    }
} 