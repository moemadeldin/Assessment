<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\SalesReturn;
use Illuminate\Support\Str;

final readonly class SalesReturnObserver
{
    public function creating(SalesReturn $salesReturn): void
    {
        if (empty($salesReturn->return_number)) {
            $salesReturn->return_number = $this->generateReturnNumber();
        }
    }

    private function generateReturnNumber(): string
    {
        $year = now()->year;
        $prefix = 'RET-'.$year.'-';

        do {
            $random = Str::upper(Str::random(8));
            $returnNumber = $prefix.$random;
        } while (SalesReturn::query()->where('return_number', $returnNumber)->exists());

        return $returnNumber;
    }
}
