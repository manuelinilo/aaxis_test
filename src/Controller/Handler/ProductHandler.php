<?php

namespace AaxisTest\Controller\Handler;

use AaxisTest\Entity\Product;
use AaxisTest\Exception\InvalidEntityException;
use AaxisTest\Exception\InvalidRequestException;
use Particle\Validator\Failure;
use Particle\Validator\Validator;

class ProductHandler
{
    private const DEFAULT_ERROR_MESSAGE = [
        'error' => 'An error has occurred'
    ];

    /**
     * @throws InvalidRequestException
     * @return Product[]
     */
    public function fromArrayInput(array $data): array
    {
        $products = [];
        foreach ($data as $productData) {
            $products[] = $this->validateInput($productData);
        }

        return $products;
    }

    /**
     * @throws InvalidRequestException
     */
    public function fromInput(array $data): Product
    {
        return $this->validateInput($data);
    }

    public function toOutputFromException(\Throwable $exception): array
    {

        if ($exception instanceof(InvalidRequestException::class)) {
            $outputErrors = [];

            /** @var Failure $error */
            foreach ($exception->getErrors() as $error) {
                $outputErrors['error'] = [
                    'sku' => $exception->getIdentifier(),
                    'field' => $error->getKey(),
                    'reason' => $error->format(),
                ];
            }

            return $outputErrors;
        }

        if ($exception instanceof(InvalidEntityException::class)) {
            /** @var InvalidEntityException $exception */
            return $exception->getErrors();
        }

        return self::DEFAULT_ERROR_MESSAGE;

    }

    public function toJson(array $products): string|false
    {
        $jsonData = [];

        /** @var Product $product */
        foreach ($products as $product) {
            $jsonData[] = [
                'sku' => $product->getSku(),
                'product_name' => $product->getProductName(),
                'description' => $product->getDescription(),
            ];
        }

        return json_encode($jsonData);
    }

    private function validateInput(array $input): Product
    {
        $validator = new Validator();
        $validator->required('sku')->string()->lengthBetween(1, 50);
        $validator->required('product_name')->string()->lengthBetween(1, 250);
        $validator->optional('description')->string()->allowEmpty(false);

        $result = $validator->validate($input);

        if ($result->isNotValid()) {
            throw new InvalidRequestException($result->getFailures(), $input['sku'] ?? null);
        }

        $product = new Product();
        $product->setSku($input['sku']);
        $product->setProductName($input['product_name']);
        $product->setDescription($input['description'] ?? null);

        return $product;
    }

}