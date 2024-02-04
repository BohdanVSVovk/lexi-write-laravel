<?php

namespace Modules\CMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\CMS\Database\Seeders\AdminMenusTableSeeder;
use Modules\CMS\Database\Seeders\TemplateAndLayoutSeeder;

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
    }
}
