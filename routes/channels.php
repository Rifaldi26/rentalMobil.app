<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * Channel privat untuk percakapan antara dua user.
 * Nama channel: chat.{minId}-{maxId}
 *
 * User hanya boleh subscribe jika id-nya ada di dalam channel tersebut.
 */
Broadcast::channel('chat.{channelName}', function ($user, $channelName) {
    // channelName format: "3-7" (sorted IDs)
    $ids = explode('-', $channelName);

    if (count($ids) !== 2) return false;

    return in_array((string) $user->id, $ids);
});
