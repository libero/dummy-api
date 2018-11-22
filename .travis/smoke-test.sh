#!/bin/bash
set -e

function finish {
    docker-compose --file docker-compose.yaml logs
    docker-compose --file docker-compose.yaml down --volumes
}

trap finish EXIT

docker-compose --file docker-compose.yaml up -d web
docker-compose --file docker-compose.yaml exec app bin/console --version
ping=$(curl -sS http://localhost:8080/ping 2>&1)
[[ "$ping" == "pong" ]]