<?php
use App\Lib\Env;
use Illuminate\Support\Facades\Artisan;

function beforeUpgrade(){
    echo "beforeUpgrade<br>";
    Env::set('APP_ENV', 'local');


    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    sleep(5);
}



function afterUpgrade(){
    echo "afterUpgrade<br>";
    Env::set('APP_ENV', 'production');
}

