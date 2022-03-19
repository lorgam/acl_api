# Test API

## Configuration

The API runs locally on docker compose containers and makes use of make commands to control the containers

### Commands for the development machines
- `make start` to start the development machines
- `make stop` to stop the development machines
- `make restart` to restart the development machines
- `make ssh` to log into the php server command line
- `make sql` to log into the database command line
- `make logs` to read the logs
- `make xdebug` to enable xdebug
- `make phpv` to check the php version installed

### Commands for the symfony environment
- `make sf-routes` to show the routes Symfony has registered
- `make sf-env` to show the environment variables in use
- `make sf-params` to show the parameters in use
- `make sf-load-fixtures` to load the local data for development

The file named `.env` in the root folder has the environment variables for the development machines

Access the web server at `http://${PROJECT_NAME}.localhost/` and the router panel at [`http://localhost:8080/`](http://localhost:8080/).

The code for the api is located in the api folder

## Stack

Software | Version
--- | ---
FPM | 8.0
Symfony | 4.4
Nginx | 1.21
MariaDB | 10.6
Traefik | 2.5
XDebug |

