<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected function ensureBaseRoles(): void
    {
        foreach (['admin', 'manager', 'staff', 'supplier'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    public function index(): Response
    {
        $this->ensureBaseRoles();
        $roles = Role::query()->orderBy('name')->get()->map(fn ($r) => [
            'value' => $r->name,
            'label' => ucfirst($r->name),
        ]);

        return Inertia::render('Role/Index', [
            'roles' => $roles,
            'stats' => [
                'admins' => User::role('admin')->count(),
                'managers' => User::role('manager')->count(),
                'staff' => User::role('staff')->count(),
                'suppliers' => User::role('supplier')->count(),
            ],
        ]);
    }

    public function editPermissions(string $role): Response
    {
        $this->ensureBaseRoles();
        $roleModel = Role::where('name', $role)->firstOrFail();

        return Inertia::render('Role/Permissions', [
            'role' => [
                'value' => $roleModel->name,
                'label' => ucfirst($roleModel->name),
            ],
        ]);
    }
}
