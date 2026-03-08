<?php 
namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Sorts\Sort;

class QuoteAddressSort implements Sort
{
    public function __construct(private string $column) {}

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'desc' : 'asc';

        $query->orderBy(
            DB::raw(
                "(SELECT qa.{$this->column}
                  FROM quote_addresses qa
                  WHERE qa.quote_id = quotes.id
                  AND qa.type = 'billing'
                  LIMIT 1)"
            ),
            $direction
        );
    }
}

