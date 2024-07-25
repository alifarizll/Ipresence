<?php

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;

if (!function_exists('config_path')) {
    function config_path($path = '') {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('now')) {
    function now() {
        return Carbon::now('Asia/Jakarta');
    }
}
