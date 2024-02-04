<?php

return [
    'name' => 'OpenAI',

    'url' => 'https://api.openai.com/v1/images/generations',
    'chatUrl' => 'https://api.openai.com/v1/chat/completions',
    'chatModel' => 'gpt-3.5-turbo',
    'chatToken' => 4000,

    'openAIModel' => [
        'gpt-4' => 'Gpt-4',
        'text-davinci-003' => 'Davinci',
        'text-curie-001' =>  'Curie',
        'gpt-3.5-turbo' =>  'Gpt-3.5-turbo',
        'gpt-3.5-turbo-16k' =>  'gpt-3.5-turbo-16k',
        'text-babbage-001' =>  'Babbage',
        'text-ada-001' =>  'Ada',
    ],

    'modelDescription' => [
        'gpt-4' => 'More capable than any GPT-3.5 model, able to do more complex tasks',
        'text-davinci-003' => 'Most capable GPT-3 model. Can do any task with higher quality',
        'text-curie-001' =>  'Very capable, but faster and lower cost than Davinci',
        'gpt-3.5-turbo' =>  'Optimized for chat at 1/10th the cost of text-davinci-003',
        'gpt-3.5-turbo-16k' =>  'Same capabilities as gpt-3.5-turbo model but with 4 times the context',
        'text-babbage-001' =>  'Capable of straightforward tasks',
        'text-ada-001' =>  'Cheapest & Fastest',
    ],

    'size' => [
        '256x256' => '256x256',
        '512x512' =>  '512x512',
        '1024x1024' =>  '1024x1024',
    ],

    'ssl_verify_host' => false,

    'ssl_verify_peer' => false,

    /*
    |--------------------------------------------------------------------------
    | The application is in demo mode or not
    |--------------------------------------------------------------------------
    |
    | This option controls the demo mode of the application.
    |
    | value: true, false
    |
    */

    'is_demo' => env('IS_DEMO', false),

    /* The application version */
    'version' => env('ARTIFISM_VERSION', '1.0.0'),

     /**
     * Thumbnail path
     *
     * Note:If you want to change the thumbnail_dir name,
     * You must change dir name from public/uploads/[old thumbnail directory name]
     * */
    'thumbnail_dir' => 'sizes',


    /* Demo account credentails, Only work when the application on demo mode */
    'credentials' => [
        'admin' => [
            'email' => 'admin@techvill.net',
            'password' => '123456'
        ],
        'customer' => [
            'email' => 'user@techvill.net',
            'password' => '123456'
        ]
    ],

];
