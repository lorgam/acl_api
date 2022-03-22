# Test API

## Configuration

The API runs locally on docker compose containers and makes use of make commands to control the containers, start the machine with `make start` and load the database with `make install`

Access the web server at [`http://acl_api.localhost/`](http://acl_api.localhost/) and the router panel at [`http://localhost:8080/`](http://localhost:8080/)

The file named `.env` in the root folder has the environment variables for the development machines, the variable named `PROJECT_NAME` contains the project name and is used for the local URL of the development server
The variables whose value is `OverrideMeInLocal` like `EXCHANGERATES_API_KEY` need to be configured in a .env.local file inside the same folder of his .env file
The code for the api is located in the api folder

### Commands for the development machines
- `make start` to start the development machines
- `make stop` to stop the development machines
- `make restart` to restart the development machines
- `make ssh` to log into the php server command line
- `make sql` to log into the database command line
- `make logs` to read the logs
- `make xdebug` to enable xdebug
- `make phpv` to check the php version installed
- `make install` to install composer packages and load the database with test data
- `make c-install` to install composer packages
- `make tests` to execute the tests
- `make newman` to execute the Postman tests

### Commands for the symfony environment
- `make sf-routes` to show the routes Symfony has registered
- `make sf-env` to show the environment variables in use
- `make sf-params` to show the parameters in use
- `make sf-load-fixtures` to load the local data for development

## API Description

The API uses JSON representation for the items of the API, you have to use an Accept Header compatible with `application/json`.
As an API REST it uses the HTTP verbs to define the behaviour of the API, `GET` for reading items, `POST` to create items, `DELETE` to delete items and `PUT` to update items.
When a parameter is obligatory it is mandatory only on the `POST` request, the `PUT` request allows you to pudate only certain parts of the object

### Entities

#### Category

JSON representation:
<pre>
{
    "id": int,
    "name": string,
    "description": string
}
</pre>

`name` and `description` are mandatory

Endpoints
- `/api/v1/category` Methods accepted:
  - `GET` Returns an array of all the categories
  - `POST` Creates the category and returns the category, if it has a problem with the data returns a 400 response
- `/api/v1/category/{id}` if the category is not found it will return a 204 response. Methods accepted:
  - `GET` Returns the category
  - `DELETE` Deletes the category, returns a the current JSON if everything has gone right `{"deleted": true}`
  - `PUT` Updates the category and returns the data of the category updated

#### Product

JSON representation:
<pre>
{
    "id": int,
    "name": string,
    "category": Category,
    "price": float,
    "currency": string,
    "featured": boolean
}
</pre>

`name`, `price`, `currency` and `featured` are mandatory, `currency` can only take the values `EUR` o `USD`, in the methods `POST` and `PUT` `category` is an object only needs the id of the category as a parameter

Endpoints
- `/api/v1/product` Methods accepted:
  - `GET` Returns an array of all the products
  - `POST` Creates the product and returns the product, if it has a problem with the data returns a 400 response
- `/api/v1/product/{id}` if the product is not found it will return a 204 response. Methods accepted:
  - `GET` Returns the product
  - `DELETE` Deletes the product, returns a the current JSON if everything has gone right `{"deleted": true}`
  - `PUT` Updates the product and returns the data of the product updated
- `/api/v1/product/featured?currency={currency}` `currency` is an optional parameter that can only be `EUR` or `USD`. Methods accepted:
    - `GET` Returns an array of featured products, with the currencies converted to the currency specified by the `currency` paramater

## Stack

Software | Version
--- | ---
FPM | 8.0
Symfony | 4.4
Nginx | 1.21
MariaDB | 10.6
Traefik | 2.5
Node | 14.16.1
Newman |
XDebug |

## Pending
- Endpoint to obtain the products associated with a category
- Automate tests for the commit command
- Authentication
- Better documentation (Maybe use nelmio/api-doc-bundle)
- OPTIONS method
- CORS Headers
- Pagination
