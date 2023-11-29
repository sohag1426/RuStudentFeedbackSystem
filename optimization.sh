#!//bin/bash

composer install -n --optimize-autoloader --no-dev

php artisan config:cache

php artisan event:cache

php artisan route:cache

php artisan view:cache

chown -R www-data:www-data /var/www/laravelApps/RuStudentFeedbackSystem/storage/

chown -R www-data:www-data /var/www/laravelApps/RuStudentFeedbackSystem/vendor/

chown -R www-data:www-data /var/www/laravelApps/RuStudentFeedbackSystem/bootstrap/

php artisan migrate --force --path database/migrations/
