<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    /**
     * Columns allowed for server-side sorting.
     */
    protected array $sortable = [
        'id', 'name', 'email', 'role_id', 'city', 'created_at',
    ];

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $sort = in_array($request->query('sort'), $this->sortable, true)
            ? $request->query('sort')
            : 'id';

        $query->orderBy($sort, $direction);

        // Summary (based on the filtered query)
        $summary = (clone $query)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN role_id = (SELECT id FROM roles WHERE name = 'admin') THEN 1 ELSE 0 END) as admin_count")
            ->selectRaw("SUM(CASE WHEN role_id = (SELECT id FROM roles WHERE name = 'entreprise') THEN 1 ELSE 0 END) as company_count")
            ->selectRaw("SUM(CASE WHEN role_id = (SELECT id FROM roles WHERE name = 'citoyen') THEN 1 ELSE 0 END) as citizen_count")
            ->first();

        $roles = Role::query()->orderBy('name')->get(['id', 'name']);

        $perPage = (int) $request->query('per_page', 12);
        if (! in_array($perPage, [5, 12, 25, 50, 100], true)) {
            $perPage = 12;
        }

        $users = $query->paginate($perPage)->withQueryString();

        return view('back.users.index', compact('users', 'summary', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);

        return view('back.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($validated);

        return redirect()->route('back.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with('role')->findOrFail($id);

        return view('back.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::query()->orderBy('name')->get(['id', 'name']);

        return view('back.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = null;
        }

        $user->update($validated);

        return redirect()->route('back.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->route('back.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('back.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Build the filtered query based on request parameters.
     */
    protected function filteredQuery(Request $request)
    {
        $query = User::query()->with('role');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($role = $request->query('role')) {
            $query->where('role_id', $role);
        }

        return $query;
    }
}
