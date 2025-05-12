<?php

namespace App\Repositories\Product;

use App\DTO\Filter\GetProductsFilterDTO;
use App\DTO\Product\ProductDTO;
use App\Models\Product;

class ProductRepository
{

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        return Product::query()
            ->select('id')
            ->get()
            ->pluck('id')
            ->toArray();
    }

    /**
     * @return ProductDTO[]
     */
    public function getFiltered(GetProductsFilterDTO $DTO, ?array $ids = null): array
    {
        $products = Product::query()
            ->when($ids, function ($query, $ids) {
                $query->whereIn('id', $ids);
            })
            ->offset($DTO->page > 1 ? ($DTO->page * $DTO->limit) - $DTO->limit : 0)
            ->limit($DTO->limit)
            ->get();

        return ProductDTO::fromCollection($products);
    }

    /**
     * @return ProductDTO[]
     */
    public function get(): array
    {
        $products = Product::query()
            ->get();

        return ProductDTO::fromCollection($products);
    }

    public function updateOrCreate(ProductDTO $DTO): ProductDTO
    {
        $product = Product::query()
            ->updateOrCreate(
                ['name' => $DTO->name],
                [
                    'price'    => $DTO->price,
                    'quantity' => $DTO->quantity,
                ]
            );

        return ProductDTO::fromModel($product);
    }

    public function getCount(): int
    {
        return Product::query()
            ->count();
    }
}
