<?php

declare(strict_types=1);

namespace App\Http\Requests\SalesReturn;

use App\Enums\SalesReturnStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreSalesReturnRequest extends FormRequest
{
    /**
     * @return array<string, list<string>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'invoice_id' => ['required', 'uuid', 'exists:invoices,id'],
            'return_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', Rule::enum(SalesReturnStatus::class)],
            'items' => ['required', 'array', 'min:1', function ($attribute, $value, $fail): void {
                $hasValidItem = false;
                foreach ($value as $item) {
                    if (isset($item['qty']) && (int) $item['qty'] > 0) {
                        $hasValidItem = true;
                        break;
                    }
                }

                if (! $hasValidItem) {
                    $fail('At least one item must have a qty greater than 0.');
                }
            }],
            'items.*.qty' => ['required', 'integer', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.invoice_item_id' => ['required', 'uuid', 'exists:invoice_items,id'],
        ];
    }
}
