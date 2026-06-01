<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'url',
        'icon',
        'type',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function notify(int $userId, string $title, string $message, ?string $url = null, string $icon = 'fa-bell', string $type = 'info'): self
    {
        return self::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'icon'    => $icon,
            'type'    => $type,
        ]);
    }
}
