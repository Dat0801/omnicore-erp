<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->input('role') !== 'all') {
            $query->role($request->input('role'));
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status') === 'active';
            $query->where('is_active', $status);
        }

        $users = $query->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at ? $user->last_login_at->format('M d, Y · H:i A') : 'Never',
                'avatar_url' => 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF',
            ]);

        return Inertia::render('User/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
            'roles' => Role::query()->orderBy('name')->get()->map(fn ($r) => [
                'value' => $r->name,
                'label' => ucfirst($r->name),
            ]),
        ]);
    }
}
