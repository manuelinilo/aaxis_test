<?php

namespace AaxisTest\Tests\Service;

use AaxisTest\Entity\Product;
use AaxisTest\Exception\InvalidEntityException;
use AaxisTest\Repository\ProductRepository;
use AaxisTest\Service\ProductService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ProductServiceTest extends TestCase
{
    use ProphecyTrait;

    public function testsIsFindingAll(): void
    {
        $products = [
            [
                'sku' => 'JJJJ-11',
                'product_name' => 'Product 11',
                'description' => 'This is a test product'
            ],
            [
                'sku' => 'BBBB-22',
                'product_name' => 'Product 2',
                'description' => 'This is a test product'
            ],
            [
                'sku' => 'HHHH-99',
                'product_name' => 'Product 9',
                'description' => 'This is a test product'
            ]
        ];

        $em = $this->prophesize(EntityManager::class);
        $repository = $this->prophesize(ProductRepository::class);
        $repository
            ->findAll()
            ->shouldBeCalled()
            ->willReturn($products);

        $service = new ProductService($em->reveal(), $repository->reveal());
        $this->assertEquals($products, $service->findAll());
    }

    public function testIsFindingOneBySku(): void
    {
        $product = new Product();
        $product->setSku('HHHH-99');
        $product->setProductName('Product 9');
        $product->setDescription('This is a test product');

        $em = $this->prophesize(EntityManager::class);
        $repository = $this->prophesize(ProductRepository::class);
        $repository
            ->findOneBy(['sku' => $product->getSku()])
            ->shouldBeCalled()
            ->willReturn($product);

        $service = new ProductService($em->reveal(), $repository->reveal());
        $this->assertEquals($product, $service->findOneBySku($product->getSku()));
    }

    public function testIsCreating(): void
    {
        $product1 = new Product();
        $product1->setSku('HHHH-99');
        $product1->setProductName('Product 9');
        $product1->setDescription('This is a test product');

        $product2 = new Product();
        $product2->setSku('AAAA-11');
        $product2->setProductName('Product 1');
        $product2->setDescription('This is a test product');

        $products = [$product1, $product2];
        $em = $this->prophesize(EntityManager::class);
        $repository = $this->prophesize(ProductRepository::class);

        $em
            ->beginTransaction()
            ->shouldBeCalled();
        $em
            ->persist($product1)
            ->shouldBeCalled();
        $em
            ->persist($product2)
            ->shouldBeCalled();
        $em
            ->flush()
            ->shouldBeCalledTimes(2);
        $em
            ->rollback()
            ->shouldNotBeCalled();

        $service = new ProductService($em->reveal(), $repository->reveal());
        $this->assertTrue($service->create($products));
    }

    public function testIsThrowingExceptionOnUpdateWithOrmFailure(): void
    {
        $product1 = new Product();
        $product1->setSku('HHHH-99');
        $product1->setProductName('Product 9');
        $product1->setDescription('This is a test product');

        $product2 = new Product();
        $product2->setSku('AAAA-11');
        $product2->setProductName('Product 1');
        $product2->setDescription('This is a test product');

        $products = [$product1, $product2];
        $em = $this->prophesize(EntityManager::class);
        $repository = $this->prophesize(ProductRepository::class);

        $em
            ->beginTransaction()
            ->shouldBeCalled();
        $em
            ->persist($product1)
            ->shouldBeCalled();
        $em
            ->persist($product2)
            ->shouldBeCalled()
            ->willThrow(ORMException::class);
        $em
            ->flush()
            ->shouldBeCalledTimes(1);
        $em
            ->rollback()
            ->shouldBeCalled();

        $service = new ProductService($em->reveal(), $repository->reveal());
        $this->expectException(InvalidEntityException::class);
        $service->create($products);
    }
}