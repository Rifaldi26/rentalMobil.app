<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Catat aksi kritis ke tabel audit_logs.
     *
     * Contoh penggunaan:
     *   AuditLogService::log('partner.verified', $partner);
     *   AuditLogService::log('booking.confirmed', $booking, $oldValues);
     */
    public static function log(
        string $action,
        ?Model $model = null,
        array $oldValues = [],
        array $newValues = []
    ): void {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues ?: ($model?->getChanges() ?? []),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
