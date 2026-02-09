<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(): Response
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

    public function editPermissions(string $role): Response
    {
        $roleEnum = Role::tryFrom($role);

        if (! $roleEnum) {
            abort(404);
        }

        return Inertia::render('Role/Permissions', [
            'role' => [
                'value' => $roleEnum->value,
                'label' => $roleEnum->label(),
            ],
        ]);
    }
}
