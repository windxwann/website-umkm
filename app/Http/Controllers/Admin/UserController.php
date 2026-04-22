<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->latest()->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form for creating new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,cashier',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('password', 'photo');
        $data['password'] = Hash::make($request->password);
        $data['is_active'] = true; // Set default active

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $data['photo'] = $path;
        }

        User::create($data);

        Log::info('New user created: ' . $request->email . ' by admin: ' . Auth::user()->email);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Show form for editing user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,cashier',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('users', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        Log::info('User updated: ' . $user->email . ' by admin: ' . Auth::user()->email);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        
        $user->delete();

        Log::info('User deleted: ' . $user->email . ' by admin: ' . Auth::user()->email);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        try {
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menonaktifkan akun sendiri'
                ], 400);
            }

            $user->update([
                'is_active' => !$user->is_active
            ]);

            Log::info('User status toggled: ' . $user->email . ' -> ' . ($user->is_active ? 'Active' : 'Inactive') . ' by admin: ' . Auth::user()->email);

            return response()->json([
                'success' => true,
                'is_active' => $user->is_active,
                'message' => 'Status user berhasil diubah menjadi ' . ($user->is_active ? 'Aktif' : 'Nonaktif')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        try {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            Log::info('Password reset for user: ' . $user->email . ' by admin: ' . Auth::user()->email);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}