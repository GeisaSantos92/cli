#!/usr/bin/env bash
#
# fatiar-figma.sh — baixa o PNG de um frame do Figma e fatia em tiras legíveis,
# para comparar lado a lado com as fatias que o captura.mjs gera do site.
#
# Uso:
#   fatiar-figma.sh <url-do-get_screenshot> <pasta-destino> [altura-da-fatia]
#
# A altura da fatia tem que ser a mesma que o captura.mjs usou no site (--fatias),
# senão a fatia N de um lado não corresponde à fatia N do outro. Padrão 1600 nos dois.
#
# A URL vem do get_screenshot chamado com maxDimension = altura real do frame.
# Sem isso o Figma devolve o lado maior em 1024 px e as fatias saem ilegíveis —
# o script avisa quando detecta essa situação.
#
# Requer ImageMagick (`brew install imagemagick`).

set -euo pipefail

url="${1:-}"
destino="${2:-}"
altura="${3:-1600}"

if [ -z "$url" ] || [ -z "$destino" ]; then
	echo "Uso: fatiar-figma.sh <url-do-get_screenshot> <pasta-destino> [altura-da-fatia]" >&2
	exit 1
fi

command -v magick >/dev/null 2>&1 || {
	echo "ImageMagick não encontrado. Rode: brew install imagemagick" >&2
	exit 1
}

mkdir -p "$destino"
origem="$destino/frame.png"

curl -fsSL -o "$origem" "$url"

largura_px=$(magick identify -format '%w' "$origem")
altura_px=$(magick identify -format '%h' "$origem")

echo "frame: ${largura_px}x${altura_px}"

if [ "$largura_px" -lt 1000 ]; then
	echo
	echo "AVISO: largura de ${largura_px}px. O get_screenshot provavelmente rodou sem" >&2
	echo "maxDimension e devolveu uma miniatura. Chame de novo passando a altura real" >&2
	echo "do frame em maxDimension e refaça o download." >&2
	echo
fi

# Largura nativa preservada: o captura.mjs também fatia na largura do viewport, então
# a fatia N do Figma e a fatia N do site saem na mesma escala.
magick "$origem" -crop "${largura_px}x${altura}" +repage "$destino/figma_%02d.png"

echo "fatias:"
ls -1 "$destino"/figma_*.png
echo
echo "Leia em pares com as fatias do site (mesmo índice) usando a ferramenta Read."
