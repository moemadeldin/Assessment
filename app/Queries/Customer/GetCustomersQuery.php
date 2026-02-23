<?php

declare(strict_types=1);

namespace App\Queries\Customer;

use App\Models\Customer;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\Paginator;

final readonly class GetCustomersQuery
{
    public function execute(array $filters): Paginator
    {
        return Customer::query()
            ->withUser()
            ->search(isset($filters['search']) && is_string($filters['search']) ? $filters['search'] : null)
            ->simplePaginate(Constants::NUMBER_OF_PAGINATED_CUSTOMERS)
            ->withQueryString();
    }
}
