<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permission categories
        $permissionCategories = [
            'school' => [
                'view', 'edit', 'create', 'delete',
                'manage_settings', 'view_analytics'
            ],
            'user' => [
                'view', 'edit', 'create', 'delete',
                'change_role', 'reset_password'
            ],
            'class' => [
                'view', 'edit', 'create', 'delete',
                'assign_teacher', 'assign_student'
            ],
            'course' => [
                'view', 'edit', 'create', 'delete',
                'assign_to_class'
            ],
            'assignment' => [
                'view', 'edit', 'create', 'delete',
                'grade'
            ],
            'grade' => [
                'view', 'edit', 'create', 'delete',
                'finalize'
            ],
            'attendance' => [
                'view', 'edit', 'create', 'delete',
                'generate_report'
            ],
            'report' => [
                'view', 'generate', 'share'
            ],
            'communication' => [
                'send_message', 'create_announcement',
                'moderate_forum'
            ],
            'finance' => [
                'view', 'create_invoice', 'process_payment',
                'generate_financial_report'
            ],
        ];

        // Create permissions
        foreach ($permissionCategories as $category => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$category}", 'guard_name' => 'sanctum']);
            }
        }

        // Define roles and their permissions
        $roles = [
            'super_admin' => array_merge(...array_values($permissionCategories)),
            'school_admin' => array_merge(
                $permissionCategories['school'],
                $permissionCategories['user'],
                $permissionCategories['class'],
                $permissionCategories['course'],
                $permissionCategories['report'],
                $permissionCategories['communication'],
                $permissionCategories['finance']
            ),
            'teacher' => [
                'view_school',
                'view_user', 'edit_user',
                'view_class', 'edit_class',
                'view_course', 'edit_course',
                'view_assignment', 'edit_assignment', 'create_assignment', 'delete_assignment', 'grade_assignment',
                'view_grade', 'edit_grade', 'create_grade',
                'view_attendance', 'edit_attendance', 'create_attendance',
                'view_report', 'generate_report',
                'send_message', 'create_announcement'
            ],
            'student' => [
                'view_school',
                'view_user',
                'view_class',
                'view_course',
                'view_assignment',
                'view_grade',
                'view_attendance',
                'view_report',
                'send_message'
            ],
            'parent' => [
                'view_school',
                'view_user',
                'view_class',
                'view_course',
                'view_assignment',
                'view_grade',
                'view_attendance',
                'view_report',
                'send_message'
            ],
            'accountant' => array_merge(
                $permissionCategories['finance'],
                ['view_school', 'view_user', 'view_report', 'generate_report']
            ),
        ];

        // Create roles and assign permissions
        foreach ($roles as $role => $permissions) {
            $createdRole = Role::create(['name' => $role, 'guard_name' => 'sanctum']);
            foreach ($permissions as $permission) {

                $permissionInstance = Permission::where('name', $permission)
                    ->where('guard_name', 'sanctum')
                    ->first();

                if ($permissionInstance) {
                    $createdRole->givePermissionTo($permissionInstance);
                }
            }
        }
    }
}