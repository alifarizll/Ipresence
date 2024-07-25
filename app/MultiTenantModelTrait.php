<?php

namespace App;

use App\Observers\SignatureObserver;

trait MultiTenantModelTrait
{
    public static function bootMultiTenantModelTrait()
    {
        static::observe(SignatureObserver::class);
    }
}
