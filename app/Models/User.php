<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UserStatus;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens, HasFactory;

    protected $attributes = [
        'gender' => Gender::Unknown,
        'status' => UserStatus::Inactivated,
    ];

    protected $casts = [
        'gender' => Gender::class,
        'birthday' => 'date',
        'status' => UserStatus::class,
    ];

    protected $fillable = [
        'nickname',
        'avatar',
        'gender',
        'birthday',
        'location',
        'status',
    ];

    protected $hidden = [
        'remember_token',
    ];

    /**
     * 属于此用户的登录凭证
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function passport(): HasOne
    {
        return $this->hasOne(Passport::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthPassword()
    {
        return $this->passport?->password;
    }
}
