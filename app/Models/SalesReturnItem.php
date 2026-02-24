<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $sales_return_id
 * @property string|null $invoice_item_id
 * @property string $description
 * @property int $qty
 * @property numeric $unit_price
 * @property numeric $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SalesReturn $salesReturn
 * @property-read InvoiceItem|null $invoiceItem
 */
final class SalesReturnItem extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $casts = [
        'id' => 'string',
        'sales_return_id' => 'string',
        'invoice_item_id' => 'string',
        'description' => 'string',
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    protected function formattedUnitPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) $this->unit_price, 2),
        );
    }

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => number_format((float) $this->total, 2),
        );
    }
}
