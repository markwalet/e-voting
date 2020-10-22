<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OTPHP\TOTP;
use OTPHP\TOTPInterface;
use RuntimeException;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * Returns TOTP configured to 8 digits
     * @param null|string $secret
     * @return TOTPInterface
     */
    private static function getTotp(?string $secret): TOTPInterface
    {
        return TOTP::create($secret, 30, 'sha1', 8);
    }

    /**
     * Ensure a totp_token is always set
     * @return void
     */
    public static function booted()
    {
        self::saving(static function (User $user) {
            if (empty($user->totp_secret)) {
                $user->totp_secret = self::getTotp(null)->getSecret();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'phone',
        'totp_secret'
    ];

    /**
     * The attributes that should be cast.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_voter' => 'bool',
        'is_admin' => 'bool',
        'is_monitor' => 'bool',
        'conscribo_id' => 'int'
    ];

    /**
     * Returns verification instance
     * @return TOTPInterface
     * @throws RuntimeException
     */
    public function getTotpAttribute(): TOTPInterface
    {
        if (empty($this->totp_secret)) {
            throw new RuntimeException('TOTP secret not set');
        }

        return self::getTotp($this->totp_secret);
    }
}
