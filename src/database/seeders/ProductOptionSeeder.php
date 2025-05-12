<?php

namespace Database\Seeders;

use App\DTO\Filter\GetProductsFilterDTO;
use App\Services\Product\ProductService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductOptionSeeder extends Seeder
{
    private const MOCK_PATH = 'seeders/data/options.json';

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

            $options = json_decode($content, true);

            $products = $this->productService->get(new GetProductsFilterDTO(data: []));

            foreach ($products as $product) {
                $randOptions = array_rand($options, 5);
                foreach ($randOptions as $name) {
                    $this->productService->addOption($product->id, $name);
                }
            }
        } catch (Exception $e) {
            Log::error("ProductOptionSeeder fell into error", ['exception' => $e->getMessage()]);

            throw new Exception("ProductOptionSeeder fell into error: ".$e->getMessage());
        }
    }
}
