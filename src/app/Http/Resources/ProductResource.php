<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Кровать"),
 *     @OA\Property(property="price", type="integer", example=42786),
 *     @OA\Property(property="quantity", type="integer", example=571),
 *     @OA\Property(
 *         property="options",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ProductOption")
 *     )
 * )
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->resource->id,
            'name'     => $this->resource->name,
            'price'    => $this->resource->price,
            'quantity' => $this->resource->quantity,
            'options'  => ProductOptionResource::collection($this->resource->options),
        ];
    }
}
