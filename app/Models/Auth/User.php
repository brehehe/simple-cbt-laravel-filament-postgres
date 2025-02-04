<?php

namespace App\Models\Auth;

use App\Traits\HasActivityLogs;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use Notifiable, HasApiTokens, HasRoles, SoftDeletes, HasUuids, HasActivityLogs;
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        // Jika ada foto, ambil URL-nya menggunakan Storage::url, jika tidak, kembalikan null
        return $this->photo ? Storage::url($this->photo) : null;
    }

    protected static function booted()
    {
        parent::boot();

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('order', 'desc');
        });

        static::creating(function ($model) {
            // Jika kolom 'order' belum di-set, set dengan urutan terbesar + 1
            if (!$model->order) {
                $model->order = static::max('order') + 1;
            }
        });
    }
}
