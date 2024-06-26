<?php

namespace Modules\CMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\CMS\Database\Seeders\AdminMenusTableSeeder;
use Modules\CMS\Database\Seeders\versions\v1_2_0\ThemeOptionsTableSeeder as ThemeOptionsV12TableSeeder;
use Modules\CMS\Database\Seeders\versions\v1_6_0\DatabaseSeeder as DatabaseSeederV16;

class CMSDatabaseWithoutDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(PageTableWithoutDummyDataSeeder::class);
        $this->call(AdminMenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
        $this->call(ThemeOptionsTableSeeder::class);

        $this->call(ThemeOptionsV12TableSeeder::class);

        $this->call(DatabaseSeederV16::class);
    }
}
