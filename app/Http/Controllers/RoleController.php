<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Role/Index', [
            'roles' => collect(Role::cases())->map(fn ($role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ]),
            'stats' => [
                'admins' => User::where('role', Role::ADMIN)->count(),
                'managers' => User::where('role', Role::MANAGER)->count(),
                'staff' => User::where('role', Role::STAFF)->count(),
            ],
        ]);
    }
}
