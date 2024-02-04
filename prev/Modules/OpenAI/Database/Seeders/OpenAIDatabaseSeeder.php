<?php

namespace Modules\OpenAI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class OpenAIDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UseCaseCategoriesTableSeeder::class);
        $this->call(UseCasesTableSeeder::class);
        $this->call(UseCaseUseCaseCategoryTableSeeder::class);
        $this->call(OptionsTableSeeder::class);
        $this->call(OptionMetaTableSeeder::class);
        $this->call(ContentTypeTableSeeder::class);
        $this->call(ContentTypeMetaTableSeeder::class);

        $this->call(ContentsTableSeeder::class);
        $this->call(CodesTableSeeder::class);

        $this->call(AdminMenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
        $this->call(ChatCategoriesTableSeeder::class);
        $this->call(ChatBotsTableSeeder::class);
    }
}
