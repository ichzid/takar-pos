<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Form', [
            'user' => null,
            'roleOptions' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_CASHIER])],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => $validated['password'],
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Form', [
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'roleOptions' => $this->roleOptions(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_CASHIER])],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if (
            $user->role === User::ROLE_ADMIN
            && $validated['role'] !== User::ROLE_ADMIN
            && User::query()->where('role', User::ROLE_ADMIN)->count() <= 1
        ) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Minimal harus ada satu admin aktif di sistem.');
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()?->id === $user->id) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Akun yang sedang digunakan tidak dapat dihapus dari menu ini.');
        }

        if ($user->orders()->exists()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Pengguna tidak dapat dihapus karena sudah memiliki transaksi.');
        }

        if ($user->role === User::ROLE_ADMIN && User::query()->where('role', User::ROLE_ADMIN)->count() <= 1) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Minimal harus ada satu admin aktif di sistem.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * @return array<int, array{value:string,label:string}>
     */
    private function roleOptions(): array
    {
        return [
            ['value' => User::ROLE_ADMIN, 'label' => 'Admin'],
            ['value' => User::ROLE_CASHIER, 'label' => 'Kasir'],
        ];
    }
}
