<?php

namespace Database\Seeders;

use App\Services\Product\ProductService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ProductOptionValueSeeder extends Seeder
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
            $productIds = $this->productService->getIds();
            $productOptions = $this->productService->getOptionsByProductIds($productIds);

            $filePath = database_path(self::MOCK_PATH);
            $content = file_get_contents($filePath);

            $options = json_decode($content, true);

            foreach ($productOptions as $option) {
                $randIndex = array_rand($options[$option->name]);
                $value = $options[$option->name][$randIndex];
                $this->productService->addOptionValue($option->id, $value);
            }
        } catch (Exception $e) {
            Log::error("ProductOptionValueSeeder fell into error", ['exception' => $e->getMessage()]);

            throw new Exception("ProductOptionValueSeeder fell into error: ".$e->getMessage());
        }
    }
}
