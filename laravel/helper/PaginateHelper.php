<?php

namespace App\Helper;

use Illuminate\Database\Eloquent\Builder;

class PaginateHelper
{
    /**
     * Paginate data with search, filtering, ordering, and relationships.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  array  $options  Options for pagination, search, filters, sorting, and relationships.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function paginate(Builder $query, array $options = [])
    {
        $perPage = $options['per_page'] ?? 10;
        $page = $options['page'] ?? null;
        $search = $options['search'] ?? null;
        $searchFields = $options['search_fields'] ?? [];
        $filters = $options['filters'] ?? [];
        $orderBy = $options['order_by'] ?? 'id';
        $orderDirection = $options['order_direction'] ?? 'asc';
        $relationships = $options['with'] ?? [];

        // Apply relationships
        if ( ! empty($relationships)) {
            $query->with($relationships);
        }

        // Apply search
        if ($search && ! empty($searchFields)) {
            $query->where(function ($q) use ($search, $searchFields) {
                foreach ($searchFields as $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $relatedField] = explode('.', $field, 2);
                        $q->orWhereHas($relation, function ($subQuery) use ($search, $relatedField) {
                            $subQuery->where($relatedField, 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($field, 'like', "%{$search}%");
                    }
                }
            });
        }

        // Apply filters
        foreach ($filters as $field => $value) {
            if ($value === null) {
                continue;
            }

            if (str_contains($field, '.')) {
                [$relation, $relatedField] = explode('.', $field, 2);
                $query->whereHas($relation, function ($subQuery) use ($relatedField, $value) {
                    if (is_array($value)) {
                        $subQuery->whereIn($relatedField, $value);
                    } else {
                        $subQuery->where($relatedField, $value);
                    }
                });
            } else {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        // Apply sorting
        if (str_contains($orderBy, '.')) {
            [$relation, $relatedField] = explode('.', $orderBy, 2);
            $query->orderByRelation($relation, $relatedField, $orderDirection);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        // Return paginated result with optional page
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
