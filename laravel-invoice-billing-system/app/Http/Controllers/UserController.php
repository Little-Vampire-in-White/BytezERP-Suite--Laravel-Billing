<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('logo')) {
            if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
                Storage::disk('public')->delete($user->logo_path);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'logo_path' => $validated['logo_path'] ?? $user->logo_path,
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    public function deleteLogo()
    {
        $user = Auth::user();

        if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
            Storage::disk('public')->delete($user->logo_path);
        }

        $user->update(['logo_path' => null]);

        return redirect()->route('profile.edit')
            ->with('success', 'Logo deleted successfully!');
    }
}