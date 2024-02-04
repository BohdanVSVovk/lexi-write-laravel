<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(EmailConfigurationsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(EmailTemplatesTableSeeder::class);

        $this->call(PermissionsTableSeeder::class);
        $this->call(PreferencesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RoleUsersTableSeeder::class);

        $this->call(PermissionRolesTableSeeder::class);
        $this->call(AddressesTableSeeder::class);
        $this->call(FilesTableSeeder::class);
        $this->call(ObjectFilesTableSeeder::class);

        $this->call(UsersMetaTableSeeder::class);
    }
}
