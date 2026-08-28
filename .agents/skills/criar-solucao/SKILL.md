---
name: criar-solucao
description: >
  Monta a landing page de uma solução do tema CLI Connect (CPT cli_solucao) a partir de
  um template com o nome da solução e uma lista numerada de links do Figma — uma seção
  por rodada, com validação do usuário entre elas. Use quando pedirem "criar uma
  solução", "nova solução", "Indústria - X", "Tecnologia - X", "Nuvem - X",
  "Departamento - X", "landing de solução", ou quando colarem uma lista de frames do
  Figma numerada (01, 02, 03…) para virar página.
---

# Criar uma landing de solução (CPT `cli_solucao`)

Cada post de `cli_solucao` é ao mesmo tempo item do catálogo e landing page. As seções
são **opcionais e independentes**: cada template-part retorna cedo quando seus campos
ACF estão vazios, então uma solução mostra só o que foi preenchido.

Duas invariantes que mudam tudo:

> **O post já existe.** `criar_solucoes()` em `inc/cli/seed.php` cria um stub para cada
> tipo da hierarquia de `cli_categoria_solucao` (slug de seed `solucao:<slug>`).
> **Nunca crie um post novo** — preencha o que está lá.
>
> **A seção provavelmente já existe.** O catálogo abaixo cobre as seções recorrentes do
> design. Na última landing montada, 5 das 7 seções não exigiram uma linha de código —
> só seed. Escrever template-part novo é a exceção, não o passo padrão.

---

## Catálogo de seções

Ordem de render em `single-cli_solucao.php`. Todas usam `assets/css/page-solucao.css` e
`template-parts/solucao/`.

| Aba ACF | Slug | Prefixo dos campos | Classe CSS | Conteúdo |
|---|---|---|---|---|
| 1 · Hero | `hero` | `solucao_hero_*` | `.sh-hero` | eyebrow, título + destaque azul, corpo, 2 botões, imagem quadrada |
| 2 · Métricas | `metricas` | `solucao_metrica_{1..3}_*` | `.sh-metricas` | faixa de 3 números em degradê + rótulo |
| 3 · Pilares | `pilares` | `solucao_pilares_*` | `.sp-pilares` | eyebrow, título, 3 cards com ícone em máscara |
| 4 · Logos | `logos` | `solucao_logos_*` | `.sh-logos` | microcopy + logos de `cli_cliente` (relationship) |
| 5 · Casos de Uso | `casos` | `solucao_casos_{1..6}_*` | `.sc-casos` | eyebrow, título, até 6 cards + card CTA azul opcional |
| 6 · Selos | `selos` | `solucao_selos_*` | `.ss-selos` | eyebrow, título, corpo; os 10 badges são assets estáticos do tema |
| 7 · Diferencial | `diferencial` | `solucao_dif_*` | `.sd-dif` | texto + tópicos + imagem |
| 8 · Plataforma | `plataforma` | `solucao_plat_*` | `.spu-plat` | texto + tópicos + imagem |
| 9 · Aceleradores | `aceleradores` | `solucao_acel_*` | `.sacel-acel` | texto + tópicos + botão + imagem |
| 10 · FAQ | `faq` | `solucao_faq_*` | `.sfaq` | accordion `<details>` com relationship para `cli_faq` |
| 11 · Diagrama | `diagrama` | `solucao_diagrama_*` | `.sdg-diagrama` | título centralizado + ilustração fechada larga, sobre textura de pontos. **Renderiza entre Pilares e Logos** — o número é a ordem de entrada no catálogo, não a posição na página |

O card-global (card branco com quadrado de ícone 40px em degradê azul) é o mesmo em
Pilares e Casos de Uso — se um frame novo usar esse card, ele já está implementado nos
dois lugares.

---

## Fase 0 — Ler o template de entrada

O usuário abre com um bloco assim:

```
Agora vamos criar uma solução: Indústria - Manufatura

Faça a seções seguindo a ordem. Faça uma de cada vez:

01: https://www.figma.com/design/<fileKey>/...?node-id=16627-113438
02: https://www.figma.com/design/<fileKey>/...?node-id=16627-113472
...
```

Extraia: **categoria**, **nome da solução**, e a **lista ordenada de nós** (`node-id`
com o hífen trocado por dois-pontos: `16627-113438` → `16627:113438`) mais o `fileKey`.

Depois confirme em **uma única chamada** de `AskUserQuestion` só o que ficou em aberto —
tipicamente nada além disto:

| # | Pergunta | Opções |
|---|---|---|
| 1 | O post da solução já existe no catálogo? | **Sim** (confirmar o slug) · **Não — criar o termo e o stub antes** |
| 2 | Os assets já foram exportados à mão? | **Não — exportar do Figma** · **Sim** (informar a pasta) |

A pergunta 2 é a que mais economiza token: com os assets já na pasta, você não chama
`download_assets` nem pede screenshot de nós de imagem. Detalhe em
[`criar-pagina/references/fonte-figma.md`](../criar-pagina/references/fonte-figma.md).

Confirme o slug rodando:

```bash
./bin/wp post list --post_type=cli_solucao --field=post_title --posts_per_page=-1 | tr -d '\357\273\277'
```

Se a solução não estiver na hierarquia, acrescente o termo em `criar_solucoes()`
(`inc/cli/seed.php`) e rode o seed antes de seguir.

Feche a fase criando a branch a partir da `main`:

```bash
git checkout main && git checkout -b feat/solucao-<slug>
```

---

## Fase 1 — Rodada por seção (repetir para cada link)

**Uma seção por rodada.** Termine, entregue e espere o aceite antes de abrir o próximo
link — a lista inteira estar visível no template não autoriza adiantar as seguintes.

### 1. Ler o nó no Figma

Invoque `figma:figma-design-to-code` (pré-requisito obrigatório do MCP), depois:

1. `get_metadata` no nó — estrutura e, principalmente, o que está `hidden="true"`.
   O arquivo tem muita camada escondida que **não** entra na implementação.
2. `get_design_context` no mesmo nó — código de referência, screenshot e tokens.

### 2. Casar com o catálogo

Compare o frame com a tabela acima e classifique a rodada:

- **Reutilizar** — o componente já existe e bate. Vai só para o passo 4 (seed).
- **Estender** — existe, mas falta um pedaço (um card a mais, um eyebrow que não havia).
  Amplie o componente existente; não duplique.
- **Criar** — não há equivalente no catálogo.

Estender e criar seguem [`references/estender-secao.md`](references/estender-secao.md).

Diga ao usuário em qual dos três a rodada caiu **antes** de escrever código.

### 3. Assets

Ilustrações e ícones vão para `assets/seed/<slug-solucao>-<papel>.<ext>`, no padrão já
existente (`servicos-financeiros-hero.png`, `-pilar-1.svg`, `-caso-1.svg`). Ícone entra
como **SVG usado em `mask-image`** — nunca redesenhe o vetor à mão. Hero em 2x
(`defaultScale: 2`).

> `assets/seed/` é gitignored: os arquivos ficam só na máquina e o commit leva apenas o
> `seed.php` que os referencia. Avise o usuário sempre que baixar assets novos.

### 4. Seed

Um método por solução em `inc/cli/seed.php`, chamado no `run()` logo depois do da
solução anterior. Molde — use `preencher_solucao_servicos_financeiros()` como referência
viva:

```php
protected function preencher_solucao_<slug>() {
	$posts = get_posts(
		array(
			'post_type'      => 'cli_solucao',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => self::META,   // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => 'solucao:<slug>', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);

	if ( ! $posts ) {
		WP_CLI::warning( '  <Nome>: post não encontrado — verifique se criar_solucoes() foi executado.' );
		return;
	}

	$post_id = (int) $posts[0];

	$campos = array(
		// 1 · Hero.
		'solucao_hero_eyebrow' => '…',
		'solucao_hero_imagem'  => $this->img( '<slug>-hero' ),
	);

	foreach ( $campos as $nome => $valor ) {
		update_field( $nome, $valor, $post_id );
	}
}
```

A cada rodada, **acrescente o bloco da seção ao mesmo `$campos`**, com um comentário
`// N · Nome.` — assim o método cresce na ordem do Figma.

- `$this->img( 'nome-sem-extensao' )` para assets de `assets/seed/`.
- `$this->id_do_seed( $slug, $post_type )` para vincular posts de outros CPTs
  (ex.: `$this->id_do_seed( 'cliente:hsbc', 'cli_cliente' )`).
- FAQ tem método próprio, no molde de `preencher_servicos_financeiros_faq()`: cria os
  `cli_faq` por `upsert()` e liga pelo relationship.

Rode incremental (o `--reset` apaga e recria todo o conteúdo do seed):

```bash
./bin/wp cliconnect seed
```

### 5. Validar

Nesta ordem, sem pular:

```bash
"$PHP_BIN" -l <arquivo>          # PHP_BIN vem de bin/wp.config.sh
./bin/wp post meta list <ID> --format=csv | tr -d '\357\273\277' | grep solucao_
```

Depois abra `http://cli.local/solucao/<slug>/` e compare com o screenshot do Figma:
ordem, hierarquia tipográfica, cores, espaçamento, alinhamento. **Itere até bater.**

**Armadilhas do ambiente:**

- Não existe `php` no PATH — só o binário empacotado pelo Local, em `PHP_BIN`
  (`bin/wp.config.sh`, não versionado).
- A saída do `bin/wp` vem com **BOM**: limpe com `| tr -d '\357\273\277'` antes de usar
  em variável de shell.
- O painel do navegador não recompõe frames ao rolar — screenshot de conteúdo abaixo da
  dobra sai em branco. Use viewport alto (`resize_window` 1440×1400) e, via
  `javascript_tool`, `display:none` nas seções acima da que você quer ver; force
  `img.loading='eager'` nas imagens lazy.

### 6. Commit e entrega

Um commit por rodada, mensagem em português, corpo dizendo **o que já existia e o que
foi criado**, e `Co-Authored-By: Codex Opus 5 <noreply@anthropic.com>`.

Feche a rodada entregando a **URL local**, o **que mudou** e as **divergências
conhecidas** — e peça a validação. A rodada só termina no aceite do usuário.

---

## Fase 2 — Fechamento

Quando a última seção da lista for aceita:

1. Liste as **pendências acumuladas** — divergências que você deixou de propósito,
   decisões tomadas sozinho, e todo texto que você redigiu sem estar no Figma.
2. Ofereça o merge na `main` (`--no-ff`, para os commits por seção continuarem
   visíveis). **Não faça push sem pedir** — a `main` local costuma estar à frente da
   `origin/main`, e o push publicaria mais do que esta landing.

---

## Regras que não se negociam

Resumo de `AGENTS.md` e `docs/code-standards.md`; o design system está em
[`criar-pagina/references/padroes-tema.md`](../criar-pagina/references/padroes-tema.md).

- `if ( ! defined( 'ABSPATH' ) ) { exit; }` no topo de todo arquivo PHP.
- Prefixo `cliconnect_`, text-domain `cli`.
- Coalescência nula em tudo que vem de ACF: `get_field('x') ?? ''`. Nos template-parts de
  solução, leia com `cliconnect_campo_pagina()` / `cliconnect_lista_numerada_pagina()`,
  que leem do objeto consultado.
- Escape em toda saída: `esc_html` / `esc_attr` / `esc_url` / `wp_kses_post`.
- Zero texto fixo no PHP — título, parágrafo e rótulo de botão vêm do ACF/CPT. Só rótulo
  de interface fica em `esc_html_e( '…', 'cli' )`.
- CSS só com tokens de `theme.css`. Ritmo vertical padrão das seções:
  `padding-block: 120px 80px`.
- ACF Free por código, sem Repeater: lista fixa vira campo numerado, lista que cresce
  vira CPT com `relationship`.
- Sem FSE, sem build, sem plugin novo.

**Texto que não está no Figma** (respostas de FAQ com o accordion fechado, por exemplo)
é rascunho: marque como provisório no docblock do método de seed, no commit e na entrega.
