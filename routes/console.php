<?php

use App\Jobs\SendWeeklyReportJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendWeeklyReportJob, 'reports')
    ->weeklyOn(1, '10:00')->name('reports.weekly');
