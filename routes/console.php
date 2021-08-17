<?php

use App\Models\Drink;
use Illuminate\Support\Facades\Artisan;

Artisan::command('init', function () {
    $drinks = [
        [
            'name' => 'Monster Ultra Sunrise',
            'caffeine' => 150
        ],
        [
            'name' => 'Black Coffee',
            'caffeine' => 95
        ],
        [
            'name' => 'Americano',
            'caffeine' => 77
        ],
        [
            'name' => 'Sugar free NOS',
            'caffeine' => 260
        ],
        [
            'name' => '5 Hour Energy',
            'caffeine' => 200
        ]
    ];

    foreach ($drinks as $drink) {
        Drink::create($drink);
    }
});
