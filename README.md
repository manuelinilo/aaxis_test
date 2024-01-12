# AaxisTest - Basic Technical Test

## Installation

To install this project, you must clone the GitHub repository and install required dependencies

```bash
git clone https://github.com/manuelinilo/aaxis_test.git
cd aaxis_test
composer install
```

Add the following extensions to your ***php.ini*** configuration file (if needed) and then restart the webserver:
```file
extension=pdo_pgsql
extension=pgsql
```

## Usage
- To start the webserver:

```bash
symfony serve
```

- To clear Symfony cache

```bash
php bin/console cache:clear
```

## Details

This project consist on a simple products API with follows API RESTful standards.

Endpoint ***/products***:


| Method | Endpoint        | Description                                                            |
|--------|-----------------|------------------------------------------------------------------------|
| GET    | /products       | list all products                                                      |
| GET    | /products/{sku} | list a product identified by the sku parameter                         |
| POST   | /products       | Create one or many products given a Json payload                       |
| PUT    | /products       | Update one or many products given a Json payload                       |
| PUT    | /products/{sku} | Update a product given a Json payload, identified by the sku parameter |


### Json payload

- POST - PUT /products
```json
[
  {
    "sku": "JJJJ-11",
    "product_name": "Product 11",
    "description": "This is a test product"
  },
  {
    "sku": "BBBB-22",
    "product_name": "Product 2",
    "description": "This is a test product"
  },
  {
    "sku": "HHHH-99",
    "product_name": "Product 9",
    "description": "This is a test product"
  }
]
```

- PUT /products/{sku}
```json
{
  "sku": "GGGG-88",
  "product_name": "Product 8",
  "description": "Updated description"
}
```

### Security
This application is protected with ***Basic Auth***. User and hashed password are stored in the ***.env*** file
as ***API_USER*** and ***API_PASSWORD_HASH***. The password is hashed using ***bcrypt***.

- Provided test credentials: 
  - user: aaxis
  - password: aaxis


