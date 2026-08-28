#!/usr/bin/env bash
#
# auditar.sh — varre o tema procurando desvios das regras do projeto.
#
# O script só COLETA indícios (grep). A triagem — separar violação real de
# exceção legítima — é do agente: ver references/regras.md.
#
# Uso:
#   auditar.sh [--regra=R07] [--formato=lista|resumo]
#
# Saída: uma ocorrência por linha, no formato
#   SEVERIDADE | REGRA | arquivo:linha | trecho

set -uo pipefail

# Raiz do tema (4 níveis acima de .claude/skills/auditar-tema/scripts/).
cd "$(dirname "${BASH_SOURCE[0]}")/../../../.." || exit 1

REGRA_FILTRO=""
FORMATO="lista"

for arg in "$@"; do
  case "$arg" in
    --regra=*)   REGRA_FILTRO="${arg#*=}" ;;
    --formato=*) FORMATO="${arg#*=}" ;;
    *) echo "Argumento desconhecido: $arg" >&2; exit 1 ;;
  esac
done

EXCLUI=(--exclude-dir=.claude --exclude-dir=node_modules --exclude-dir=vendor
        --exclude-dir=.git --exclude-dir=docs --exclude-dir=languages
        --exclude-dir=fonts --exclude=wp-cli.phar --exclude=*.md)

TOTAL=0
ACHADOS=()

# registrar <severidade> <regra> <arquivo:linha> <trecho>
registrar() {
  local sev="$1" regra="$2" alvo="$3" trecho="$4"

  if [[ -n "$REGRA_FILTRO" && "$regra" != "$REGRA_FILTRO" ]]; then
    return 0
  fi

  trecho="$(printf '%s' "$trecho" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//' | cut -c1-110)"
  ACHADOS+=("$sev | $regra | $alvo | $trecho")
  TOTAL=$((TOTAL + 1))
}

# consumir <severidade> <regra>  — lê `arquivo:linha:trecho` do stdin.
#
# IMPORTANTE: chame sempre por redirecionamento — `consumir X Y < <(grep ...)`.
# Em pipe (`grep ... | consumir`) a função roda em subshell e os achados sobem
# vazios no fim.
consumir() {
  local sev="$1" regra="$2" linha resto

  while IFS= read -r linha; do
    [[ -z "$linha" ]] && continue
    resto="${linha#*:}"
    registrar "$sev" "$regra" "${linha%%:*}:${resto%%:*}" "${resto#*:}"
  done
}

# ============================================================ BLOQUEIO

# R01 — arquivo PHP sem a guarda ABSPATH.
while IFS= read -r f; do
  grep -q "ABSPATH" "$f" || registrar "BLOQUEIO" "R01" "$f:1" "sem guarda if ( ! defined( 'ABSPATH' ) )"
done < <(find . -name '*.php' -not -path './.claude/*' -not -path './node_modules/*' \
              -not -path './vendor/*' -not -path './.git/*' | sort)

# R02 — echo de variável sem função de escape na mesma linha.
consumir "BLOQUEIO" "R02" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' -E '(<\?=|echo )[[:space:]]*\$[a-zA-Z_]' . \
    | grep -vE 'esc_html|esc_attr|esc_url|esc_textarea|esc_js|wp_kses|absint|intval|number_format|phpcs:ignore'
)

# R03 — PHP cru de rede em vez das APIs do WordPress.
consumir "BLOQUEIO" "R03" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' -E 'curl_(init|exec)|file_get_contents[[:space:]]*\([[:space:]]*.https?://' .
)

# R04 — atributo style inline montado em PHP.
consumir "BLOQUEIO" "R04" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' 'style="' .
)

# ============================================================ PADRÃO

# R05 — dado externo sem coalescência nula.
consumir "PADRÃO" "R05" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' -E '(get_field|get_theme_mod|get_post_meta)[[:space:]]*\(' . \
    | grep -v '??' \
    | grep -vE 'function_exists|@param|@return|^\s*\*'
)

# R06 — função de topo sem o prefixo do tema.
consumir "PADRÃO" "R06" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' -E '^function [a-zA-Z_]+' . \
    | grep -v 'function cliconnect_'
)

# R07 — cor literal fora do bloco de tokens de theme.css.
while IFS= read -r linha; do
  arquivo="${linha%%:*}"
  resto="${linha#*:}"
  numero="${resto%%:*}"

  # O :root de theme.css (até a linha 62) é onde os tokens são declarados.
  if [[ "$arquivo" == "./assets/css/theme.css" && "$numero" -lt 63 ]]; then
    continue
  fi

  registrar "PADRÃO" "R07" "${arquivo}:${numero}" "${resto#*:}"
done < <(
  # Branco, preto e suas variantes com alfa são ruído: aparecem legitimamente em
  # overlay, gradiente, máscara e sombra, e não são cor de marca.
  grep -rn "${EXCLUI[@]}" --include='*.css' -E '(#[0-9a-fA-F]{3,8}\b|rgba?\()' . \
    | grep -v 'var(--' \
    | grep -vE ':[0-9]+:[[:space:]]*(\*|/\*)' \
    | grep -viE '#(fff|ffffff|000|000000)\b|rgba?\([[:space:]]*(255,[[:space:]]*255,[[:space:]]*255|0,[[:space:]]*0,[[:space:]]*0)[,)]'
)

# R08 — <link>/<script> cru no HTML em vez de wp_enqueue_*.
consumir "PADRÃO" "R08" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' -E '<(link|script)[[:space:]]' . \
    | grep -vE 'wp_enqueue|preload|phpcs:ignore'
)

# R09 — text-domain diferente de 'cli'.
consumir "PADRÃO" "R09" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' -E "(__|_e|_x|_n)\(" . \
    | grep -E "'[^']*'[[:space:]]*,[[:space:]]*'[a-z0-9-]+'[[:space:]]*\)" \
    | grep -v "'cli'"
)

# R10 — módulo em inc/ que ninguém carrega em functions.php.
for f in inc/*.php; do
  [[ -e "$f" ]] || continue
  grep -q "$(basename "$f")" functions.php \
    || registrar "PADRÃO" "R10" "$f:1" "não carregado por cliconnect_require() em functions.php"
done

# R11 — require/include direto em functions.php (deve ser cliconnect_require).
consumir "PADRÃO" "R11" < <(
  grep -n -E '^[[:space:]]*(require|include)(_once)?[[:space:]]' functions.php 2>/dev/null \
    | sed 's|^|./functions.php:|'
)

# R12 — <img src> hardcoded em vez de wp_get_attachment_image()/cliconnect_imagem_tema().
consumir "PADRÃO" "R12" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' -E '<img[^>]+src="[^"]*\.(png|jpe?g|svg|webp)' .
)

# R13 — arquivo que enfileira asset sem usar cliconnect_asset_version().
while IFS= read -r f; do
  grep -q 'cliconnect_asset_version' "$f" \
    || registrar "PADRÃO" "R13" "$f:1" "usa wp_enqueue_* sem cliconnect_asset_version()"
done < <(grep -rl "${EXCLUI[@]}" --include='*.php' -E 'wp_enqueue_(style|script)\(' . | sort)

# R14 — strict_types em arquivo que liga em hooks do WP.
consumir "PADRÃO" "R14" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' 'declare([[:space:]]*strict_types' .
)

# ============================================================ ATENÇÃO

# R15 — !important no CSS.
consumir "ATENÇÃO" "R15" < <(
  grep -rn "${EXCLUI[@]}" --include='*.css' '!important' .
)

# R16 — marcações de pendência.
consumir "ATENÇÃO" "R16" < <(
  grep -rn "${EXCLUI[@]}" --include='*.php' --include='*.css' --include='*.js' -E '(TODO|FIXME|XXX|HACK)\b' .
)

# R17 — asset em assets/img/ que nenhum arquivo referencia.
for img in assets/img/*; do
  [[ -e "$img" ]] || continue
  nome="$(basename "$img")"

  if ! grep -rq "${EXCLUI[@]}" --include='*.php' --include='*.css' --include='*.js' -F "$nome" .; then
    registrar "ATENÇÃO" "R17" "$img:1" "asset não referenciado em nenhum php/css/js"
  fi
done

# R18 — possível texto fixo em template (heurística: acento fora de função de tradução).
consumir "ATENÇÃO" "R18" < <(
  # inc/cli/ (seed) e inc/acf-fields-* são exatamente onde o texto DEVE estar:
  # conteúdo e rótulos do painel. A regra mira template e front-end.
  grep -rn "${EXCLUI[@]}" --include='*.php' -E '>[^<>]{18,}[àáâãéêíóôõúçÀÁÂÃÉÊÍÓÔÕÚÇ][^<>]*<' . \
    | grep -vE '^\./inc/cli/|^\./inc/acf-fields-' \
    | grep -vE "esc_html_e|esc_html__|__\(|_e\(|'message'|^\s*\*|//"
)

# ================================================================= saída

if [[ "${#ACHADOS[@]}" -gt 0 ]]; then
  if [[ "$FORMATO" == "resumo" ]]; then
    printf '%s\n' "${ACHADOS[@]}" \
      | awk -F' \\| ' 'NF{c[$2]++; s[$2]=$1} END {for (r in c) printf "%-9s %s  %d ocorrência(s)\n", s[r], r, c[r]}' \
      | sort -k2
  else
    printf '%s\n' "${ACHADOS[@]}" | sort -t'|' -k2,2 -k3,3
  fi
fi

echo ""
echo "── $TOTAL indício(s). Nem todo indício é violação: faça a triagem com references/regras.md."
