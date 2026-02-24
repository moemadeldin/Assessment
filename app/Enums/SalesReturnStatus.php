<?php

declare(strict_types=1);

namespace App\Enums;

enum SalesReturnStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Processed = 'processed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Processed => 'Processed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-green-900 text-green-300',
            self::Approved => 'bg-yellow-900 text-yellow-300',
            self::Rejected => 'bg-red-900 text-red-300',
            self::Processed => 'bg-blue-900 text-blue-300',
        };
    }
}
