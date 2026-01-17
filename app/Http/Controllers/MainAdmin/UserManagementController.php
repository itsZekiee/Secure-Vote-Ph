<?php

namespace App\Http\Controllers\MainAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'manager'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main-admin.users', compact('users'));
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);
        return back()->with('success', "User {$user->name} has been approved.");
    }

    public function reject(User $user)
    {
        // For rejection, we could either delete or just keep it unapproved.
        // Let's just keep it unapproved for now, or maybe delete if it was a mistake.
        // Actually, the requirement just says "must be verified/approved".
        $user->update(['is_approved' => false]);
        return back()->with('info', "User {$user->name} approval has been revoked.");
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', "User {$user->name} has been deleted.");
    }
}
