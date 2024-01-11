<?php

namespace App\Controller;

use App\Controller\Handler\ProductHandler;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    private const ERROR_EMPTY_BODY = 'Request body must not be empty';
    private const ERROR_EMPTY_PARAMETER = 'Parameter must not be empty';

    public function __construct(
        private readonly ProductService $productService,
        private readonly ProductHandler $productHandler
    )
    {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->findAll();

        if (empty($products)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->productHandler->toJson($products), Response::HTTP_OK, [], true);
    }

    public function show(string $sku): JsonResponse
    {
        $product = $this->productService->findOneBySku($sku);

        if (is_null($product)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->productHandler->toJson([$product]), Response::HTTP_OK, [], true);

    }

    public function create(Request $request): JsonResponse
    {
        $content = $request->toArray();

        if (empty($content)) {
            return new JsonResponse(self::ERROR_EMPTY_BODY, Response::HTTP_BAD_REQUEST);
        }

        try {
            $products = $this->productHandler->fromArrayInput($content);
            $this->productService->create($products);

            return new JsonResponse('Records loaded successfully', Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return new JsonResponse($this->productHandler->toOutputFromException($e), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $content = $request->toArray();

        if (empty($content)) {
            return new JsonResponse(self::ERROR_EMPTY_BODY, Response::HTTP_BAD_REQUEST);
        }

        try {
            $products = $this->productHandler->fromArrayInput($content);
            $this->productService->update($products);

            return new JsonResponse('Records updated successfully', Response::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse($this->productHandler->toOutputFromException($e), Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateOne(Request $request, ?string $sku = null): JsonResponse
    {
        $content = $request->toArray();

        if (empty($content)) {
            return new JsonResponse(self::ERROR_EMPTY_BODY, Response::HTTP_BAD_REQUEST);
        }

        if (empty($sku)) {
            return new JsonResponse(self::ERROR_EMPTY_PARAMETER, Response::HTTP_BAD_REQUEST);
        }

        try {
            $product = $this->productHandler->fromInput($content);
            $this->productService->updateOneBySku($product, $sku);

            return new JsonResponse('Record updated successfully', Response::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse($this->productHandler->toOutputFromException($e), Response::HTTP_BAD_REQUEST);
        }
    }
}