#!/bin/bash

cd $(git rev-parse --show-toplevel)

# Currently builds the release version
echo "-- Building docker container"
export REPO=$(git remote get-url --all `git remote show` | sed -n 's|[a-z]*:\/\/github.com\/\(.*\/.*\)\.git.*|\1|p')

docker build -t $REPO .
docker tag $REPO $REPO
