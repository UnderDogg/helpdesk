<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * List of seeders.
     *
     * @var array
     */
    protected $seeders = [
        LabelSeeder::class,
        ComputerTypeSeeder::class,
        InquiryCategorySeeder::class,
        PermissionSeeder::class,
        ApplyPermissionsSeeder::class,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        foreach ($this->seeders as $seeder) {
            $this->call($seeder);
        }

        Model::reguard();
    }
}
