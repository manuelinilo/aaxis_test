<?php

namespace AaxisTest\Tests\Controller\Handler;

use AaxisTest\Controller\Handler\ProductHandler;
use AaxisTest\Entity\Product;
use AaxisTest\Exception\InvalidRequestException;
use PHPUnit\Framework\TestCase;

class ProductHandlerTest extends TestCase
{
    public function testIsValidatingMultipleSkus(): void
    {
        $input = [
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

        $product1 = new Product();
        $product1->setSku('JJJJ-11');
        $product1->setProductName('Product 11');
        $product1->setDescription('This is a test product');

        $product2 = new Product();
        $product2->setSku('BBBB-22');
        $product2->setProductName('Product 2');
        $product2->setDescription('This is a test product');

        $product3 = new Product();
        $product3->setSku('HHHH-99');
        $product3->setProductName('Product 9');
        $product3->setDescription('This is a test product');

        $expected = [$product1, $product2, $product3];

        $handler = new ProductHandler();

        $actual = $handler->fromArrayInput($input);

        $this->assertEquals($expected, $actual);
    }

    public function testIsValidatingSingleSku(): void
    {
        $input = [
            'sku' => 'HHHH-99',
            'product_name' => 'Product 9',
            'description' => 'This is a test product'
        ];

        $expected = new Product();
        $expected->setSku('HHHH-99');
        $expected->setProductName('Product 9');
        $expected->setDescription('This is a test product');

        $handler = new ProductHandler();

        $actual = $handler->fromInput($input);

        $this->assertEquals($expected, $actual);
    }

    public function testIsValidatingMultipleSkusWithoutOptionalField(): void
    {
        $input = [
            [
                'sku' => 'JJJJ-11',
                'product_name' => 'Product 11',
            ],
            [
                'sku' => 'BBBB-22',
                'product_name' => 'Product 2',
                'description' => 'This is a test product'
            ],
            [
                'sku' => 'HHHH-99',
                'product_name' => 'Product 9',
            ]
        ];

        $product1 = new Product();
        $product1->setSku('JJJJ-11');
        $product1->setProductName('Product 11');
        $product1->setDescription(null);

        $product2 = new Product();
        $product2->setSku('BBBB-22');
        $product2->setProductName('Product 2');
        $product2->setDescription('This is a test product');

        $product3 = new Product();
        $product3->setSku('HHHH-99');
        $product3->setProductName('Product 9');
        $product3->setDescription(null);

        $expected = [$product1, $product2, $product3];

        $handler = new ProductHandler();

        $actual = $handler->fromArrayInput($input);

        $this->assertEquals($expected, $actual);
    }

    public function testIsValidatingSingleSkuWithoutOptionalField(): void
    {
        $input = [
            'sku' => 'HHHH-99',
            'product_name' => 'Product 9'
        ];

        $expected = new Product();
        $expected->setSku('HHHH-99');
        $expected->setProductName('Product 9');
        $expected->setDescription(null);

        $handler = new ProductHandler();

        $actual = $handler->fromInput($input);

        $this->assertEquals($expected, $actual);
    }

    public function testIsNotValidatingMultipleSkusOnMissingSku(): void
    {
        $input = [
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
                'product_name' => 'Product 9',
                'description' => 'This is a test product'
            ]
        ];

        $handler = new ProductHandler();

        $this->expectException(InvalidRequestException::class);

        $handler->fromArrayInput($input);
    }

    public function testIsNotValidatingSingleSkuOnMissingSku(): void
    {
        $input = [
            'product_name' => 'Product 9',
            'description' => 'This is a test product'
        ];

        $handler = new ProductHandler();

        $this->expectException(InvalidRequestException::class);

        $handler->fromInput($input);
    }
}