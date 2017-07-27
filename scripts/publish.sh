echo "-- Publish to docker.io"

export REPO=$(git remote get-url --all `git remote show` | sed -n 's|[a-z]*:\/\/github.com\/\(.*\/.*\)\.git.*|\1|p')
docker push $REPO