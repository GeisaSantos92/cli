# CLAUDE.md

Tema **clássico** do WordPress do site **CLI Connect** (Agência R8 — dev: Danilo Mello).
Prioridades: **performance**, **segurança** e **baixa dependência** de plugins/build.

> O tema nasceu do starter em branco da R8; o processo original está em `SETUP.md`
> (já executado). Plugins em uso: **ACF Free** e **Polylang** (pt/en).

## Regras que nunca mudam

- **Classic Theme, PHP hierárquico.** Nada de FSE/blocos estruturais. Exceção
  controlada: blocos de **conteúdo** para landing pages (opcional, `inc/blocks.php` —
  server-rendered, sem build; ver `docs/blocks.md`).
- **Sem build local.** Assets (`assets/css/`, `assets/js/`) são estáticos e entram via
  `wp_enqueue_`* — nunca `<link>`/`<script>` no HTML.
- **Escape sempre na saída:** `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`.
- **Coalescência nula** em todo dado do ACF/Customizer: `get_field('x') ?? ''`.
- `functions.php` **é só maestro:** apenas `cliconnect_require()` dos arquivos de `inc/`.
- **ACF Free via código** (`acf_add_local_field_group`), nunca pelo painel. Repetições →
  CPT. Dados globais → **Customizer**, não ACF.
- **APIs do WordPress** no lugar de PHP cru (`wp_remote_get`, Transients, etc.).
- **Prefixo `cliconnect_` / text-domain `cli`** em todo código novo.
- **Fontes auto-hospedadas** em `assets/fonts/` (Rajdhani, Inter, Mona Sans,
  Nunito Sans) — nunca CDN do Google Fonts.
- **Menus com Polylang:** ao criar/atribuir menus por código, registre também em
  `polylang['nav_menus'][tema][location][idioma]`, senão o front renderiza vazio.

## Onde aprofundar (leia sob demanda)

- Setup inicial de um projeto novo (ficha + processo) → `SETUP.md`
- Decisões de arquitetura e o porquê → `docs/architecture.md`
- Como escrever código (PHP/HTML/CSS/segurança) → `docs/code-standards.md`
- Boas práticas de longevidade e plugins → `docs/best-practices.md`
- Estrutura de arquivos e como o tema cresce → `docs/project-structure.md`
- Landing pages com blocos (padrão sem build) → `docs/blocks.md`
- WP-CLI (`bin/wp`) e receitas → `docs/wp-cli.md`
- Ambiente LocalWP, debug, MailPit → `docs/local-env.md`

## Estado atual

**Home implementada e populada**, fiel ao Figma (arquivo `CLI Connect`, frame `Home`).

- **Home:** `front-page.php` orquestra 17 template-parts em `template-parts/home/`.
  Nada de texto fixo: tudo vem do grupo ACF da página inicial
  (`inc/acf-fields-home.php`, ~85 campos em abas por seção) ou dos CPTs.
- **CPTs** (`inc/cpt.php`): `cli_agente`, `cli_integracao`, `cli_cliente`, `cli_case`,
  `cli_evento`, `cli_faq`, `cli_selo` + taxonomias de categoria/segmento.
  Como o ACF Free não tem Repeater, **toda lista que cresce é CPT**; só conjuntos
  fixos (métricas, departamentos) são campos numerados.
- **Chrome:** header com painéis de menu montados pela própria árvore do menu
  (`Cliconnect_Walker_Nav_Menu`: filhos sem netos → cartões, filhos com netos →
  mega menu em colunas), seletor de idioma do Polylang e dois botões do
  Customizer; rodapé em colunas pelo menu `rodape` sobre o planeta, faixa da
  agência (`template-parts/rodape-agencia.php`), CTA azul, botão de voltar ao
  topo e botão flutuante de WhatsApp.
- **Design system:** tokens do Figma em `assets/css/theme.css`; seções da home em
  `assets/css/front-page.css` (enfileirado só em `is_front_page()`).
- **Seed:** `./bin/wp cliconnect seed [--reset]` (`inc/cli/seed.php`) importa os 49
  assets de `assets/seed/`, cria páginas, CPTs, posts, menus e theme mods.
  É idempotente (meta `_cliconnect_seed`).
- **Site em inglês:** a camada `en` também vem do seed — `./bin/wp cliconnect seed
  --traducao` roda só ela. Mecânica em `inc/cli/seed-en.php` (trait) e texto em
  `seed-en-paginas.php`, `seed-en-cpts.php`, `seed-en-faq.php` e
  `seed-en-solucoes.php`. Cada tradução copia todos os campos ACF do original e
  sobrescreve só o texto, então seção nova em português já nasce presente no inglês.
  Strings de interface em `languages/en_US.po|mo` (nome é `{locale}.mo`, não
  `{dominio}-{locale}.mo`); integração do tema com o plugin em `inc/polylang.php`.

Ilustrações fechadas (com texto embutido na arte) são assets estáticos em
`assets/img/`, renderizados por `cliconnect_imagem_tema()`: pilha de camadas,
cartão da Boomi, anel de canais do suporte, planeta do rodapé e os logos em SVG.
Continuam em CSS o diagrama de departamentos e a órbita de logos do hero.

## Ambiente

LocalWP + `WP_DEBUG` ligado. Auditoria local: Query Monitor e Show Current Template.
WP-CLI via `bin/wp` (config por máquina em `bin/wp.config.sh`, não versionada).
