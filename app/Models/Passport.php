<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passport extends Model
{
    use HasFactory;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'username',
        'password',
        'email',
        'email_verified_at',
        'phone',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * 此登录凭证所属的用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
