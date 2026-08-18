<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('naco:warrants:process-expiry')->dailyAt('06:00')->withoutOverlapping()->onOneServer();
