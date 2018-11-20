#!/bin/bash
set -e

function finish {
    docker-compose --file docker-compose.yaml logs
    docker-compose --file docker-compose.yaml down --volumes
}

trap finish EXIT

docker-compose --file docker-compose.yaml up -d web
docker-compose --file docker-compose.yaml exec app bin/console --version

.travis/docker-wait-healthy dummyapi_app_1

ping=$(curl -sS http://localhost:8080/ping 2>&1)
[[ "$ping" == "pong" ]]
