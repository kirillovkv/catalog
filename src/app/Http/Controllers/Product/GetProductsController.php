<?php

namespace app\Http\Controllers\Product;

use App\DTO\Filter\GetProductsFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\GetProductsRequest;
use App\Http\Resources\ProductResource;
use App\Services\Product\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Получение списка товаров с фильтрацией по опциям",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="Цвет",
 *         in="query",
 *         description="Фильтр по цвету (например: Цвет=Красный&Цвет=Синий)",
 *         required=false,
 *         @OA\Schema(type="array", @OA\Items(type="string"))
 *     ),
 *     @OA\Parameter(
 *         name="Материал",
 *         in="query",
 *         description="Фильтр по материалу (например: Материал=Дерево)",
 *         required=false,
 *         @OA\Schema(type="array", @OA\Items(type="string"))
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Номер страницы",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Product")
 *             ),
 *             @OA\Property(
 *                 property="count",
 *                 type="integer",
 *                 example=100
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Internal Server Error")
 *         )
 *     )
 * )
 */
class GetProductsController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(GetProductsRequest $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $filter = new GetProductsFilterDTO($request->validated());
            $data = $this->productService->get($filter);
            $count = $this->productService->getCount($filter);

            return ProductResource::collection($data)->additional(['count' => $count]);
        } catch (Exception $exception) {
            Log::error("GetProductsController fell into error: {$exception->getMessage()}", ['exception' => $exception]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
