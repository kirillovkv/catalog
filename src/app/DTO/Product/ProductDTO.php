<?php

namespace App\DTO\Product;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductDTO
{
    public function __construct(
        public string $name,
        public float $price,
        public int $quantity,
        public array $options = [],
        public ?int $id = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id'       => $this?->id,
            'name'     => $this->name,
            'price'    => $this->price,
            'quantity' => $this->quantity,
        ];
    }

    /**
     * @param  Collection<Product>  $items
     * @return ProductDTO[]
     */
    public static function fromCollection(Collection $items): array
    {
        $data = [];

        foreach ($items as $item) {
            $data[] = self::fromModel($item);
        }

        return $data;
    }

    public static function fromModel(Product $product): self
    {
        return new self(
            name: $product->name,
            price: $product->price,
            quantity: $product->quantity,
            id: $product->id
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            price: $data['price'],
            quantity: $data['quantity'],
            id: $data['id'] ?? null
        );
    }
}
