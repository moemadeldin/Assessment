<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class NoNumbers implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $stringValue = (string) $value;
        if (preg_match('/\d/', $stringValue)) {
            $fail('The :attribute must not contain any numbers.');
        }
    }
}
