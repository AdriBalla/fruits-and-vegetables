# 🍎🥕 Fruits and Vegetables


![Image](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Image](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white) 
![Image](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)
![Image](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=Symfony&logoColor=white)
![Image](https://img.shields.io/badge/Sqlite-003B57?style=for-the-badge&logo=sqlite&logoColor=white)

## 📚 Stack specification 
- PHP 8.2
- Symfony 7
- Mysql
- Docker Compose
- PhpUnit 10
- SQLite

## 🐳 Docker images
This project uses two container : 
- A php container for running the Symfony application
- A Mysql container for storage

You will first have to start the container
```bash
docker-compose up -d
```

### 🧱 Starting Symfony 
In order to start the Symfony application, we will need to install all the dependencies, generate the database, migrations and run the server

With the stack running (see previous step above), we will need to go inside the Symfony container
```bash
docker exec -ti fv_app sh
```

Then run these commands 
```
composer install;
php bin/console doctrine:migrations:migrate;
```

You can then exit the container and start the Symfony server from outside your docker with the command 
```bash
docker exec -ti fv_app php -S 0.0.0.0:8080 -t public;
```

The Symfony server will be running on `localhost:8080`

### 📥 Importing request.json
In order to populate the database with the provided request.json.
With the stack running, you can use the command line below from outside your docker


```bash
docker exec -ti fv_app bin/console app:ingest:file request.json -vv
```

This command has a truncate option if you want to flush the database before importing the file

```bash
docker exec -ti fv_app bin/console app:ingest:file --truncate request.json -vv
```

### ⚡ Apis
This project provides two endpoints that are deployed on port `8080`
- `/fruits`
- `/vegetables`

The api for fruit and vegetable have similar behaviour. The documentation below explain how to use them.

### 🍎 Fruit Api 

| Route        | Method | Description                                      |
|:-------------|:------:|--------------------------------------------------|
| /fruits      |  GET   | Returns elements of the fruit collection         |
| /fruits      |  POST  | Add a fruit to the fruit collection              |
| /fruits/{id} | DELETE | Remove fruit from the collection based on its id |

#### [GET] /fruits

This endpoint returns all elements of the fruit collection.
It is possible to choose the unit that will be used in the returned fruit as well as the pagination and the page size of the returned result.

Parameters are as followed : 

| Parameters |  Type  | Description                              |
|:-----------|:------:|------------------------------------------|
| unit       | string | The unit chosen for conversion (g or kg) |
| page       |  int   | The page of the collection               |
| size       |  int   | Number of fruits per page                |


#### [POST] /fruits

This endpoint allow to add a fruit to the fruit collection

#### Parameters are as followed :

| Parameters |     Type     | Description                        |
|:-----------|:------------:|------------------------------------|
| name       |    string    | The name of the fruit              |
| quantity   | int or float | Quantity of the fruit              |
| unit       |    string    | The unit of the quantity (g or kg) |

#### Example of input : 

```json
{
    "name": "Apple",
    "quantity" : 1234,
    "unit":"g"
}
```

#### Example of output :

```json
{
    "name": "Apple",
    "quantity" : 1234,
    "unit":"g",
    "id" : 11
}
```
#### [DELETE] /fruits/{id}

This endpoint allows you to remove a fruit from the fruit collection based on its id.
The response code will be `204` if the deletion is successful and `404`otherwise.

## 🥕 Vegetable Api

| Route        | Method | Description                                      |
|:-------------|:------:|--------------------------------------------------|
| /vegetables      |  GET   | Returns elements of the vegetable collection         |
| /vegetables      |  POST  | Add a vegetable to the vegetable collection              |
| /vegetables/{id} | DELETE | Remove vegetable from the collection based on its id |

#### [GET] /vegetables

This endpoint returns all elements of the vegetable collection.
It is possible to choose the unit that will be used in the returned vegetable as well as the pagination and the page size of the returned result.

Parameters are as followed :

| Parameters |  Type  | Description                              |
|:-----------|:------:|------------------------------------------|
| unit       | string | The unit chosen for conversion (g or kg) |
| page       |  int   | The page of the collection               |
| size       | DELETE | Number of vegetables per page                |


#### [POST] /vegetables

This endpoint allow to add a vegetable to the vegetable collection

#### Parameters are as followed :

| Parameters |     Type     | Description                        |
|:-----------|:------------:|------------------------------------|
| name       |    string    | The name of the vegetable              |
| quantity   | int or float | Quantity of the vegetable              |
| unit       |    string    | The unit of the quantity (g or kg) |

#### Example of input :

```json
{
    "name": "Beans",
    "quantity" : 1234,
    "unit":"g"
}
```

#### Example of output :

```json
{
    "name": "Beans",
    "quantity" : 1234,
    "unit":"g",
    "id" : 11
}
```
#### [DELETE] /vegetables/{id}

This endpoint allows you to remove a vegetable from the vegetable collection based on its id.
The response code will be `204` if the deletion is successful and `404`otherwise.

### ✔️ Running tests

This project uses PHPUnit 10 and sqlite for testing. 
With the stack running, these tests are available from the terminal with this outside your running docker with this command :

```bash
docker exec -ti fv_app vendor/bin/phpunit
```

### ⚙️ Development tool

This project uses Php-Cs-Fixer and PhpStan to provide better code quality.
You can use these tools when the docker are up with these commands :

```bash
docker exec -ti fv_app vendor/bin/php-cs-fixer fix
```

```bash
docker exec -ti fv_app vendor/bin/phpstan analyse src tests
```

### 🌱 Possible next steps and improvements

- Implementation of a search engine (storage based or Elasticsearch based)
- Implementation of bulk insertion for heavy file ingestion 
- Implementation of cache with Redis to improve list response time