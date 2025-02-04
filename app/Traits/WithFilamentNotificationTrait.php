<?php

namespace App\Traits;

use Filament\Notifications\Notification;

trait WithFilamentNotificationTrait
{
    /**
     * Send a notification with custom message and type
     *
     * @param string $message
     * @param string $type success|warning|error|info
     * @param int $duration
     * @return \Filament\Notifications\Notification
     */
    private function _notify(string $message, string $type = 'success', int $duration = 6000): Notification
    {
        return Notification::make()
            ->title($message)
            ->{$type}()
            ->duration($duration)
            ->send();
    }

    /**
     * Send a notification with custom icon
     *
     * @param string $message
     * @param string $type success|warning|error|info
     * @param string $icon
     * @param int $duration
     * @return \Filament\Notifications\Notification
     */
    protected function notifyWithIcon(string $message, string $type = 'success', string $icon = 'heroicon-o-check-circle', int $duration = 6000): Notification
    {
        return Notification::make()
            ->title($message)
            ->{$type}()
            ->icon($icon)
            ->duration($duration)
            ->send();
    }
}