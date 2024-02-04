<?php
use App\Lib\Env;
use Illuminate\Support\Facades\Artisan;

function beforeUpgrade(){
    echo "Please be patient; it might take up to 10 minutes or more to upgrade your system.<br>";
    Env::set('APP_ENV', 'local');
    set_time_limit(0);

    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    sleep(5);
}

function afterUpgrade(){    
    Env::set('APP_ENV', 'production');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('optimize:clear');
}

