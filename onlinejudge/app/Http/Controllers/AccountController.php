<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Display a listing of users with search functionality
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Combined search for username and email
        if ($request->filled('search')) {
            $search = $request->input('search');
            if (filter_var($search, FILTER_VALIDATE_EMAIL)) {
                $query->where('email', 'like', '%' . $search . '%');
            } else {
                $query->where('username', 'like', '%' . $search . '%');
            }
        }

        // Filter by admin status
        if ($request->filled('is_admin')) {
            $query->where('is_admin', $request->is_admin);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $users = $query->paginate(10);

        return view('manage.accounts.index', compact('users'));
    }

    /**
     * Display user details with tabs
     */
    public function details($id)
    {
        $user = User::findOrFail($id);
        return view('manage.accounts.details', compact('user'));
    }

    /**
     * Display user information tab
     */
    public function information($id)
    {
        $user = User::findOrFail($id);
        return view('manage.accounts.information', compact('user'));
    }

    /**
     * Display password change tab
     */
    public function password($id)
    {
        $user = User::findOrFail($id);
        return view('manage.accounts.password', compact('user'));
    }

    /**
     * Display settings tab
     */
    public function settings($id)
    {
        $user = User::findOrFail($id);
        return view('manage.accounts.settings', compact('user'));
    }

    /**
     * Update user information
     */
    public function updateInformation(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:Users,username,' . $id,
            'email' => 'required|email|max:255|unique:Users,email,' . $id,
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update($request->only(['username', 'email', 'bio', 'avatar']));

        return redirect()->back()
            ->with('success', 'User information updated successfully');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()
            ->with('success', 'Password changed successfully');
    }

    /**
     * Toggle admin status
     */
    public function setAdmin(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_admin' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->back()
            ->with('success', 'Admin status updated successfully');
    }

    /**
     * Update user bio
     */
    public function updateBio(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update($request->only(['bio', 'location', 'website', 'social_links']));

        return redirect()->back()
            ->with('success', 'Profile information updated successfully');
    }

    /**
     * Create a new user account
     */
    public function create(Request $request)
    {
        // If it's a GET request, just show the form
        if ($request->isMethod('get')) {
            return view('manage.accounts.create');
        }

        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255|unique:Users,username',
                'email' => 'required|email|max:255|unique:Users,email',
                'password' => 'required|string|min:8|confirmed',
                'is_admin' => 'boolean',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_admin' => $request->boolean('is_admin', false),
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('manage.accounts.index')
                ->with('success', 'User account created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
