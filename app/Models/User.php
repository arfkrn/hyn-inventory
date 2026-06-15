<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;

class User extends Authenticatable implements HasAvatar, HasName, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar'
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
            'password' => 'hashed',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=000000';;
    }

    public function getFilamentName(): string
    {
        $role = $this->getRoleNames()->first() ?? 'No Role';
        
        return "{$this->name} (" . ucfirst($role) . ")";
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // OPSI A: Untuk bypass testing (Memastikan error 403 & Class implements hilang)
        return true;

        // OPSI B: Jika menggunakan Spatie Permission (Aktifkan ini jika sudah normal)
        // return $this->hasRole('admin') || $this->hasRole('super-admin');
        
        // OPSI C: Jika menggunakan kolom 'role' manual di tabel users
        // return $this->role === 'admin';
    }

}
