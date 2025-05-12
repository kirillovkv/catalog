<?php

namespace App\Repositories\Product;

use App\DTO\Product\ProductOptionDTO;
use App\DTO\Product\ProductOptionValueDTO;
use App\Models\Product\Option\Value;

class ProductOptionValueRepository
{

    public function add(int $productOptionId, string $value): ProductOptionValueDTO
    {
        $item = Value::query()
            ->updateOrCreate([
                'product_option_id' => $productOptionId,
            ], [
                'value' => $value,
            ]);

        return ProductOptionValueDTO::fromModel($item);
    }


    /**
     * @return ProductOptionValueDTO[]
     */
    public function getByOptionIds(array $ids): array
    {
        $values = Value::query()
            ->whereIn('product_option_id', $ids)
            ->get();
        return ProductOptionValueDTO::fromCollection($values);
    }
}
