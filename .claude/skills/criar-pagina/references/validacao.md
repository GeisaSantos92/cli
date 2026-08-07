# Revisão interna e validação

Duas etapas: **você revisa primeiro** (Fase 5) e só então **pede a validação do usuário**
(Fase 6). Entregar sem ter olhado a página renderizada não conta como entrega.

---

## 1. Sintaxe PHP

```bash
source bin/wp.config.sh && for f in \
  page-{slug}.php \
  inc/acf-fields-{slug}.php \
  inc/helpers.php \
  inc/enqueue.php \
  functions.php \
  template-parts/{slug}/*.php
do "$PHP_BIN" -l "$f"; done
```

`PHP_BIN` vem de `bin/wp.config.sh` (não versionado). Se o arquivo não existir, avise o
usuário — não tente adivinhar o caminho do PHP do Local.

---

## 2. Estado do conteúdo

```bash
./bin/wp cliconnect seed
./bin/wp eval 'echo home_url( "/{slug}/" );'
./bin/wp eval '$p = get_page_by_path("{slug}"); var_export( get_field("{slug}_hero_titulo", $p->ID) );'
```

Campo vazio logo após a primeira criação da página → rode o seed de novo (ver
`seed.md`, seção 5).

---

## 3. Capturar a página renderizada

```bash
node .claude/skills/criar-pagina/scripts/captura.mjs \
  "http://<site>.local/{slug}/" \
  /tmp/revisao-{slug}
```

O script gera, por viewport (1440 / 768 / 390 por padrão): um PNG de página inteira +
fatias verticais legíveis, e imprime um resumo com:

- status HTTP e altura da página;
- classes das `<section>` encontradas em `<main>` (confere a **ordem das seções**);
- quantidade de `<h1>` (tem que ser **1**);
- **scroll horizontal** e o elemento culpado;
- `img` sem `alt`;
- erros de console e requisições com status ≥ 400 (asset 404, CSS não enfileirado).

Depois **leia as imagens** com a ferramenta `Read`. As fatias existem para isso: uma
captura de página inteira de 8000px fica ilegível.

---

## 4. Comparar com a referência

Abra a referência ao lado das capturas:

| Fonte | Referência |
|---|---|
| Figma | `get_screenshot` do frame (já capturado na Fase 1) |
| Print | a imagem original do usuário |
| HTML | screenshot do `index.html` renderizado (ver `fonte-html.md`) |
| Briefing | não há comparação visual — valide contra a lista de seções acordada |

Confira seção a seção, nesta ordem (do estruturante ao cosmético):

1. **Ordem e presença** das seções — nenhuma faltando, nenhuma a mais.
2. **Conteúdo** — todo texto da referência aparece, sem typo e sem placeholder.
3. **Hierarquia tipográfica** — tamanho relativo entre `h1`/`h2`/corpo/eyebrow bate.
4. **Cores** — fundos, botões e destaques usam o token certo.
5. **Layout** — colunas, alinhamento, largura de container, proporção de imagem.
6. **Ritmo vertical** — respiro entre seções e dentro de cada bloco.
7. **Estados** — hover/foco do botão e do link seguem o padrão do tema.
8. **Responsivo** — em 768 e 390 nada estoura, quebra nem some.

Divergência estruturante (seção fora de ordem, coluna trocada, cor errada) → **corrija
e recapture**. Divergência de 2–4px de espaçamento → registre na entrega em vez de
gastar iterações.

---

## 5. Higiene técnica

- `debug.log` sem notice/warning novo:
  ```bash
  tail -30 "$WP_PATH/wp-content/debug.log"
  ```
- Nenhum `404` de asset no relatório do script.
- CSS da página realmente enfileirado (se não aparecer no `<head>`, o
  `cliconnect_e_pagina()` não casou — confira o slug da página no banco).
- Nada de estilo inline nem `<link>`/`<script>` cru no HTML gerado.
- Acessibilidade mínima: um `<h1>`, `alt` em toda imagem de conteúdo, `aria-label` em
  botão só-ícone, foco visível.

---

## 6. Fechar com o usuário (obrigatório)

A validação final é do usuário. Entregue:

1. **URL local** da página.
2. **Caminhos das capturas** geradas (para ele conferir sem abrir o navegador, se quiser).
3. **Arquivos criados e alterados**, em lista.
4. **Decisões que você tomou sozinho** — mapeamentos de cor, comportamento responsivo
   inferido, textos que faltavam.
5. **Pendências conhecidas** — assets faltando, links sem destino, trechos ilegíveis na
   referência.
6. Um pedido explícito: *"abra a página e compare com o layout; me diga o que ajustar"*.

Não declare a página concluída antes desse retorno.
