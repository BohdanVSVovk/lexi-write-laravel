<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseWithoutDummyDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CurrenciesTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(EmailTemplatesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PreferencesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableWithoutDummyDataSeeder::class);

        $this->call(RoleUsersTableWithoutDummyDataSeeder::class);
        $this->call(PermissionRolesTableSeeder::class);
        $this->call(FilesTableWithoutDummyDataSeeder::class);
        $this->call(ObjectFilesTableWithoutDummyDataSeeder::class);
    }
}
