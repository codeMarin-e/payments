<?php
namespace Database\Seeders\Packages\Payments;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MarinarPaymentsSeeder extends Seeder {

    public function run() {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::upsert([
            ['guard_name' => 'admin', 'name' => 'payments.view'],
            ['guard_name' => 'admin', 'name' => 'payment.system'],
            ['guard_name' => 'admin', 'name' => 'payment.create'],
            ['guard_name' => 'admin', 'name' => 'payment.update'],
            ['guard_name' => 'admin', 'name' => 'payment.delete'],
        ], ['guard_name','name']);
    }
}
