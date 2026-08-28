#!/usr/bin/env bash
#
# issue.sh — lê uma issue do GitHub por inteiro e baixa os anexos de imagem.
#
# As issues deste projeto quase sempre trazem a especificação numa imagem
# anexada (github.com/user-attachments/...). Em repositório privado essa URL
# devolve 404 sem autenticação — por isso o download usa o token do `gh`.
#
# Uso:
#   issue.sh <numero> [--repo owner/nome] [--destino <dir>]
#
# Exemplo:
#   .claude/skills/implementar-issue/scripts/issue.sh 13 \
#     --repo daniilomello/cli-connect --destino /tmp/issue-13
#
# Saída: markdown da issue no stdout + anexos salvos em <destino>.

set -euo pipefail

NUMERO="${1:-}"
REPO=""
DESTINO=""

if [[ -z "$NUMERO" ]]; then
  echo "Uso: issue.sh <numero> [--repo owner/nome] [--destino <dir>]" >&2
  exit 1
fi

shift

while [[ $# -gt 0 ]]; do
  case "$1" in
    --repo)    REPO="$2"; shift 2 ;;
    --destino) DESTINO="$2"; shift 2 ;;
    *) echo "Argumento desconhecido: $1" >&2; exit 1 ;;
  esac
done

DESTINO="${DESTINO:-${TMPDIR:-/tmp}/issue-${NUMERO}}"
mkdir -p "$DESTINO"

GH_ARGS=(issue view "$NUMERO")
[[ -n "$REPO" ]] && GH_ARGS+=(--repo "$REPO")

JSON="$DESTINO/issue.json"

gh "${GH_ARGS[@]}" \
  --json number,title,body,state,url,labels,assignees,milestone,createdAt,comments \
  > "$JSON"

# ------------------------------------------------------------------ markdown

jq -r '
  "# #\(.number) — \(.title)",
  "",
  "- estado: \(.state)",
  "- url: \(.url)",
  "- labels: \((.labels // []) | map(.name) | join(", ") | if . == "" then "(nenhuma)" else . end)",
  "- responsáveis: \((.assignees // []) | map(.login) | join(", ") | if . == "" then "(nenhum)" else . end)",
  "- milestone: \(.milestone.title // "(nenhuma)")",
  "- criada em: \(.createdAt)",
  "",
  "## Descrição",
  "",
  (.body // "(vazia)"),
  "",
  "## Comentários (\((.comments // []) | length))",
  "",
  ((.comments // [])[] | "### \(.author.login) — \(.createdAt)\n\n\(.body)\n")
' "$JSON"

# -------------------------------------------------------------------- anexos

URLS=$(
  jq -r '[(.body // ""), ((.comments // [])[].body // "")] | join("\n")' "$JSON" \
    | grep -oE 'https://(github\.com/user-attachments/assets/[A-Za-z0-9._-]+|user-images\.githubusercontent\.com/[A-Za-z0-9._/-]+|raw\.githubusercontent\.com/[A-Za-z0-9._/-]+|github\.com/[A-Za-z0-9._-]+/[A-Za-z0-9._-]+/assets/[A-Za-z0-9._/-]+)' \
    | sort -u || true
)

if [[ -z "$URLS" ]]; then
  echo ""
  echo "## Anexos"
  echo ""
  echo "(nenhum anexo de imagem na issue)"
  exit 0
fi

TOKEN="$(gh auth token)"
INDICE=0

echo ""
echo "## Anexos"
echo ""

while IFS= read -r url; do
  [[ -z "$url" ]] && continue
  INDICE=$((INDICE + 1))

  BASE="$DESTINO/anexo-$(printf '%02d' "$INDICE")"
  CABECALHO="$BASE.headers"

  # -L segue o redirect para o storage; o token é obrigatório em repo privado.
  HTTP=$(curl -sSL -H "Authorization: Bearer $TOKEN" \
    -D "$CABECALHO" -o "$BASE.bin" -w '%{http_code}' "$url" || echo "000")

  if [[ "$HTTP" != "200" ]]; then
    echo "- FALHA ($HTTP): $url"
    rm -f "$BASE.bin" "$CABECALHO"
    continue
  fi

  TIPO=$(grep -i '^content-type:' "$CABECALHO" | tail -1 | tr -d '\r' | awk '{print $2}')
  rm -f "$CABECALHO"

  case "$TIPO" in
    image/png)      EXT="png" ;;
    image/jpeg)     EXT="jpg" ;;
    image/gif)      EXT="gif" ;;
    image/webp)     EXT="webp" ;;
    image/svg+xml)  EXT="svg" ;;
    video/*)        EXT="mp4" ;;
    *)              EXT="bin" ;;
  esac

  mv "$BASE.bin" "$BASE.$EXT"
  echo "- $BASE.$EXT  ($TIPO)  ←  $url"
done <<< "$URLS"

echo ""
echo "Leia cada anexo de imagem com a ferramenta Read antes de propor a correção."
