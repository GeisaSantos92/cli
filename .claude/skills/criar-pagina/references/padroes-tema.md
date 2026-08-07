# Padrões do tema — o que já existe (use antes de criar)

Mapa do design system e dos helpers do CLI Connect. **Toda página nova deve ser montada
com estas peças**; criar equivalente novo só quando o layout exigir algo que não existe,
e ainda assim seguindo a mesma nomenclatura.

---

## 1. Tokens CSS (`assets/css/theme.css`, `:root`)

Espelham as variáveis do Figma. **Nunca escreva hex, px de tipografia ou raio literal**
num CSS de página sem antes procurar o token correspondente.

### Cores

| Token | Valor | Uso típico |
|---|---|---|
| `--cor-primaria` | `#3551f2` | botão primário, links, destaques |
| `--cor-primaria-escura` | `#0041ba` | hover do primário |
| `--cor-marca-profunda` | `#002057` | fundos escuros, rodapé |
| `--cor-marca-secundaria` | `#4a64f5` | gradientes, detalhes |
| `--cor-texto` | `#1f242d` | corpo e títulos |
| `--cor-texto-suave` | `#4a5364` | texto secundário |
| `--cor-texto-alt` | `#566f9d` | subtítulos, apoio em fundo claro |
| `--cor-texto-marca` | `#3551f2` | trecho destacado do título |
| `--cor-texto-marca-escura` | `#003187` | título sobre fundo azul claro |
| `--cor-texto-clara` | `#ffffff` | texto sobre fundo escuro |
| `--cor-texto-clara-suave` | `#9dbafe` | apoio sobre fundo escuro |
| `--cor-fundo` | `#ffffff` | superfície padrão |
| `--cor-fundo-suave` | `#f5f9ff` | seção alternada |
| `--cor-fundo-azul` | `#e7efff` | cartão/pill azul claro |
| `--cor-positivo` | `#02542d` | status positivo |
| `--cor-borda` | `rgba(39,62,128,.1)` | borda de card |
| `--cor-borda-sutil` | `rgba(39,62,128,.05)` | divisórias |

### Tipografia

| Token | Família / tamanho |
|---|---|
| `--fonte-titulo` | Rajdhani (h1–h6) |
| `--fonte-base` | Inter (corpo) |
| `--fonte-eyebrow` | Mona Sans (`.eyebrow`) |
| `--fonte-botao` | Nunito Sans (`.botao`) |
| `--tam-h1` … `--tam-h6` | 80 / 60 / 48 / 36 / 28 / 20 px (fluidos por `clamp`) |
| `--tam-body-lg` `--tam-body` `--tam-body-sm` `--tam-caption` | 18 / 16 / 14 / 13 px |

`h1`–`h6` já vêm estilizados globalmente. Para uma seção cujo título é menor que o H2
padrão (60px), **não redeclare tudo**: só sobrescreva `font-size`/`line-height`/
`letter-spacing` para o token certo, como `front-page.css` faz no bloco "Escalas de
título por seção".

Fontes são **auto-hospedadas** em `assets/fonts/` (`assets/css/fonts.css`). Nunca
adicione Google Fonts/CDN.

### Layout

| Token | Valor |
|---|---|
| `--largura-conteudo` | `1312px` |
| `--margem-lateral` | `40px` (→ `24px` em ≤768px) |
| `--secao-espacamento` | `clamp(56px, 7vw, 120px)` |
| `--raio` / `--raio-card` / `--raio-pill` | `8px` / `12px` / `999px` |
| `--sombra-card` / `--sombra-flutuante` | ver `theme.css` |

---

## 2. Classes globais (já existem — reutilize)

| Classe | O que faz |
|---|---|
| `.container` | largura máxima + margem lateral |
| `.secao` | `padding-block: var(--secao-espacamento)` |
| `.secao--azul` | variante de fundo azul |
| `.eyebrow`, `.eyebrow--pill`, `.eyebrow--clara` | selo acima do título |
| `.texto-marca` | trecho em azul dentro de texto |
| `.botao` + `.botao--primario` / `--branco` / `--contorno` / `--compacto` | botões |
| `.link-seta` | link textual com seta |
| `.visually-hidden` | texto só para leitor de tela |
| `.pagination*` | paginação (`cliconnect_pagination_render()`) |

Convenção de nomes: **BEM em português** — `bloco__elemento` e `bloco--modificador`,
com o bloco batendo com o nome do template-part (`boomi.php` → `.boomi`,
`.boomi__grid`, `.boomi__cartao`).

---

## 3. Helpers PHP (`inc/helpers.php`)

| Função | Uso |
|---|---|
| `cliconnect_campo( $nome, $padrao )` | campo ACF **da home** (lê `page_on_front`) |
| `cliconnect_botao( $link, $classes, $icone )` | renderiza botão a partir de campo Link (array ou nome de campo da home) |
| `cliconnect_imagem_tema( $arquivo, $attrs )` | `<img>` de `assets/img/` (ilustração fechada); string vazia se o arquivo não existe |
| `cliconnect_posts( $post_type, $limite, $extra )` | posts de CPT ordenados por `menu_order` |
| `cliconnect_thumb( $post_id, $tamanho, $attrs )` | imagem destacada como `<img>` |
| `cliconnect_campo_imagem( $campo, $tamanho, $attrs )` | imagem de campo ACF **da home** |
| `cliconnect_lista_numerada( $molde, $total, $cb )` | agrupa campos `prefixo_1..n` (substituto do Repeater) |
| `cliconnect_enfase( $texto, $tag )` | aplica `*ênfase*` escapando o resto |
| `cliconnect_logo_integracao( $nome )` | logo do CPT `cli_integracao` pelo nome |

> `cliconnect_campo()` e `cliconnect_campo_imagem()` são **exclusivas da home**. Para
> páginas internas use `cliconnect_campo_pagina()` — ver
> [`estrutura-pagina.md`](estrutura-pagina.md), seção "Infra".

`cliconnect_icone( $nome, $tamanho )` (`inc/icons.php`) devolve SVG inline 24×24 herdando
`currentColor`. Chaves disponíveis:

`seta-direita`, `seta-nordeste`, `chevron-baixo`, `chevron-cima`, `play`, `mais`,
`portal`, `whatsapp`, `email`, `telefone`, `vendas`, `fiscal`, `logistica`, `credito`,
`suporte`, `estoque`, `agenda`, `automacao`.

Ícone novo → acrescente o `path` (estilo Material Symbols, viewBox 24) ao array de
`cliconnect_icone()`; **não** crie arquivo SVG solto nem use biblioteca externa.

---

## 4. CPTs e taxonomias (`inc/cpt.php`)

| CPT | Conteúdo |
|---|---|
| `cli_agente` | agentes de IA (ícone, descrição, status, integrações) |
| `cli_integracao` | sistemas integrados (logo na imagem destacada) |
| `cli_cliente` | logos de clientes/parceiros |
| `cli_case` | cases de sucesso (depoimento, métricas) |
| `cli_evento` | cards de eventos automáticos |
| `cli_faq` | perguntas frequentes |
| `cli_selo` | selos de compliance |

**Regra do ACF Free:** sem Repeater/Group/Gallery. Toda lista que pode crescer vira CPT;
só conjunto de tamanho fixo (métricas, departamentos) usa campos numerados
`prefixo_1..n` + `cliconnect_lista_numerada()`.

Antes de criar CPT novo, cheque se um existente serve (ex.: uma página de "Soluções"
provavelmente lista `cli_integracao` e `cli_case`, não precisa de CPT próprio).

---

## 5. Referências de composição (copie a estrutura, não o conteúdo)

Seções da home que valem como modelo, em `template-parts/home/`:

| Arquivo | Padrão útil |
|---|---|
| `hero.php` | hero centralizado, título em duas linhas, botão do ACF |
| `boomi.php` | grid texto + ilustração fechada, `return` cedo se vazio |
| `midia.php` | bloco texto/imagem alternado, recebe `$args` do template |
| `agentes.php` | esteira/carrossel de CPT em CSS puro (trilha duplicada) |
| `metricas.php` | conjunto fixo em campos numerados |
| `faq.php` | acordeão acessível (JS em `assets/js/theme.js`) |
| `blog.php` | listagem de posts com `WP_Query` |
| `compliance.php` | grade de logos/selos de CPT |

---

## 6. Fora de escopo (não faça)

- Full Site Editing, `theme.json` estrutural, blocos que salvem markup no banco.
- Bundler, npm, Tailwind compilado no runtime do tema.
- Plugin novo. O site roda só com **ACF Free** e **Polylang**.
- Campos ACF criados pelo painel — sempre `acf_add_local_field_group()` em código.
- Dado global (telefone, redes, logo) em ACF — isso é **Customizer**
  (`inc/customizer.php`).
