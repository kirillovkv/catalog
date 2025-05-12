<?php

namespace App\DTO\Product;

use App\Models\Product\Option;
use App\Models\Product\Option\Value;
use Illuminate\Support\Collection;

class ProductOptionValueDTO
{
    public function __construct(
        public string $value,
        public int $productOptionId,
        public ?int $id = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id'         => $this?->id,
            'value'      => $this->value,
            'product_id' => $this->productOptionId,
        ];
    }

    /**
     * @param  Collection<Value>  $items
     * @return ProductOptionValueDTO[]
     */
    public static function fromCollection(Collection $items): array
    {
        $data = [];

        foreach ($items as $item) {
            $data[] = self::fromModel($item);
        }

        return $data;
    }

    public static function fromModel(Value $product): self
    {
        return new self(
            value: $product->value,
            productOptionId: $product->product_option_id,
            id: $product->id
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
            productOptionId: $data['product_option_id'],
            id: $data['id'] ?? null
        );
    }
}
