<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings()
    {
        $profile = auth()->user()->profile ?? new UserProfile();
        return view('profile.settings', compact('profile'));
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'default_name' => 'required|string|max:255',
                'default_email' => 'required|email|max:255',
                'default_phone' => 'required|string|max:20',
                'default_address_line1' => 'required|string|max:255',
                'default_address_line2' => 'nullable|string|max:255',
                'default_city' => 'required|string|max:255',
                'default_postcode' => 'required|string|max:10',
            ]);

            $profile = UserProfile::updateOrCreate(
                ['user_id' => auth()->id()],
                $request->only([
                    'default_name',
                    'default_email',
                    'default_phone',
                    'default_address_line1',
                    'default_address_line2',
                    'default_city',
                    'default_postcode'
                ])
            );

            return redirect()->route('profile.settings')
                ->with('success', 'Profile settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Profile update error', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('profile.settings')
                ->withInput()
                ->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }
} 