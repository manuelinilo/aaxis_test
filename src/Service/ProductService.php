<?php

namespace AaxisTest\Service;

use AaxisTest\Entity\Product;
use AaxisTest\Exception\InvalidEntityException;
use AaxisTest\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductRepository $productRepository
    )
    {
    }

    /**
     * @return Product[]
     */
    public function findAll(): array
    {
        return $this->productRepository->findAll();
    }

    public function findOneBySku(string $sku): Product|null
    {
        return $this->productRepository->findOneBy(['sku' => $sku]);
    }

    /**
     * @throws InvalidEntityException
     */
    public function create(array $products): bool
    {
        $this->entityManager->beginTransaction();
        /** @var Product $product */
        foreach ($products as $product) {
            $product->setCreatedAt(new \DateTimeImmutable());

            try {
                $this->entityManager->persist($product);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $error['error'] = [
                    'sku' => $product->getSku(),
                    'reason' => $e->getMessage()
                ];

                $this->entityManager->rollback();

                throw new InvalidEntityException($error, $e->getMessage());
            }
        }

        return true;
    }

    /**
     * @throws InvalidEntityException
     */
    public function updateOneBySku(Product $product, string $sku): bool
    {
        $record = $this->findOneBySku($sku);

        if (is_null($record)) {
            $record = $product;
            $record->setCreatedAt(new \DateTimeImmutable());
        } else {
            $record->setSku($product->getSku());
            $record->setProductName($product->getProductName());
            $record->setDescription($product->getDescription());
            $record->setUpdatedAt(new \DateTimeImmutable());
        }

        try {
            $this->entityManager->persist($record);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $error['error'] = [
                'sku' => $product->getSku(),
                'reason' => $e->getMessage()
            ];

            throw new InvalidEntityException($error, $e->getMessage());
        }

        return true;
    }

    /**
     * @throws InvalidEntityException
     */
    public function update(array $products): bool
    {
        $this->entityManager->beginTransaction();
        /** @var Product $product */
        foreach ($products as $product) {
            $record = $this->findOneBySku($product->getSku());

            if (is_null($record)) {
                $record = $product;
                $record->setCreatedAt(new \DateTimeImmutable());
            } else {
                $record->setSku($product->getSku());
                $record->setProductName($product->getProductName());
                $record->setDescription($product->getDescription());
                $record->setUpdatedAt(new \DateTimeImmutable());
            }

            try {
                $this->entityManager->persist($record);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $error['error'] = [
                    'sku' => $product->getSku(),
                    'reason' => $e->getMessage()
                ];

                $this->entityManager->rollback();

                throw new InvalidEntityException($error, $e->getMessage());
            }
        }

        $this->entityManager->commit();

        return true;
    }
}