<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'aktivitas',
        'ip_address',
    ];

    public static function log($aktivitas)
    {
        return self::create([
            'user_id' => auth()->id(),
            'username' => auth()->user() ? auth()->user()->username : 'Guest',
            'aktivitas' => $aktivitas,
            'ip_address' => request()->ip(),
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
