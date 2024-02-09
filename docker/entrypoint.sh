#!/bin/bash

role=${CONTAINER_ROLE:-app}

if [ "$role" = "app" ]; then

    php artisan key:generate
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    # php artisan migrate
    php artisan serve --port="$PORT" --host=0.0.0.0 --env=.env
    exec docker-php-entrypoint "$@"

elif [ "$role" = "queue" ]; then

    echo "Running the queue ... "
    php /var/www/html/artisan queue:work --verbose --tries=3 --timeout=180

fi
