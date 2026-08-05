#!/usr/bin/env bash
#
# bin/rename.sh — renomeia os tokens do starter para os do projeto novo.
#
# Uso (rode na RAIZ do tema já copiado para a pasta definitiva):
#   ./bin/rename.sh --slug=acme --prefix=acme --name="Acme"
#
#   --slug    Text-domain e slug do tema (minúsculas, hífens). Ex.: acme
#   --prefix  Prefixo PHP (minúsculas, underscores). Ex.: acme
#   --name    Nome legível do tema. Ex.: "Acme"
#
# Substituições (nesta ordem, em .php/.css/.js/.md/.sh):
#   started-theme  -> {slug}      (text-domain)
#   Started_Theme  -> {Prefix}    (@package, prefix em PascalCase)
#   Started Theme  -> {name}      (nome legível)
#   STARTER_       -> {PREFIX}_   (constantes)
#   Starter_       -> {Prefix}_   (classes, ex.: Starter_Seed_Setup)
#   starter/       -> {prefix}/   (namespace dos blocos, ex.: starter/hero)
#   starter_       -> {prefix}_   (funções, hooks, theme mods)
#   starter-       -> {prefix}-   (handles de assets, classes CSS)
#
# O script é idempotente e se auto-exclui da substituição. Depois de rodar,
# confira com o grep sugerido no SETUP.md e delete este arquivo se quiser.

set -euo pipefail

SLUG=""
PREFIX=""
NAME=""

for arg in "$@"; do
  case "$arg" in
    --slug=*)   SLUG="${arg#*=}" ;;
    --prefix=*) PREFIX="${arg#*=}" ;;
    --name=*)   NAME="${arg#*=}" ;;
    *) echo "Argumento desconhecido: $arg" >&2; exit 1 ;;
  esac
done

if [[ -z "$SLUG" || -z "$PREFIX" || -z "$NAME" ]]; then
  echo "Uso: ./bin/rename.sh --slug=acme --prefix=acme --name=\"Acme\"" >&2
  exit 1
fi

if [[ ! -f "./style.css" || ! -f "./functions.php" ]]; then
  echo "ERRO: rode este script na raiz do tema (onde estão style.css e functions.php)." >&2
  exit 1
fi

if [[ ! "$SLUG" =~ ^[a-z0-9-]+$ ]]; then
  echo "ERRO: --slug deve ter só minúsculas, números e hífens (ex.: acme)." >&2
  exit 1
fi

if [[ ! "$PREFIX" =~ ^[a-z][a-z0-9_]*$ ]]; then
  echo "ERRO: --prefix deve ter só minúsculas, números e underscores (ex.: acme)." >&2
  exit 1
fi

# PascalCase do prefixo p/ @package: acme_corp -> Acme_Corp
PACKAGE="$(printf '%s' "$PREFIX" | awk -F'_' '{ for (i = 1; i <= NF; i++) { $i = toupper(substr($i,1,1)) substr($i,2) } print }' OFS='_')"
UPPER="$(printf '%s' "$PREFIX" | tr '[:lower:]' '[:upper:]')"

echo "Renomeando tokens do starter:"
echo "  started-theme -> $SLUG"
echo "  Started_Theme -> $PACKAGE"
echo "  Started Theme -> $NAME"
echo "  STARTER_      -> ${UPPER}_"
echo "  Starter_      -> ${PACKAGE}_"
echo "  starter/      -> ${PREFIX}/"
echo "  starter_      -> ${PREFIX}_"
echo "  starter-      -> ${PREFIX}-"

export SLUG PREFIX NAME PACKAGE UPPER

find . \
  -path ./.git -prune -o \
  -path ./bin/rename.sh -prune -o \
  -type f \( -name '*.php' -o -name '*.css' -o -name '*.js' -o -name '*.json' -o -name '*.md' -o -name '*.sh' \) \
  -print0 |
  xargs -0 perl -pi -e '
    s/started-theme/$ENV{SLUG}/g;
    s/Started_Theme/$ENV{PACKAGE}/g;
    s/Started Theme/$ENV{NAME}/g;
    s/STARTER_/$ENV{UPPER}_/g;
    s/Starter_/$ENV{PACKAGE}_/g;
    s{starter/}{$ENV{PREFIX}/}g;
    s/starter_/$ENV{PREFIX}_/g;
    s/starter-/$ENV{PREFIX}-/g;
  '

echo
echo "Feito. Próximos passos (ver SETUP.md):"
echo "  1) Revise style.css (Description, Author, Theme URI)."
echo "  2) Troque a paleta em assets/css/theme.css e inc/login.php."
echo "  3) Confira sobras: grep -rn 'starter\\|started' --include='*.php' ."
echo "  4) Delete este script: rm bin/rename.sh"
