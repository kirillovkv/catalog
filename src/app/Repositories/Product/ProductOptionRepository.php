<?php

namespace App\Repositories\Product;

use App\DTO\Product\ProductOptionDTO;
use App\Models\Product\Option;
use App\Models\Product\Option\Value;

class ProductOptionRepository
{

    /**
     * @return ProductOptionDTO[]
     */
    public function getByProductIds(array $ids): array
    {
        $options = Option::query()
            ->whereIn('product_id', $ids)
            ->get();

        return ProductOptionDTO::fromCollection($options);
    }

    public function add(int $productId, string $name): ProductOptionDTO
    {
        $option = Option::query()
            ->updateOrCreate([
                'product_id' => $productId,
                'name'       => $name,
            ]);

        return ProductOptionDTO::fromModel($option);
    }

    public function getProductIdsByFilters(array $filters): array
    {
        return $this->getFilterBuilder($filters)
            ->values()
            ->toArray();
    }

    public function getCountProductIdsByFilters(array $filters): int
    {
        return $this->getFilterBuilder($filters)->count();
    }

    protected function getFilterBuilder(array $filters)
    {
        $query = Value::query();

        foreach ($filters as $name => $values) {
            $query->where(function ($query) use ($name, $values) {
                $query->whereHas('option', function ($query) use ($name, $values) {
                    $query->where('name', $name);
                })->whereIn('value', $values);
            });
        }

        return $query
            ->with('option')
            ->get()
            ->pluck('option.product_id')
            ->unique();
    }
}
