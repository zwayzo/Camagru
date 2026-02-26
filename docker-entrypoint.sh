
set -e


DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_USER=${DB_USER:-root}
DB_PASS=${DB_PASS:-root123}

echo "Waiting for database at $DB_HOST:$DB_PORT..."
until php -r "try { new PDO('mysql:host=$DB_HOST;port=$DB_PORT', getenv('DB_USER') ?: '$DB_USER', getenv('DB_PASS') ?: '$DB_PASS'); echo 'DB reachable\n'; } catch (Exception \$e) { exit(1); }"; do
  sleep 1
done

if [ "${RUN_SETUP:-1}" = "1" ]; then
  echo "Running config/setup.php..."
  php /var/www/html/config/setup.php || true
fi

echo "Starting Apache..."
exec docker-php-entrypoint apache2-foreground
