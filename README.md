# Test API

## Configuration

The API runs locally on docker compose containers and makes use of make commands to control the containers, start the machine with `make start` and load the database with `make install`
Access the web server at [`http://acl_api.localhost/`](http://acl_api.localhost/) and the router panel at [`http://localhost:8080/`](http://localhost:8080/)

The file named `.env` in the root folder has the environment variables for the development machines, the variable named `PROJECT_NAME` contains the project name and is used for the local URL of the development server
The variables whose value is `OverrideMeInLocal` like `EXCHANGERATES_API_KEY` need to be configured in the .env.local file
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

## Stack

Software | Version
--- | ---
FPM | 8.0
Symfony | 4.4
Nginx | 1.21
MariaDB | 10.6
Traefik | 2.5
NodeJs | 14.16.1
Newman |
XDebug |

