<?php

namespace App\Controller\Handler;

use App\Entity\Product;
use App\Exception\InvalidEntityException;
use App\Exception\InvalidRequestException;
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
            $validator = new Validator();
            $validator->required('sku')->string()->lengthBetween(1, 50);
            $validator->required('product_name')->string()->lengthBetween(1, 250);
            $validator->optional('description')->string()->allowEmpty(false);

            $result = $validator->validate($productData);

            if ($result->isNotValid()) {
                throw new InvalidRequestException($result->getFailures(), $productData['sku']);
            }

            $product = new Product();
            $product->setSku($productData['sku']);
            $product->setProductName($productData['product_name']);
            $product->setDescription($productData['description'] ?? null);

            $products[] = $product;
        }

        return $products;
    }

    /**
     * @throws InvalidRequestException
     */
    public function fromInput(array $data): Product
    {
        $validator = new Validator();
        $validator->required('sku')->string()->lengthBetween(1, 50);
        $validator->required('product_name')->string()->lengthBetween(1, 250);
        $validator->optional('description')->string()->allowEmpty(false);

        $result = $validator->validate($data);

        if ($result->isNotValid()) {
            throw new InvalidRequestException($result->getFailures());
        }

        $product = new Product();
        $product->setSku($data['sku']);
        $product->setProductName($data['product_name']);
        $product->setDescription($data['description'] ?? null);

        return $product;
    }

    public function toErrorOutput(array $errors): array
    {
        $outputErrors = [];

        /** @var Failure $error */
        foreach ($errors as $error) {
            $outputErrors[] = [
                'field' => $error->getKey(),
                'reason' => $error->format(),
            ];
        }

        return $outputErrors;
    }

    public function toOutputFromException(\Exception $exception): array
    {

        if ($exception instanceof(InvalidRequestException::class)) {
            $outputErrors = [];

            /**
             * @var Failure $error
             * @var InvalidRequestException $exception
             */
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

    public function toJson(array $products): string
    {
        $jsonData = [];

        /** @var Product $product */
        foreach ($products as $product) {
            $jsonData[] = [
                // remove after development
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'product_name' => $product->getProductName(),
                'description' => $product->getDescription(),
            ];
        }

        return json_encode($jsonData);
    }

}