<?php

namespace App\DTO\Product;

use App\Models\Product\Option;
use Illuminate\Support\Collection;

class ProductOptionDTO
{
    public function __construct(
        public string $name,
        public int $productId,
        public array $values = [],
        public ?int $id = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id'         => $this?->id,
            'name'       => $this->name,
            'product_id' => $this->productId,
        ];
    }

    /**
     * @param  Collection<Option>  $items
     * @return ProductOptionDTO[]
     */
    public static function fromCollection(Collection $items): array
    {
        $data = [];

        foreach ($items as $item) {
            $data[] = self::fromModel($item);
        }

        return $data;
    }

    public static function fromModel(Option $product): self
    {
        return new self(
            name: $product->name,
            productId: $product->product_id,
            id: $product->id
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            productId: $data['product_id'],
            id: $data['id'] ?? null
        );
    }
}
