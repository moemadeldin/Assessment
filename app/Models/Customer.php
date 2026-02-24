<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 */
#[ScopedBy([TenantScope::class])]
final class Customer extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'name' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'address' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class);
    }

    #[Scope]
    protected function search(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search): void {
            $q->where('name', 'like', sprintf('%%%s%%', $search))
                ->orWhereAny(['email', 'phone'], 'like', sprintf('%%%s%%', $search));
        });
    }

    #[Scope]
    protected function withUser(Builder $query): Builder
    {
        return $query->with('user')->latest();
    }
}
