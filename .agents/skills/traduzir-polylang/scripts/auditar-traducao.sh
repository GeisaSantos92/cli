#!/usr/bin/env bash
#
# auditar-traducao.sh — o que ainda falta traduzir (Polylang).
#
# Só lê dados: não altera nada no site.
#
# Uso: bash .claude/skills/traduzir-polylang/scripts/auditar-traducao.sh

set -uo pipefail

# Raiz do tema (4 níveis acima de .claude/skills/traduzir-polylang/scripts/).
cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." || exit 1

if [[ ! -x ./bin/wp ]]; then
  echo "ERRO: ./bin/wp não encontrado ou sem permissão de execução." >&2
  exit 1
fi

if [[ ! -f ./bin/wp.config.sh ]]; then
  echo "ERRO: bin/wp.config.sh ausente (config por máquina, não versionada)." >&2
  echo "      cp bin/wp.config.example.sh bin/wp.config.sh  e ajuste PHP_BIN, DB_SOCKET e WP_PATH." >&2
  echo "      Ver docs/wp-cli.md." >&2
  exit 1
fi

./bin/wp eval-file .claude/skills/traduzir-polylang/scripts/auditar-traducao.php
