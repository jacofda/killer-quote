<?php

use Illuminate\Database\Seeder;

class KillerQuotePermissionsSeeder extends Seeder
{

    const PERMISSIONS = [
        'killerquotes.*',
        'killerquotes.read',
        'killerquotes.write',
        'killerquotes.delete',
        'killerquotes.configure'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(self::PERMISSIONS as $permission) {
            \Spatie\Permission\Models\Permission::create([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}
