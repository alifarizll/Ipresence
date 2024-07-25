<?php

namespace App\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;

class ExampleEvent
{
    use SerializesModels;

    public $user;

    public $entityType;

    public $entityName;

    public $operation;

    public $status;

    public function __construct(Authenticatable $user, $entityType, $entityName, $operation, $status)
    {
        $this->user = $user;
        $this->entityType = $entityType;
        $this->entityName = $entityName;
        $this->operation = $operation;
        $this->status = $status;
    }
}
