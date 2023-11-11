<?php

namespace App\Traits;

trait SendsNotifications
{
    public function sendNotification($message, $type = 'info')
    {
        $this->dispatch('notification', [
            'message' => $message,
            'type' => $type,
        ]);
    }
}
