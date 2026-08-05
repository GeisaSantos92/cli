# Arquitetura e decisões

Registro das decisões de engenharia do starter e do **porquê** de cada uma. Serve como
"fonte da verdade" para escolhas que não devem ser reabertas sem motivo — todas já
validadas em produção em projeto real.

## Resumo do blueprint

| Área | Decisão | Motivo curto |
| --- | --- | --- |
| Tipo de tema | Classic Theme (PHP) do zero | Layout fixo mapeia direto para PHP, sem curva do FSE |
| Assets | Vanilla enfileirado (sem bundler) | Simplicidade; plugin de cache minifica em produção |
| Estrutura | Hierárquica + `template-parts` | Legível e sustentável; evita "index.php espaguete" |
| Conteúdo dinâmico | ACF (Free) via código | Blinda o HTML; usuário só edita formulários |
| Repetições | Custom Post Types | Substitui o Repeater (ausente no ACF Free) |
| Dados globais | Customizer API | Substitui Options Page (ausente no ACF Free) |
| Mídia | ID de anexo + `wp_get_attachment_image()` | `srcset`/`sizes` nativos, Core Web Vitals |
| Menus | `wp_nav_menu()` + ajuste de CSS | Mais rápido que Walker custom (use Walker só p/ mega menu) |
| Segurança | Escaping estrito na saída | XSS + estabilidade de layout |
| i18n | text-domain próprio + `title-tag` | Compatível com SEO e tradução |
| `wp_head` | Deep clean (emoji, feeds, generator) | HTML final enxuto |
| Analytics | Hook no `wp_head` lendo o Customizer | Sem plugin; só produção + visitante não logado |
| Listagens | Archives nativos + `pre_get_posts` | Evita 404 de paginação |
| Cache-busting | `filemtime()` | Versão automática por arquivo |
| Componentes | Dados via `$args` de `get_template_part()` | Reutilizáveis fora do loop global |
| Landing pages | Blocos server-rendered sem build (opcional) | Cliente monta seções sem quebrar o design system |
| Deploy | Migração atômica (WP Migrate) | Preserva dados serializados |

## Detalhamento

### Tema clássico, do zero
PHP hierárquico. Sem Full Site Editing nem blocos estruturais. Cada tipo de página tem seu
arquivo (`front-page.php`, `single-*.php`, `archive-*.php`).

### Assets sem bundler
Tudo em `assets/` é tratado como **arquivo estático**: nada de `node_modules` no fluxo do
tema. Enfileiramento por `wp_enqueue_scripts` com `filemtime()` para cache-busting. Se o
projeto usar CSS utilitário (ex.: Tailwind), ele entra **pré-compilado** como asset
estático — recompilar é passo externo, fora do runtime do tema. Em produção, o plugin de
cache (ex.: WP Rocket) minifica e gera Critical CSS.

### ACF Free via código
Grupos definidos em `inc/acf-fields-*.php` com `acf_add_local_field_group()`, atados ao
hook `acf/init` e blindados com `function_exists()`. Vantagem: os campos vivem no
versionamento, não no banco. Modelo de referência: `inc/acf-fields-exemplo.php`.

**Contornos das limitações do Free:**
- **Sem Repeater** → modele listas/carrosséis/grids como **CPT** e itere com `WP_Query`
  (modelo: `inc/cpt-exemplo.php`).
- **Sem Options Page** → dados globais no **Customizer** (`get_theme_mod()`).

### Dados globais no Customizer
Redes sociais, telefone, logos e tag de rastreamento (GA/GTM) ficam em
`inc/customizer.php`. O snippet de analytics é injetado por `inc/analytics.php` no
`wp_head`, **apenas fora de WP_DEBUG e para visitantes não logados**.

### Mídia responsiva por ID
Campo de imagem do ACF retorna **ID**; renderiza com
`wp_get_attachment_image( $id, 'tamanho' )` para ganhar `srcset`/`sizes`. Tamanhos custom
via `add_image_size()`.

### Componentes desacoplados
`template-parts/` recebe dados por `$args`:
`get_template_part('template-parts/card', 'projeto', $args)`. O componente lê
`$args['...']`, não o loop global — assim é reutilizável em qualquer contexto.
Exceção documentada: os cards genéricos de archive (`content*.php`) leem o loop.

### Landing pages com blocos (opcional)
Quando o cliente precisa montar landing pages sozinho, o tema oferece blocos de seção
**server-rendered** (`block.json` + `render.php`) com `editor.js` em JS puro — sem build,
sem markup serializado no banco (`save: () => null`). Restritos ao template canvas
`page-templates/landing.php` por allowlist. Detalhes: [blocks.md](blocks.md).

### Limpeza do `wp_head`
Em `inc/clean-head.php`: emojis, RSD/wlwmanifest, generator, shortlink e feeds
desnecessários. **Não** remova `wp-block-library` globalmente se o conteúdo usar blocos —
qualquer dequeue deve ser condicional.

### Deploy
Migração atômica (WP Migrate / All-in-One) para não corromper strings serializadas do PHP
(Customizer, menus). Nunca busca/substitui bruta em `.sql`.

## Recursos modernos úteis em Classic Theme

- **`theme.json`** (opcional): trava paleta/tipografia do editor e expõe variáveis CSS.
- **Block Patterns** (`/patterns/*.php` ou `register_block_pattern()`): blocos prontos
  para o cliente no editor do blog.
- **`wp_enqueue_block_style()`**: CSS de bloco carregado só quando o bloco existe.
- **Tipografia/espaçamento fluidos** via `theme.json` (se adotado).
