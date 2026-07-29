<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('attendance:close')
    ->everyMinute();