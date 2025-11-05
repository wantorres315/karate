#!/bin/bash
# filepath: update-db.sh

set -e  # para em caso de erro

echo "🔄 Atualizando banco de dados..."
php artisan migrate:fresh --force

echo "🌱 Executando seeders..."
php artisan db:seed --force

echo "📦 Importando dados do storage..."

echo "  ➜ Clubes (storage/clube.csv)..."
php artisan app:run-clubs storage/clube.csv

echo "  ➜ Usuários (storage/users.csv)..."
php artisan app:run-users storage/users.csv

echo "  ➜ Instrutores de Clubes (storage/instrutor_clube.csv)..."
php artisan app:run-instructor storage/instrutor_clube.csv

echo "  ➜ Graduações (storage/graduations.csv)..."
php artisan app:run-graduations storage/graduations.csv

echo "✅ Processo concluído com sucesso!"