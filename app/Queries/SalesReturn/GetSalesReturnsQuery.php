<?php

declare(strict_types=1);

namespace App\Queries\SalesReturn;

use App\Enums\SalesReturnStatus;
use App\Models\SalesReturn;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\Paginator;

final readonly class GetSalesReturnsQuery
{
    public function execute(array $filters): Paginator
    {
        $status = isset($filters['status']) && is_string($filters['status'])
            ? SalesReturnStatus::from($filters['status'])
            : null;

        return SalesReturn::query()
            ->withCustomerAndUsers()
            ->search(isset($filters['search']) && is_string($filters['search']) ? $filters['search'] : null)
            ->filterByStatus($status)
            ->filterByDateFrom(isset($filters['date_from']) && is_string($filters['date_from']) ? $filters['date_from'] : null)
            ->filterByDateTo(isset($filters['date_to']) && is_string($filters['date_to']) ? $filters['date_to'] : null)
            ->simplePaginate(Constants::NUMBER_OF_PAGINATED_SALES_RETURNS)
            ->withQueryString();
    }
}
