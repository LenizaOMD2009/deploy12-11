#!/usr/bin/env bash
set -euo pipefail

# run.sh - script de deploy/instalação idempotente
# - instala dependências via composer
# - aplica arquivos SQL em database/migrations (ordenados)
# - recarrega nginx

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_ROOT"

echo "[run.sh] Projeto: $PROJECT_ROOT"

# --- Composer install (sem dev) ---
if ! command -v composer > /dev/null 2>&1; then
	echo "[run.sh] composer não encontrado no PATH. Abortando."
	exit 1
fi

echo "[run.sh] Removendo vendor/ antigo (se existir) e reinstalando..."
if [ -d "vendor" ]; then
	rm -rf vendor/
fi

composer install --no-dev --no-progress -a

# --- Variáveis de banco de dados (substitua/defina via env se necessário) ---
DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-5432}"
DB_NAME="${DB_NAME:-app_db}"
DB_USER="${DB_USER:-postgres}"
DB_PASS="${DB_PASS:-}"

echo "[run.sh] DB -> host=$DB_HOST port=$DB_PORT db=$DB_NAME user=$DB_USER"

run_sql_file(){
	local file="$1"
	echo "[run.sh] Aplicando: $file"

	if [ "$DB_USER" = "postgres" ] && [ -z "$DB_PASS" ]; then
		# Usa sudo -u postgres (socket local)
		sudo -u postgres psql -d "$DB_NAME" -f "$file"
	else
		if [ -n "$DB_PASS" ]; then
			PGPASSWORD="$DB_PASS" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -f "$file"
		else
			psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" -f "$file"
		fi
	fi
}

# --- Executa migrações SQL ---
MIGRATIONS_DIR="$PROJECT_ROOT/database/migrations"
if [ -d "$MIGRATIONS_DIR" ]; then
	shopt -s nullglob
	sql_files=("$MIGRATIONS_DIR"/*.sql)
	if [ ${#sql_files[@]} -eq 0 ]; then
		echo "[run.sh] Nenhum arquivo .sql encontrado em $MIGRATIONS_DIR"
	else
		# Ordena por nome (ex: 001_... , 002_...)
		IFS=$'\n' sorted=($(sort <<<"${sql_files[*]}"))
		unset IFS
		for f in "${sorted[@]}"; do
			run_sql_file "$f"
		done
	fi
else
	echo "[run.sh] Diretorio de migrações não encontrado: $MIGRATIONS_DIR (pulando migrações)"
fi

# --- Ajusta permissões e recarrega Nginx ---
echo "[run.sh] Ajustando permissões em vendor/ (se existir)"
if [ -d "vendor" ]; then
	chmod -R 755 vendor/
fi

echo "[run.sh] Recarregando nginx"
if command -v systemctl > /dev/null 2>&1; then
	sudo systemctl reload nginx || sudo systemctl restart nginx
else
	sudo service nginx reload || sudo service nginx restart
fi

echo "[run.sh] Deploy finalizado."