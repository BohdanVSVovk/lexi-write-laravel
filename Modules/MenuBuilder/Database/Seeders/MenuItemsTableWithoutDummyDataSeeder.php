<?php

namespace Modules\MenuBuilder\Database\Seeders;

use Illuminate\Database\Seeder;

class MenuItemsTableWithoutDummyDataSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('menu_items')->delete();

        \DB::table('menu_items')->insert(array (
            0 =>
            array (
                'id' => 1,
                'label' => 'Dashboard',
                'link' => 'dashboard',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\DashboardController@index","route_name":["dashboard"]}',
                'is_default' => 1,
                'icon' => 'fas fa-home',
                'parent' => 0,
                'sort' => 0,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            1 =>
            array (
                'id' => 2,
                'label' => 'Add User',
                'link' => 'user/create',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\UserController@create","route_name":["users.create"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 27,
                'sort' => 2,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            6 =>
            array (
                'id' => 8,
                'label' => 'All Users',
                'link' => 'user/list',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\UserController@index","route_name":["users.index","users.edit","users.pdf","users.csv","users.verify"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 27,
                'sort' => 3,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            12 =>
            array (
                'id' => 14,
                'label' => 'addons',
                'link' => 'addons',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\AddonsMangerController@index","route_name":["addon.index","addon.switch-status","addon.remove","addon.upload"]}',
                'is_default' => 1,
                'icon' => 'fas fa-chess-rook',
                'parent' => 0,
                'sort' => 58,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            13 =>
            array (
                'id' => 16,
                'label' => 'Menus',
                'link' => 'menu-builder',
                'params' => '{"permission":"Modules\\\\MenuBuilder\\\\Http\\\\Controllers\\\\MenuBuilderController@index","route_name":["menu.index"]}',
                'is_default' => 1,
                'icon' => 'fas fa-bars',
                'parent' => 0,
                'sort' => 33,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            14 =>
            array (
                'id' => 19,
                'label' => 'General Settings',
                'link' => 'general-setting',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\CompanySettingController@index","route_name":["preferences.index", "companyDetails.setting", "maintenance.enable", "language.translation", "language.index", "currency.convert", "withdrawalSetting.index"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 31,
                'sort' => 49,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            17 =>
            array (
                'id' => 26,
                'label' => 'Cache Clear',
                'link' => 'clear-cache',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\DashboardController@index","route_name":["clear-cache"]}',
                'is_default' => 1,
                'icon' => 'fas fa-trash-alt',
                'parent' => 0,
                'sort' => 59,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            18 =>
            array (
                'id' => 27,
                'label' => 'Personnel',
                'link' => NULL,
                'params' => NULL,
                'is_default' => 0,
                'icon' => 'fas fa-users',
                'parent' => 0,
                'sort' => 1,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            21 =>
            array (
                'id' => 31,
                'label' => 'Configurations',
                'link' => NULL,
                'params' => NULL,
                'is_default' => 0,
                'icon' => 'fas fa-cog',
                'parent' => 0,
                'sort' => 48,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            36 =>
            array (
                'id' => 49,
                'label' => 'Geo Locale',
                'link' => 'geolocale',
                'params' => '{"permission":"Modules\\\\GeoLocale\\\\Http\\\\Controllers\\\\GeoLocaleController@index", "route_name":["geolocale.index"], "menu_lavel":"1"}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 31,
                'sort' => 58,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            40 =>
            array (
                'id' => 54,
                'label' => 'Categories',
                'link' => 'blog/category/list',
                'params' => '{"permission":"Modules\\\\Blog\\\\Http\\\\Controllers\\\\BlogCategoryController@index", "route_name":["blog.category.index"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 56,
                'sort' => 25,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            41 =>
            array (
                'id' => 55,
                'label' => 'Add Post',
                'link' => 'blog/create',
                'params' => '{"permission":"Modules\\\\Blog\\\\Http\\\\Controllers\\\\BlogController@create", "route_name":["blog.create"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 56,
                'sort' => 23,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            42 =>
            array (
                'id' => 56,
                'label' => 'Blogs',
                'link' => NULL,
                'params' => NULL,
                'is_default' => 1,
                'icon' => 'fab fa-blogger-b',
                'parent' => 0,
                'sort' => 22,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            43 =>
            array (
                'id' => 57,
                'label' => 'Website Setup',
                'link' => NULL,
                'params' => NULL,
                'is_default' => 1,
                'icon' => 'fas fa-box',
                'parent' => 0,
                'sort' => 38,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            45 =>
            array (
                'id' => 59,
                'label' => 'All Posts',
                'link' => 'blogs',
                'params' => '{"permission":"Modules\\\\Blog\\\\Http\\\\Controllers\\\\BlogController@index", "route_name":["blog.index", "blog.edit"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 56,
                'sort' => 24,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            46 =>
            array (
                'id' => 60,
                'label' => 'Pages',
                'link' => 'page/list',
                'params' => '{"permission":"Modules\\\\CMS\\\\Http\\\\Controllers\\\\CMSController@index", "route_name":["page.index", "page.create", "page.edit"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 57,
                'sort' => 41,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            47 =>
            array (
                'id' => 61,
                'label' => 'Media Manager',
                'link' => 'uploaded-files',
                'params' => '{"permission":"Modules\\\\MediaManager\\\\Http\\\\Controllers\\\\MediaManagerController@uploadedFiles", "route_name":["mediaManager.create", "mediaManager.upload", "mediaManager.uploadedFiles", "mediaManager.sortFiles", "mediaManager.paginateFiles", "mediaManager.download", "mediaManager.maxId"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => 'fas fa-folder-open',
                'parent' => 0,
                'sort' => 43,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            49 =>
            array (
                'id' => 63,
                'label' => 'Reports',
                'link' => 'reports',
                'params' => '{"permission":"Modules\\\\Report\\\\Http\\\\Controllers\\\\ReportController@index", "route_name":["reports"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => 'fas fa-chart-bar',
                'parent' => 0,
                'sort' => 57,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            58 =>
            array (
                'id' => 73,
                'label' => 'Marketing',
                'link' => NULL,
                'params' => NULL,
                'is_default' => 0,
                'icon' => 'fas fa-bullhorn',
                'parent' => 0,
                'sort' => 26,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            68 =>
            array (
                'id' => 92,
                'label' => 'Appearance',
                'link' => 'theme/list',
                'params' => '{"permission":"Modules\\\\CMS\\\\Http\\\\Controllers\\\\ThemeOptionController@list", "route_name":["theme.index", "theme.store"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 57,
                'sort' => 42,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            71 =>
            array (
                'id' => 100,
                'label' => 'Accounts',
                'link' => 'account-setting',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\AccountSettingController@index","route_name":["account.setting.option", "sso.index", "emailVerifySetting", "preferences.password", "permissionRoles.index", "roles.index", "roles.create", "roles.edit"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 31,
                'sort' => 54,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            72 =>
            array (
                'id' => 101,
                'label' => 'Emails',
                'link' => 'email-setting',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\EmailConfigurationController@index","route_name":["emailConfigurations.index", "emailTemplates.index", "emailTemplates.create", "emailTemplates.edit"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 31,
                'sort' => 55,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            79 =>
            array (
                'id' => 112,
                'label' => 'Login Activities',
                'link' => 'user/activity',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\UserController@index","route_name":["users.activity"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 27,
                'sort' => 5,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            80 =>
            array (
                'id' => 114,
                'label' => 'Use Cases',
                'link' => 'use-cases',
                'params' => '{"permission":"no-prefix"}',
                'is_default' => 0,
                'icon' => NULL,
                'parent' => 0,
                'sort' => 0,
                'class' => NULL,
                'menu' => 4,
                'depth' => 0,
                'is_custom_menu' => 1,
            ),
            87 =>
            array (
                'id' => 121,
                'label' => 'Digital Art',
                'link' => '#digital_art',
                'params' => '{"permission":"no-prefix"}',
                'is_default' => 0,
                'icon' => NULL,
                'parent' => 0,
                'sort' => 12,
                'class' => NULL,
                'menu' => 4,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            88 =>
            array (
                'id' => 122,
                'label' => 'Pricing',
                'link' => 'pricing',
                'params' => '{"permission":"no-prefix"}',
                'is_default' => 0,
                'icon' => NULL,
                'parent' => 0,
                'sort' => 13,
                'class' => NULL,
                'menu' => 4,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            89 =>
            array (
                'id' => 123,
                'label' => 'News',
                'link' => 'blogs',
                'params' => '{"permission":"no-prefix"}',
                'is_default' => 0,
                'icon' => NULL,
                'parent' => 0,
                'sort' => 14,
                'class' => NULL,
                'menu' => 4,
                'depth' => 0,
                'is_custom_menu' => 0,
            ),
            99 =>
            array(
                'id' => 133,
                'label' => 'System Info',
                'link' => 'system-info',
                'params' => '{"permission":"App\\\\Http\\\\Controllers\\\\SystemInfoController@index", "route_name":["systemInfo.index"], "menu_level":"2"}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 31,
                'sort' => 59,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0,
            ),
            100 =>
            array(
                'id' => 160,
                'label' => 'FAQ',
                'link' => 'faq',
                'params' => '{"permission":"Modules\\\\FAQ\\\\Http\\\\Controllers\\\\FAQController@index", "route_name":["admin.faq", "admin.faq.create", "admin.faq.edit"], "menu_level":"1"}',
                'is_default' => 1,
                'icon' => 'fas fa fa-comments',
                'parent' => 0,
                'sort' => 23,
                'class' => NULL,
                'menu' => 1,
                'depth' => 0,
                'is_custom_menu' => 0
            )
        ));

    }
}
