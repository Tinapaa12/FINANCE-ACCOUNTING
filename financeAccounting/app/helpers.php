<?php

use App\Models\AuditLog;

if (!function_exists('audit_log')) {
    function audit_log($loggable, $action, $description = null, $oldValues = null, $newValues = null)
    {
        AuditLog::create([
            'loggable_id' => $loggable->id ?? $loggable,
            'loggable_type' => get_class($loggable),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'user' => auth()->user()->name ?? 'System',
        ]);
    }
}
