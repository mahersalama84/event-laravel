<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;
use YieldStudio\LaravelExpoNotifier\Models\ExpoNotification;

class ActivateNotification extends Notification
{
   public function via($notifiable): array
   {
      return [ExpoNotificationsChannel::class];
   }

   public function toExpoNotification($notifiable): ExpoMessage
   {
      $full_name = $notifiable->full_name;
      $title = $notifiable->is_active ? 'activated' : 'deactivated';
      $body = $notifiable->is_active ? 'your account has been activated' : 'your account has been deactivated';
      $message = ExpoMessage::create()
         ->enableSound()
         ->to([$notifiable->expoToken->value])
         // ->to(['ExponentPushToken[jdjyH3JOtOCXCJ6mBIr-8J]'])
         ->title($full_name . ', ' . $title)
         ->body($body)
         ->channelId('Eveky-info')
         ->jsonData(['foo' => 'foo content']);

      ExpoNotification::create([
         'data' => $message->toJson(),
      ]);
      return $message;
   }
}
