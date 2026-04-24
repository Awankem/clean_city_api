<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of staff and teams.
     */
    public function index()
    {
        // For the UI, we'll list all users with non-citizen roles or just all users for management
        $staff = User::whereIn('role', ['admin', 'staff', 'collector']) // Assuming these roles exist or will be used
            ->orderBy('name')
            ->paginate(15);
        
        $stats = [
            'total_staff' => User::where('role', '!=', 'citizen')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'teams_active' => 4, // Placeholder for team aggregation
        ];

        return view('admin.staff', compact('staff', 'stats'));
    }

    /**
     * Update user role or status.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:admin,citizen,staff,collector',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->back()->with('success', "Role for {$user->name} updated successfully.");
    }
}
