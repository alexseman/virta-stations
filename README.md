### Installation

- `docker compose build`
- `docker compose up`
- `docker compose run --rm api composer install --no-interaction`
- (entering the API container) `docker exec -it virta_api bash`
- `cp .env.example .env`
- `php artisan migrate && php artisan db:seed && php artisan cache:clear`
- the UI can be accessed at `http://localhost:4040/`
- the API is available at `http://localhost:4040/api`
- there is also the small Node app which handles the stations by companies endpoint (`GET /api/stations/by-company`)
- the API documentation is available as JSON at `http://localhost:4040/api/docs` & as a view at `http://localhost:4040/api/docs/documentation`
- in the root of the repo there is also an importable Postman collection


### Rough Edges & Things to be Improved
- there are **a lot** and please pardon me that I will not elaborate on that in this very moment as it well past 2 in the morning here.