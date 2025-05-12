<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductOption",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Цвет"),
 *     @OA\Property(
 *         property="values",
 *         type="array",
 *         @OA\Items(type="string", example="Синий")
 *     )
 * )
 */
class ProductOptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'   => $this->resource->name,
            'values' => array_map(function ($item) {
                return $item->value;
            }, $this->resource->values),
        ];
    }
}
