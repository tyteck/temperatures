#!/usr/bin/zsh
containerName=temperatures

# updating code
git pull

# creating symlink from public to storage
if [[ ! -L "./public/storage" ]]; then
    docker exec -it --user www-data $containerName php artisan storage:link
fi

if [[ ! -L "./public/medias" ]]; then
    ln -s public/storage public/medias
fi

# installing php modules
docker run --rm -v $(pwd):/app composer:2.3 install --ignore-platform-reqs --prefer-dist

# building css/js
npm install && npm run build

# updating database migrations
docker exec -it --user www-data $containerName php artisan migrate --force

# emptying failed_jobs table
docker exec -it --user www-data $containerName php artisan queue:flush

# restart queue to avoid cache problems
docker exec -it --user www-data $containerName php artisan queue:restart

# clearing all cache
docker exec -it --user www-data $containerName php artisan optimize:clear
