<?php
// database/seeders/RolePermissionSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $franchise = Role::firstOrCreate(['name' => 'franchise']);

        // Create permissions
        $permissions = [
            'manage_franchises',
            'manage_students',
            'manage_courses',
            'manage_exams',
            'manage_certificates',
            'view_reports',
            'manage_payments'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to super admin
        $superAdmin->givePermissionTo($permissions);

        // Assign limited permissions to franchise
        $franchise->givePermissionTo([
            'manage_students',
            'manage_exams',
            'view_reports'
        ]);

        // Get your current user (the one you just registered)
        // Replace this email with the one you used to register
        $user = User::where('email', 'admin@example.com')->first();

        // If the above user doesn't exist, get the first user
        if (!$user) {
            $user = User::first();
        }

        if ($user) {
            $user->assignRole($superAdmin);
            $this->command->info("Super Admin role assigned to: {$user->email}");
        }
    }
}
