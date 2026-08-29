<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('naco:instructors:sync-warrants')->dailyAt('06:15')->withoutOverlapping()->onOneServer();
