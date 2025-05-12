<?php

namespace App\DTO\Filter;

class GetProductsFilterDTO
{
    public array $properties;
    public int $page;
    public int $limit;
    public const PER_PAGE = 40;

    public function __construct(array $data)
    {
        $this->properties = $data['properties'] ?? [];
        $this->page = $data['page'] ?? 1;
        $this->limit = self::PER_PAGE;
    }

    public function toArray(): array
    {
        return [
            'properties' => $this->properties,
            'page'       => $this->page,
            'limit'      => $this->limit,
        ];
    }
}
