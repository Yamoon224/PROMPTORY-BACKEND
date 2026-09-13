<?php

namespace App\Models;

use App\Domains\Users\Enums\UserRole;
use App\Domains\Users\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Compte utilisateur, tous roles confondus : acheteur, createur, moderateur,
 * administrateur. Une seule table plutot que plusieurs : ce sont les memes
 * colonnes et la meme authentification, seules les permissions different.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property UserRole $role
 * @property string|null $profile_photo
 * @property UserStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /** @var list<string> */
    protected $fillable = ['name', 'email', 'password', 'role', 'profile_photo', 'status'];

    /** @var list<string> */
    protected $hidden = ['password', 'remember_token'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    /** @return HasMany<Prompt, $this> */
    public function prompts(): HasMany
    {
        return $this->hasMany(Prompt::class);
    }

    /** @return HasMany<Folder, $this> */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    /** @return HasMany<Pack, $this> */
    public function packs(): HasMany
    {
        return $this->hasMany(Pack::class);
    }

    /** Achats effectues par ce compte.
     *
     * @return HasMany<Sale, $this>
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Sale::class, 'buyer_id');
    }

    /** Ventes encaissees par ce compte en tant que createur.
     *
     * @return HasMany<Sale, $this>
     */
    public function salesAsCreator(): HasMany
    {
        return $this->hasMany(Sale::class, 'creator_id');
    }

    /** @return HasMany<Subscription, $this> */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
