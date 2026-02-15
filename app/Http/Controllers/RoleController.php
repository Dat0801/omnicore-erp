<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
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
        $roles = Role::query()
            ->orderBy('name')
            ->get()
            ->map(fn ($r) => [
                'value' => $r->name,
                'label' => ucfirst($r->name),
            ]);

        $permissionsCount = Permission::query()->count();
        $recentPermissionUpdates = Permission::query()
            ->where('updated_at', '>=', now()->subDay())
            ->count();

        return Inertia::render('Role/Index', [
            'roles' => $roles,
            'stats' => [
                'admins' => User::role('admin')->count(),
                'managers' => User::role('manager')->count(),
                'staff' => User::role('staff')->count(),
                'suppliers' => User::role('supplier')->count(),
                'permissions' => $permissionsCount,
                'recentPermissionUpdates' => $recentPermissionUpdates,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->ensureBaseRoles();

        return Inertia::render('Role/Create');
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('admin.roles.permissions.edit', $role->name)
            ->with('success', 'Role created successfully. You can now configure permissions.');
    }

    public function editPermissions(string $role): Response
    {
        $this->ensureBaseRoles();
        $roleModel = Role::where('name', $role)->firstOrFail();

        $allPermissions = Permission::query()
            ->orderBy('name')
            ->get();

        $rolePermissionIds = $roleModel->permissions->pluck('id')->all();

        $grouped = $allPermissions->groupBy(function (Permission $permission) {
            $parts = explode('.', $permission->name, 2);

            return $parts[0] ?? 'general';
        });

        $actionKeys = [];

        $matrix = $grouped->map(function ($permissions, string $module) use (&$actionKeys, $rolePermissionIds) {
            $rows = [];

            foreach ($permissions as $permission) {
                $parts = explode('.', $permission->name, 2);
                $action = $parts[1] ?? 'access';

                $actionKeys[$action] = true;

                $rows[$action] = [
                    'key' => $action,
                    'id' => $permission->id,
                    'assigned' => in_array($permission->id, $rolePermissionIds, true),
                ];
            }

            return [
                'module' => $module,
                'label' => ucfirst(str_replace(['_', '-'], ' ', $module)),
                'description' => null,
                'permissions' => array_values($rows),
            ];
        })->values();

        $actions = collect(array_keys($actionKeys))
            ->sort()
            ->map(fn (string $key) => [
                'key' => $key,
                'label' => ucfirst(str_replace(['_', '-'], ' ', $key)),
            ])
            ->values();

        return Inertia::render('Role/Permissions', [
            'role' => [
                'value' => $roleModel->name,
                'label' => ucfirst($roleModel->name),
            ],
            'actions' => $actions,
            'matrix' => $matrix,
        ]);
    }

    public function updatePermissions(UpdateRolePermissionsRequest $request, string $role): RedirectResponse
    {
        $this->ensureBaseRoles();
        $roleModel = Role::where('name', $role)->firstOrFail();

        $permissions = $request->validated('permissions') ?? [];

        $roleModel->syncPermissions($permissions);

        return redirect()
            ->route('admin.roles.permissions.edit', $roleModel->name)
            ->with('success', 'Permissions updated successfully.');
    }
}
