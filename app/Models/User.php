<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wallet (): HasOne {
        return $this->hasOne(Wallet::class);
    }

    public function category (): HasMany {
        return $this->hasMany(Category::class);
    }

    protected static function booted () {
        static::created(function ($user) {
            if (User::count() === 1) {
                $categoriasPadrao = [
                    'Transporte', 'Alimentacao', 'Lazer', 'Financeiro',
                ];

                foreach ($categoriasPadrao as $categoria) {
                    Category::create([
                        'name' => $categoria,
                        'isPersonalizada' => false,
                        'user_id' => 0,
                    ]);
                }
            }
        });
    }
}
