## Quick start:

### Start nginx container without composer ... only containers that "depends_on":
- `docker-compose up app -d` ("app" is the alias of nginx container that runs all depending on services)

#### Install composer dependencies
- `docker-compose run --rm composer install`

#### Run phpunit test:
- `docker-compose run --rm phpunit`

#### Run commissions calculation:
- `docker-compose exec php php app.php`

### Note: There are some over-engineered features that are out of scope for this assignment (made intentionally)
- Logging system
- Dicker infrastructure for local development
- Etc.

