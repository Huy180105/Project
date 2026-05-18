<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('platform:about', function (): void {
    $this->info('StudyWell AI Platform Laravel app');
});
