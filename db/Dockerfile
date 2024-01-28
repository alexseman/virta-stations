FROM postgres:latest

RUN apt-get update && apt-get install -y postgis

CMD ["postgres", "-c", "shared_preload_libraries=pg_stat_statements"]
