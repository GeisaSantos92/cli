# bin/wp.config.example.sh — template VERSIONADO da config do wrapper bin/wp.
#
# COMO USAR:
#   1) Copie este arquivo para bin/wp.config.sh (NÃO versionado):
#        cp bin/wp.config.example.sh bin/wp.config.sh
#   2) Ajuste os caminhos abaixo para os DESTA máquina/site.
#      Os identificadores voláteis (versão do PHP e o ID do socket) mudam por
#      máquina e por site — por isso ficam numa config local ignorada pelo git.
#
# Este arquivo é "sourced" pelo bin/wp; use sintaxe de shell (sem espaços ao
# redor do =).

# --- PHP do Local ------------------------------------------------------------
# Binário PHP empacotado pelo Local. A VERSÃO (ex.: php-8.2.29+0) muda por
# máquina. Liste as versões instaladas com:
#   ls "$HOME/Library/Application Support/Local/lightning-services/"
PHP_BIN="$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php"

# --- Socket do MySQL do Local ------------------------------------------------
# O DB_HOST no wp-config.php é "localhost", então o mysqli do PHP usa o socket
# padrão. Apontamos o PHP para o socket do Local via -d default_socket (o bin/wp
# faz isso), sem precisar editar o wp-config.php.
#
# O segmento de ID no caminho (ex.: ox9l8zXo2) MUDA por máquina/site. Descubra o
# socket atual com (com o site iniciado no Local):
#   ls "$HOME/Library/Application Support/Local/run/"*/mysql/mysqld.sock
DB_SOCKET="$HOME/Library/Application Support/Local/run/<ID_DO_SOCKET>/mysql/mysqld.sock"

# --- Caminho da instalação WordPress -----------------------------------------
# Diretório que contém o wp-config.php (passado como --path para o WP-CLI).
WP_PATH="$HOME/Local Sites/<NOME_DO_SITE>/app/public"

# --- (Opcional) Binários do MySQL do Local -----------------------------------
# Comandos "wp db *" (db check, db export, db query, ...) NÃO usam o mysqli do
# PHP: eles chamam os executáveis externos mysqlcheck/mysql/mysqldump. O Local
# não os expõe no PATH. Defina o diretório bin do serviço de banco do seu site
# para esses comandos funcionarem. A VERSÃO (ex.: mysql-8.4.0) muda por site —
# descubra a do seu site (o server VERSION() indica se é MySQL ou MariaDB):
#   ls "$HOME/Library/Application Support/Local/lightning-services/" | grep -Ei 'mysql|mariadb'
#   find "$HOME/Library/Application Support/Local/lightning-services/" -name mysqlcheck
# Se ficar vazio, apenas comandos que dependem do cliente mysql externo falham;
# os demais (core, option, eval, etc.) funcionam via socket.
MYSQL_BIN_DIR="$HOME/Library/Application Support/Local/lightning-services/mysql-8.4.0/bin/darwin-arm64/bin"
