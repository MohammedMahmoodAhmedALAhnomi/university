<?php

namespace App\Traits;

trait Loggable
{
    protected function log(string $action, ?string $table = null, ?int $recordId = null, ?string $details = null): void
    {
        log_activity($action, $table, $recordId, $details);
    }
}
