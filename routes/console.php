<?php

// use Illuminate\Support\Facades\Schedule;

// Schedule::command('attendance:close')
//     ->everyMinute();


use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');