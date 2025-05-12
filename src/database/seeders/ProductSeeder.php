<?php

namespace Database\Seeders;

use App\DTO\Product\ProductDTO;
use App\Services\Product\ProductService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductSeeder extends Seeder
{
    private const MOCK_PATH = "seeders/data/products.json";

    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Run the database seeds.
     * @throws Exception
     */
    public function run(): void
    {
        try {
            $filePath = database_path(self::MOCK_PATH);
            $content = file_get_contents($filePath);

            $products = json_decode($content, true);

            foreach ($products as $product) {
                $this->productService->updateOrCreate(new ProductDTO(
                    name: $product,
                    price: rand(1, 100000),
                    quantity: rand(1, 1000)
                ));
            }
        } catch (Exception $e) {
            Log::error("ProductSeeder fell into error", ['exception' => $e->getMessage()]);

            throw new Exception("ProductSeeder fell into error: ".$e->getMessage());
        }
    }
}
