<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Rules\NoNumbers;
use Illuminate\Foundation\Http\FormRequest;

final class StoreCustomerRequest extends FormRequest
{
    /**
     * @return array<string, list<string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new NoNumbers()],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'phone' => ['nullable', 'string', 'digits:11'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
