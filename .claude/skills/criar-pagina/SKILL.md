---
name: criar-pagina
description: >
  Cria uma página nova do tema CLI Connect (WordPress clássico, ACF Free, sem build) a
  partir de um frame do Figma, de um screenshot/print do layout, de um template HTML ou
  de um briefing em texto. Monta page-{slug}.php + template-parts + grupo ACF + CSS por
  contexto + seed WP-CLI idempotente, e valida o resultado renderizado contra a
  referência. Use quando pedirem "criar página", "nova página", "montar a página X",
  "implementar o layout da página", "página interna/institucional", "converter esse HTML
  em página", "transformar esse Figma em página".
---

# Criar uma página nova (tema CLI Connect)

Esta skill produz uma página **fiel à referência**, no padrão do tema: nada de texto
fixo no PHP, tudo em ACF/CPT, CSS por contexto usando os tokens existentes, e conteúdo
populado por seed WP-CLI.

> Reutilize o que já existe antes de criar: tokens de `assets/css/theme.css`, classes
> globais (`.container`, `.secao`, `.botao`, `.eyebrow`), helpers de `inc/helpers.php`,
> ícones de `inc/icons.php` e CPTs de `inc/cpt.php`. Detalhes:
> [`references/padroes-tema.md`](references/padroes-tema.md).

---

## Fase 0 — Perguntar antes de executar (OBRIGATÓRIA)

**Nunca comece a implementar sem esta rodada.** Mesmo que o usuário já tenha dado algum
contexto, confirme o que falta. Use **uma única chamada** de `AskUserQuestion` com as
perguntas ainda em aberto:

| # | Pergunta | Opções |
|---|---|---|
| 1 | Qual a referência do layout? | **Figma** (link do frame) · **Screenshot/print** (caminho da imagem) · **Template HTML** (caminho da pasta/arquivo) · **Nenhuma** (briefing em texto) |
| 2 | Os assets (imagens/ícones) já foram exportados manualmente? | **Sim, já estão na pasta** (informar onde) · **Não — exportar do Figma** · **Não há assets novos** |
| 3 | Qual a página? (slug/título) | Sugerir os slugs já criados pelo seed que ainda não têm template — ver `criar_paginas()` em `inc/cli/seed.php` — + "Outra" |
| 4 | Como rodar o seed no fim? | **Incremental** (`./bin/wp cliconnect seed`) · **Com `--reset`** (apaga e recria tudo do seed) |

Regras da rodada:

- **Pergunta 2 é a que mais economiza token.** Se o usuário já exportou os assets, você
  **não** baixa nada do Figma e **não** pede screenshot de nós de imagem: só lê a pasta,
  lista os arquivos e usa os nomes. Isso corta a maior parte do custo de explorar o Figma.
- Se a resposta da 1 for **Nenhuma**, peça o briefing (texto, tópicos, títulos) antes de
  seguir — **jamais invente conteúdo institucional**. Se o usuário quiser que você
  proponha o texto, deixe explícito que é rascunho a validar.
- Se a resposta da 1 for Figma/Screenshot/HTML mas o caminho/link não veio, peça-o.
- Anote as respostas; elas definem o fluxo da Fase 1.

---

## Fase 1 — Levantar layout e conteúdo

Siga o guia da fonte escolhida (leia só o que for usar):

- **Figma** → [`references/fonte-figma.md`](references/fonte-figma.md)
- **Screenshot / print** → [`references/fonte-imagem.md`](references/fonte-imagem.md)
- **Template HTML** → [`references/fonte-html.md`](references/fonte-html.md)
- **Briefing** → sem passo de extração; monte as seções a partir do texto do usuário e
  reaproveite composições já existentes na home (`template-parts/home/`).

Saída desta fase (obrigatória, em qualquer fonte):

1. **Lista ordenada das seções** da página, com nome em kebab-case.
2. **Todo o texto real** de cada seção (títulos, eyebrows, parágrafos, rótulos de botão,
   destinos dos links) — é o que vai virar seed.
3. **Inventário de assets**: cada imagem classificada como
   - *ilustração fechada* (arte com texto embutido, não editável) → `assets/img/`,
     renderizada por `cliconnect_imagem_tema()`;
   - *conteúdo editável* (foto, logo, mockup que o cliente troca) → `assets/seed/`,
     importada pelo seed e usada como ID de anexo no ACF/CPT.
4. **Repetições identificadas**: qualquer lista que possa crescer → **CPT**
   (existente ou novo). Conjunto fixo → campos numerados. Nunca invente Repeater.

---

## Fase 2 — Plano de seções (aprovação do usuário)

Antes de escrever qualquer arquivo, apresente uma **tabela de mapeamento** e peça o "ok":

```
Seção            | Template-part                    | Conteúdo             | Assets
-----------------|----------------------------------|----------------------|---------------------
hero             | template-parts/plataforma/hero   | ACF: hero_* (4)      | assets/img/plataforma-hero.png
recursos         | template-parts/plataforma/recursos | CPT cli_recurso (novo) | assets/seed/recurso-*.png
faq              | template-parts/plataforma/faq    | CPT cli_faq (existente) | —
```

Inclua nessa mensagem: arquivos que serão criados, arquivos existentes que serão
tocados, CPTs novos (se houver) e o comando de seed que será rodado no fim.
**Só siga depois do aceite.** Se o usuário pedir ajustes, refaça a tabela.

---

## Fase 3 — Implementar

Ordem fixa (cada passo depende do anterior). Código pronto de cada arquivo em
[`references/estrutura-pagina.md`](references/estrutura-pagina.md).

1. **Infra (uma vez por projeto).** Confira se `inc/helpers.php` já tem
   `cliconnect_pagina_ids()`, `cliconnect_e_pagina()` e `cliconnect_campo_pagina()`.
   Se não tiver, adicione — são os equivalentes de `cliconnect_campo()` para páginas
   internas (a versão da home lê só `page_on_front`).
2. **CPTs novos** (se o plano previu) em `inc/cpt.php` + campos em
   `inc/acf-fields-cpt.php`, seguindo os CPTs existentes.
3. **Grupo ACF da página**: `inc/acf-fields-{slug}.php`, com abas por seção,
   chaves `field_{slug}_*`, grupo `group_cli_{slug}`, localização resolvida por slug
   (funciona em qualquer ambiente e cobre as traduções do Polylang).
4. **Template-parts**: `template-parts/{slug}/{secao}.php` — um por seção, cada um com
   guarda `ABSPATH`, leitura dos campos no topo, `return` cedo se vazio, e só marcação
   depois. Escape em toda saída.
5. **Template da página**: `page-{slug}.php` — só orquestra o `foreach` das seções,
   como `front-page.php`.
6. **CSS**: `assets/css/page-{slug}.css`, com cabeçalho de comentário, seções numeradas
   e **apenas tokens** (`var(--cor-*)`, `var(--tam-*)`, `var(--raio*)`). Se precisar de
   um valor que não existe como token, primeiro procure um equivalente; só então
   justifique o literal em comentário.
7. **Enfileirar** o CSS em `inc/enqueue.php`, condicionado a `cliconnect_e_pagina('{slug}')`.
8. **Registrar** os novos `inc/*.php` em `functions.php` via `cliconnect_require()`.

Regras que não se negociam (resumo de `CLAUDE.md` + `docs/code-standards.md`):

- `if ( ! defined( 'ABSPATH' ) ) { exit; }` no topo de todo arquivo PHP.
- Prefixo `cliconnect_`, text-domain `cli`, handles `cliconnect-`.
- Coalescência nula em tudo que vem de ACF/Customizer/meta: `get_field('x') ?? ''`.
- Escape na saída: `esc_html`/`esc_attr`/`esc_url`/`wp_kses_post`.
- Zero texto fixo no PHP: título, parágrafo e rótulo de botão vêm do ACF/CPT.
  Só rótulos de interface (ex.: "Integrações") ficam em `esc_html_e( '...', 'cli' )`.
- Imagens de conteúdo por ID (`wp_get_attachment_image`), nunca `src` hardcoded.
- Nada de `<link>`/`<script>` no HTML nem estilo inline no PHP.
- Sem FSE, sem build, sem dependência nova de plugin.

---

## Fase 4 — Seed (sempre)

Toda página criada **precisa nascer populada**. Estenda `inc/cli/seed.php` conforme
[`references/seed.md`](references/seed.md):

1. Garanta o slug da página em `criar_paginas()`.
2. Copie os assets de conteúdo para `assets/seed/` (nomes em kebab-case, com prefixo
   do contexto: `plataforma-hero.png`).
3. Crie `preencher_{slug}( $ids['{slug}'] )` com **o texto real levantado na Fase 1**,
   e chame-o em `__invoke()` logo depois de `preencher_home()`.
4. Se criou CPT novo, crie o `criar_{cpt}()` correspondente, também idempotente.
5. Rode o seed com a opção escolhida na Fase 0:
   ```bash
   ./bin/wp cliconnect seed          # incremental
   ./bin/wp cliconnect seed --reset  # apaga e recria
   ```
   Se `bin/wp.config.sh` não existir, o wrapper falha: avise o usuário e mostre o
   `cp bin/wp.config.example.sh bin/wp.config.sh` (ver `docs/wp-cli.md`) em vez de
   tentar contornar.

**Print como fonte de conteúdo:** quando não houver Figma nem HTML, o texto do seed sai
da **leitura da imagem** — transcreva literalmente o que está no print (respeitando
acentuação e quebras de linha) em vez de parafrasear.

---

## Fase 5 — Revisão interna (antes de entregar)

Você mesmo valida primeiro. Guia completo:
[`references/validacao.md`](references/validacao.md).

1. **Sintaxe**: `php -l` em cada arquivo novo/alterado (use o `PHP_BIN` de
   `bin/wp.config.sh`).
2. **Render**: capture a página em 1440 / 768 / 390 px com
   `scripts/captura.mjs` (Playwright já disponível na máquina) e **leia as imagens**.
3. **Comparação com a referência**: abra a referência (frame do Figma via
   `get_screenshot`, o print original ou o HTML renderizado) e confira, seção a seção:
   ordem, hierarquia tipográfica, cores, espaçamentos, alinhamento, estados de botão.
4. **Higiene**: sem erro no console, sem scroll horizontal, sem 404 de asset, sem
   PHP notice no `debug.log`.
5. **Itere** enquanto houver divergência relevante. Só passe para a Fase 6 quando você
   mesmo considerar que bate.

---

## Fase 6 — Validação do usuário (obrigatória)

Feche entregando:

- a **URL local** da página,
- as **capturas** geradas (caminhos),
- a **lista do que foi criado/alterado**,
- as **divergências conhecidas** e decisões que você tomou sozinho,

e **peça explicitamente** que o usuário abra e valide contra o layout, apontando o que
ajustar. Não declare a página "pronta" antes desse aceite.
