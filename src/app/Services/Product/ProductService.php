<?php

namespace App\Services\Product;

use App\DTO\Filter\GetProductsFilterDTO;
use App\DTO\Product\ProductDTO;
use App\DTO\Product\ProductOptionDTO;
use App\DTO\Product\ProductOptionValueDTO;
use App\Repositories\Product\ProductOptionRepository;
use App\Repositories\Product\ProductOptionValueRepository;
use App\Repositories\Product\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

readonly class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductOptionRepository $productOptionRepository,
        private ProductOptionValueRepository $productOptionValueRepository,
    ) {
    }

    /**
     * @return int[]
     * @throws Exception
     */
    public function getIds(): array
    {
        Log::debug("ProductService::getIds method called");
        try {
            return $this->productRepository->getIds();
        } catch (Exception $exception) {
            Log::error("ProductService::getIds method fell into error", ['exception' => $exception]);

            DB::rollBack();

            throw new Exception("ProductService::getIds method fell into error: {$exception->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public function getCount(GetProductsFilterDTO $DTO): int
    {
        Log::debug("ProductService::getCount method called", ['filters' => $DTO->toArray()]);
        try {
            if ($DTO->properties) {
                return $this->productOptionRepository->getCountProductIdsByFilters($DTO->properties);
            } else {
                return $this->productRepository->getCount();
            }

        } catch (Exception $exception) {
            Log::error("ProductService::getCount method fell into error",
                ['exception' => $exception, 'filters' => $DTO->toArray()]);

            DB::rollBack();

            throw new Exception("ProductService::getCount method fell into error: {$exception->getMessage()}");
        }
    }

    /**
     * @return ProductDTO[]
     * @throws Exception
     */
    public function get(GetProductsFilterDTO $DTO): array
    {
        Log::debug("ProductService::get method called", ['filters' => $DTO->toArray()]);
        try {
            if ($DTO->properties) {
                $productIds = $this->productOptionRepository->getProductIdsByFilters($DTO->properties);

                if ($productIds) {
                    $products = $this->productRepository->getFiltered($DTO, $productIds);
                } else {
                    return [];
                }
            } else {
                $products = $this->productRepository->getFiltered($DTO);
                $productIds = array_map(fn($item) => $item->id, $products);
            }

            $options = $this->getOptionsByProductIds($productIds);
            $optionIds = array_map(fn($item) => $item->id, $options);

            $values = $this->getOptionsValuesByProductOptionIds($optionIds);

            foreach ($products as $product) {
                $useOptions = array_map(function ($option) use ($product, $values) {
                    if ($option->productId === $product->id) {
                        $option->values = array_filter($values, function ($item) use ($option) {
                            return $item->productOptionId === $option->id;
                        });
                        return $option;
                    }
                    return null;
                }, $options);
                $product->options = array_filter($useOptions);
            }
            return $products;
        } catch (Exception $exception) {
            Log::error("ProductService::get method fell into error",
                ['exception' => $exception, 'filters' => $DTO->toArray()]);

            DB::rollBack();

            throw new Exception("ProductService::get method fell into error: {$exception->getMessage()}");
        }
    }

    /**
     * @return ProductOptionDTO[]
     * @throws Exception
     */
    public function getOptionsByProductIds(array $ids): array
    {
        Log::debug("ProductService::getOptionsByProductIds method called", ['productIds' => $ids]);
        try {
            return $this->productOptionRepository->getByProductIds($ids);
        } catch (Exception $exception) {
            Log::error("ProductService::getOptionsByProductIds method fell into error",
                ['exception' => $exception, ['productIds' => $ids]]);

            DB::rollBack();

            throw new Exception("ProductService::getOptionsByProductIds method fell into error: {$exception->getMessage()}");
        }
    }

    /**
     * @return ProductOptionValueDTO[]
     * @throws Exception
     */
    public function getOptionsValuesByProductOptionIds(array $ids): array
    {
        Log::debug("ProductService::getOptionsValuesByProductOptionIds method called", ['productOptionIds' => $ids]);
        try {
            return $this->productOptionValueRepository->getByOptionIds($ids);
        } catch (Exception $exception) {
            Log::error("ProductService::getOptionsValuesByProductOptionIds method fell into error",
                ['exception' => $exception, ['productOptionIds' => $ids]]);

            DB::rollBack();

            throw new Exception("ProductService::getOptionsValuesByProductOptionIds method fell into error: {$exception->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public function addOption(int $productId, string $name): ProductOptionDTO
    {
        $logData = [
            'productId' => $productId,
            'name'      => $name,
        ];
        Log::debug("ProductService::addOption method called", $logData);

        try {
            DB::beginTransaction();

            $option = $this->productOptionRepository->add($productId, $name);

            DB::commit();
            Log::info("App option to product", $logData);

            return $option;
        } catch (Exception $exception) {
            Log::error("ProductService::addOption method fell into error", ['exception' => $exception] + $logData);

            DB::rollBack();
            throw new Exception("ProductService::addOption method fell into error: {$exception->getMessage()}");
        }
    }

    /**
     * @throws Exception
     */
    public function addOptionValue(int $productOptionId, string $value): ProductOptionValueDTO
    {
        $logData = [
            'productOptionId' => $productOptionId,
            'value'           => $value,
        ];
        Log::debug("ProductService::addOptionValue method called", $logData);

        try {
            DB::beginTransaction();

            $optionValue = $this->productOptionValueRepository->add($productOptionId, $value);

            DB::commit();

            Log::info("App option value ", $logData);

            return $optionValue;
        } catch (Exception $exception) {
            Log::error("ProductService::addOptionValue method fell into error", ['exception' => $exception] + $logData);

            DB::rollBack();
            throw new Exception("ProductService::addOptionValue method fell into error: {$exception->getMessage()}");
        }
    }


    /**
     * @throws Exception
     */
    public function updateOrCreate(ProductDTO $DTO): ProductDTO
    {
        Log::debug("ProductService::updateOrCreate method called", ['product' => $DTO->toArray()]);
        try {
            DB::beginTransaction();

            $product = $this->productRepository->updateOrCreate($DTO);

            DB::commit();

            Log::info("Product update or create ", ['product' => $product]);

            return $product;
        } catch (Exception $exception) {
            Log::error("ProductService::updateOrCreate method fell into error",
                ['exception' => $exception, 'product' => $DTO->toArray()]);

            DB::rollBack();
            throw new Exception("ProductService::updateOrCreate method fell into error: {$exception->getMessage()}");
        }
    }
}
