#!/bin/bash
set -e

echo "==> Instalando dependências PHP..."
composer install --ignore-platform-req=ext-ldap

echo "==> Copiando .env..."
if [ ! -f .env ]; then
  cp .env.example .env 2>/dev/null || cat > .env << 'EOF'
APP_NAME=GymTrack
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=gymtrack
DB_USERNAME=gymtrack
DB_PASSWORD=secret

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
EOF
fi

echo "==> Gerando chave da aplicação..."
php artisan key:generate --ansi

echo "==> Aguardando PostgreSQL..."
until php artisan db:show --ansi > /dev/null 2>&1; do
  sleep 2
done

echo "==> Rodando migrations..."
php artisan migrate --ansi

echo "==> Instalando dependências Node.js (frontend)..."
cd frontend
if [ ! -f .env ]; then
  cp .env.example .env 2>/dev/null || echo "NUXT_PUBLIC_API_BASE_URL=http://localhost:8000" > .env
fi
npm install
cd ..

echo "==> Pronto! Para iniciar o projeto:"
echo "    Laravel:  php artisan serve --host=0.0.0.0 --port=8000"
echo "    Nuxt:     cd frontend && npm run dev -- --host 0.0.0.0"
