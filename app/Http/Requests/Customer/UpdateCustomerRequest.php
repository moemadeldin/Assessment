<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use App\Rules\NoNumbers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCustomerRequest extends FormRequest
{
    /**
     * @return array<string, list<string>|string>
     */
    public function rules(): array
    {
        $customer = $this->route('customer');

        return [
            'name' => ['nullable', 'string', 'max:255', new NoNumbers()],
            'email' => ['nullable', 'string', 'email:rfc,dns', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:11'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
