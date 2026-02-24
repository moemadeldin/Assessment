<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SalesReturnStatus;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ScopedBy([TenantScope::class])]
/**
 * @property string $id
 * @property string $user_id
 * @property string $customer_id
 * @property string|null $invoice_id
 * @property string $return_number
 * @property \Illuminate\Support\Carbon $return_date
 * @property numeric $subtotal
 * @property numeric $tax
 * @property numeric $total
 * @property string|null $reason
 * @property string|null $notes
 * @property SalesReturnStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Customer $customer
 * @property-read string $formatted_subtotal
 * @property-read string $formatted_tax
 * @property-read string $formatted_total
 * @property-read Invoice|null $invoice
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SalesReturnItem> $items
 * @property-read int|null $items_count
 * @property-read User $user
 */
final class SalesReturn extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $casts = [
        'id' => 'string',
        'user_id' => 'string',
        'customer_id' => 'string',
        'invoice_id' => 'string',
        'return_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => SalesReturnStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    protected function formattedSubtotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) $this->subtotal, 2),
        );
    }

    protected function formattedTax(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) $this->tax, 2),
        );
    }

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) $this->total, 2),
        );
    }

    #[Scope]
    protected function search(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search): void {
            $q->where('return_number', 'like', sprintf('%%%s%%', $search))
                ->orWhereHas('customer', function (Builder $customerQuery) use ($search): void {
                    $customerQuery->where('name', 'like', sprintf('%%%s%%', $search));
                });
        });
    }

    #[Scope]
    protected function filterByStatus(Builder $query, ?SalesReturnStatus $status): Builder
    {
        if (! $status instanceof SalesReturnStatus) {
            return $query;
        }

        return $query->where('status', $status->value);
    }

    #[Scope]
    protected function filterByDateFrom(Builder $query, ?string $dateFrom): Builder
    {
        if (! $dateFrom) {
            return $query;
        }

        return $query->whereDate('return_date', '>=', $dateFrom);
    }

    #[Scope]
    protected function filterByDateTo(Builder $query, ?string $dateTo): Builder
    {
        if (! $dateTo) {
            return $query;
        }

        return $query->whereDate('return_date', '<=', $dateTo);
    }

    #[Scope]
    protected function withCustomerAndUsers(Builder $query): Builder
    {
        return $query->with(['customer', 'user'])->latest();
    }
}
