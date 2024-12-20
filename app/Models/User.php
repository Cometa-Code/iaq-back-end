<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'principal_document',
        'role',
        'is_active',
        'password',
    ];

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function young_apprentice_data() : HasOne
    {
        return $this->hasOne(YoungApprenticeData::class, 'user_id');
    }

    public function company_data() : HasOne
    {
        return $this->hasOne(CompanyData::class, 'user_id');
    }

    public function young_apprentice_contracts() : HasMany
    {
        return $this->hasMany(Contracts::class, 'young_apprentice_id');
    }

    public function company_contracts() : HasMany
    {
        return $this->hasMany(Contracts::class, 'company_id');
    }

    public function jobs() : HasMany
    {
        return $this->hasMany(JobModel::class, 'user_id');
    }

    public function presence() : HasMany
    {
        return $this->hasMany(User::class);
    }
}
