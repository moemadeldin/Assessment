<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\SalesReturnStatus;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $user_id
 * @property string $customer_id
 * @property string $invoice_number
 * @property Carbon $invoice_date
 * @property numeric $subtotal
 * @property numeric $tax
 * @property numeric $tax_rate
 * @property numeric $total
 * @property numeric $sales_return_total
 * @property InvoiceStatus $status
 * @property string|null $pre_return_status
 * @property Carbon|null $due_date
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read float $adjusted_total
 * @property-read Collection<int, SalesReturn> $approvedReturns
 * @property-read int|null $approved_returns_count
 * @property-read Customer $customer
 * @property-read string $formatted_adjusted_total
 * @property-read string $formatted_sales_return_total
 * @property-read string $formatted_subtotal
 * @property-read string $formatted_tax
 * @property-read string $formatted_tax_rate
 * @property-read string $formatted_total
 * @property-read float $paid_amount
 * @property-read string $payment_status
 * @property-read float $remaining_amount
 * @property-read Collection<int, InvoiceItem> $items
 * @property-read int|null $items_count
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read Collection<int, SalesReturn> $salesReturns
 * @property-read int|null $sales_returns_count
 * @property-read User $user
 */
#[ScopedBy([TenantScope::class])]
final class Invoice extends Model
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
        'customer_id' => 'string',
        'invoice_number' => 'string',
        'invoice_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total' => 'decimal:2',
        'sales_return_total' => 'decimal:2',
        'status' => InvoiceStatus::class,
        'pre_return_status' => InvoiceStatus::class,
        'due_date' => 'date',
        'notes' => 'string',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function salesReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function approvedReturns(): HasMany
    {
        return $this->hasMany(SalesReturn::class)
            ->where('status', SalesReturnStatus::Approved->value);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected function paidAmount(): Attribute
    {
        return Attribute::make(
            get: fn (): float => round((float) $this->payments()->sum('amount'), 2),
        );
    }

    protected function remainingAmount(): Attribute
    {

        return Attribute::make(

            get: fn (): float => round((float) $this->total - $this->paid_amount, 2),
        );
    }

    protected function paymentStatus(): Attribute
    {
        return Attribute::make(
            get: function (): PaymentStatus {
                $paid = $this->paid_amount;
                $total = (float) $this->total;

                return match (true) {
                    $paid <= 0 => PaymentStatus::Unpaid,
                    $paid >= $total => PaymentStatus::Paid,
                    default => PaymentStatus::Partial,
                };
            },
        );
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

    protected function formattedTaxRate(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) ($this->tax_rate ?? 0), 2),
        );
    }

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) $this->total, 2),
        );
    }

    protected function formattedSalesReturnTotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) ($this->sales_return_total ?? 0), 2),
        );
    }

    protected function adjustedTotal(): Attribute
    {
        return Attribute::make(
            get: fn (): float => (float) $this->total - (float) ($this->sales_return_total ?? 0),
        );
    }

    protected function formattedAdjustedTotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format($this->adjustedTotal, 2),
        );
    }

    #[Scope]
    protected function search(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search): void {
            $q->where('invoice_number', 'like', sprintf('%%%s%%', $search))
                ->orWhereHas('customer', function (Builder $customerQuery) use ($search): void {
                    $customerQuery->where('name', 'like', sprintf('%%%s%%', $search));
                });
        });
    }

    #[Scope]
    protected function filterByStatus(Builder $query, ?InvoiceStatus $status): Builder
    {
        if (! $status instanceof InvoiceStatus) {
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

        return $query->whereDate('due_date', '>=', $dateFrom);
    }

    #[Scope]
    protected function filterByDateTo(Builder $query, ?string $dateTo): Builder
    {
        if (! $dateTo) {
            return $query;
        }

        return $query->whereDate('due_date', '<=', $dateTo);
    }

    #[Scope]
    protected function withCustomerAndUsers(Builder $query): Builder
    {
        return $query->with(['customer', 'user'])->latest();
    }

    #[Scope]
    protected function overdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now()->toDateString())
            ->where('status', '!=', InvoiceStatus::Paid);
    }

    #[Scope]
    protected function overdueDays(Builder $query): Builder
    {
        return $query->selectRaw('*, DATEDIFF(CURRENT_DATE, due_date) as days_overdue');
    }
}
