<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait PaginatesQuery
{
    protected function paginateQuery(Builder $query, Request $request, ?callable $search = null)
    {
        if ($search && $term = $request->query('q')) {
            $search($query, $term);
        }

        $perPage = (int) $request->query('per_page', 15);
        return $query->paginate($perPage)->withQueryString();
    }
}
