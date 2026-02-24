<?php

declare(strict_types=1);

namespace App\DTOs\Invoices;

final readonly class StoreInvoiceData
{
    public function __construct(
        public string $customerId,
        public array $items,
        public string $status,
        public float $taxRate,
        public string $invoiceDate,
        public string $dueDate,
        public ?string $notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $itemsData = $data['items'];
        $customerId = $data['customer_id'];
        $status = $data['status'];
        $taxRate = (float) ($data['tax_rate'] ?? 0);
        $invoiceDate = $data['invoice_date'];
        $dueDate = $data['due_date'] ?? null;
        $notes = $data['notes'] ?? null;

        return new self(
            customerId: (string) $customerId,
            items: array_map(
                InvoiceItemData::fromArray(...),
                $itemsData,
            ),
            status: (string) $status,
            taxRate: $taxRate,
            invoiceDate: $invoiceDate,
            dueDate: $dueDate,
            notes: $notes !== null ? (string) $notes : null,
        );
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'items' => $this->items,
            'status' => $this->status,
            'tax_rate' => $this->taxRate,
            'invoice_date' => $this->invoiceDate,
            'due_date' => $this->dueDate,
            'notes' => $this->notes,
        ];
    }
}
