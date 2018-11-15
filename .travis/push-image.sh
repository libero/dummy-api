#!/bin/bash
set -e

if [[ "$TRAVIS_BRANCH" = "master" && "$TRAVIS_PULL_REQUEST" = "false" ]]; then
    echo "$DOCKER_PASSWORD" | docker login --username "$DOCKER_USERNAME" --password-stdin 

    # tag temporarily as liberoadmin due to lack of `libero/` availability
    docker tag "libero/dummy-api:$IMAGE_TAG" "liberoadmin/dummy-api:$IMAGE_TAG"
    docker push "liberoadmin/dummy-api:$IMAGE_TAG"
else
    echo "not pushing an image, we are not on master"
fi
