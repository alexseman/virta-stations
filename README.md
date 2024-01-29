### Requirements
- One charging company can own one or more other charging companies.
  Hence, the parent company should have access to all its children companies' stations, hierarchically. For example, we have 3 companies A, B, and C owning respectively 10, 5, and 2 charging stations. Company B belongs to A and Company C belongs to B.
  Then we can say that company A owns 17, Company B owns 7, and Company C owns 2 charging stations in total.
- The API should support CRUD for both stations and companies.
- Within the radius of n kilometers from a point (latitude, longitude), your station list is ordered by increasing distance, and stations in the same location are grouped.
  - The list includes all the children's stations in the tree, for the given company_id.
- React.js app with TypeScript that shows a list of Stations with their details.
- Node.js app that connects to the above API and fetches the Station data and groups the Stations by Company.


### Installation

- `docker compose build`
- `docker compose up`
- `docker compose run --rm api composer install --no-interaction`
- (entering the API container) `docker exec -it virta_api bash`
- `cp .env.example .env`
- seeding the default & test DBs:
  - ```
    php artisan migrate &&\     
    php artisan db:seed &&\
    php artisan migrate --database pgsql_test &&\
    php artisan db:seed --database pgsql_test &&\
    php artisan cache:clear
    ```
- the UI can be accessed at `http://localhost:4040/`
- the API is available at `http://localhost:4040/api`
- there is also the small Node app which handles the stations by companies endpoint (`GET /api/stations/by-company`)
- the API documentation is available as JSON at `http://localhost:4040/api/docs` & as a view at `http://localhost:4040/api/docs/documentation`
- in the root of the repo there is also an importable Postman collection
- available commands:
  - linting: `./vendor/bin/pint`
  - static code analysis: `./vendor/bin/phpstan --memory-limit=1G`
  - tests: `php artisan test`

### Rough Edges & Things to be Improved (Given More Time)
- API:
    - lack of a proper setup for rate limiting especially for the `GET` `/stations/search` endpoint, employing also JWT authentication & rate limiting based on the JWT token (e.g. max `5` API calls per `10` seconds per token);
      - the approach for performing the stations search & the caching employed would deserve more consideration:
          - a possibility could also have been the employment of ElasticSearch with its [geospatial capabilities ](https://www.elastic.co/guide/en/elasticsearch/reference/current/geospatial-analysis.html) - I am not 100% sure if this would fulfill our requirement as it would deserve more time for research.
          - another approach could be for us to have the Earth's continents broken up in tiles (a continent with irregular shape being composed of multiple rectangles/tiles) and then have the bounds of these tiles stored in a `tiles` table:
              - Then, we can have each station belong to a tile and have the stations cached on a per-tile basis (while in the same time keeping also a cache of the stations and companies and with Redis we can retrieve what we need through a single request by employing a Lua script).
              - then the initial query will be one of determining the tile of the search's starting point and what other tiles would be nearby within the given search radius - I would say this would be the most challenging part of this implementation - i.e. marrying neighbouring tiles and their points and then building the response by ascending distance orders and other criteria.
          - I would add to all this that a decision about the caching strategy depends very much on the criticality of the endpoint(s) and what components/systems employed can fail and how problematic would be each failure.
    - (lack of) validation for non-continental coordinates (we cannot have  - yet :) - EV charging stations on ocean surface);
    - (lack of a) `PaginatorDto` & validations for `GET` `/companies` & `GET` `/stations`;
    - (lack of a) `PaginatorBuilder` that returns the `LengthAwarePaginator` instead of manually building it;
    - not employing `AfterMiddleware` for properly normalizing the API's responses, bot successful & unsuccessful;
    - a rough edge is the employment of `pgsql_test`;
    - fine-grained tests for proper company & stations hierarchies.
- UI:
  - I hope it is not problem that I've used MapBox instead of Google Maps, I'm much more comfortable with it as we employ it in our internal tools.
  - The popup for the markers are very rudimentary, I did not have time for anything more, I would have liked for them some nice HTML + CSS presenting in an aesthetic manner the station info and a company logo.
  - better/stricter TS typing (the same for the Node `stations_helper`).
- Other:
    - ENV vars! the project is generally lacking in proper definition & employment of ENV vars;
    - git hook with applying linting rules on commit;
    - a pipeline that would validate the styling, run static code analysis and run the tests;
- I'm sure that I've made plenty other mistakes and that there are some lapses in good practices - the time pressure was a bit intense :).