<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Blog\Database\Seeders\AdminMenusTableSeeder;

class SubscriptionDatabaseWithoutDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            AdminMenusTableSeeder::class,
            MenuItemsTableSeeder::class
        ]);
    }
}
