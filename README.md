# CLI Connect — starter de tema clássico do WordPress

Starter **em branco** para desenvolvimento de temas clássicos do WordPress: sem marca,
sem conteúdo, só infraestrutura pronta e **códigos de exemplo**. O foco é
**performance**, **segurança** (escaping estrito) e **baixa dependência** de plugins e
ferramentas de build.

> **Para iniciar um projeto novo:** preencha a ficha e siga o processo em
> [`SETUP.md`](./SETUP.md) — nome do tema, logos, cores, funcionalidades e renomeação
> automatizada (`bin/rename.sh`).
>
> **Desenvolvimento assistido por IA:** comece por [`CLAUDE.md`](./CLAUDE.md).

## O que vem pronto

- **Maestro `functions.php`**: setup do tema + `cliconnect_require()` dos módulos de `inc/`.
- **Chrome completo**: `header.php` (skip-link, logo do Customizer, menu mobile
  acessível), `footer.php` (menus de rodapé, redes sociais condicionais, copyright).
- **Hierarquia básica**: `index.php`, `page.php`, `single.php`, `archive.php`,
  `search.php`, `404.php` + `template-parts/` (cards, "nada encontrado", paginação).
- **Módulos `inc/`**: enqueue com `filemtime()`, deep clean do `wp_head`, Customizer
  (logos, redes sociais, contatos, GA/GTM), analytics condicional, paginação numerada,
  login white-label, SMTP → MailPit em dev.
- **Códigos de exemplo**: `inc/cpt-exemplo.php` (CPT + taxonomia) e
  `inc/acf-fields-exemplo.php` (grupo ACF local) — requires comentados em
  `functions.php`; duplique e renomeie por conteúdo real.
- **Landing pages com blocos (opcional)**: blocos de seção server-rendered sem build
  (`block.json` + `render.php` + `editor.js` em JS puro), template canvas e allowlist —
  ver [`docs/blocks.md`](./docs/blocks.md).
- **Tooling**: `bin/rename.sh` (renomeação do starter), `bin/wp` (WP-CLI no LocalWP sem
  site shell) e stub de comandos custom em `inc/cli/bootstrap.php`.
- **CSS base neutro**: `assets/css/theme.css` com custom properties (`--cor-primaria`,
  etc.) — troque a paleta e o tema inteiro acompanha.

## Filosofia & stack

- **Arquitetura:** Classic Theme hierárquico em PHP. **Sem** FSE / blocos estruturais.
- **Front-end:** HTML5 semântico, CSS com custom properties, JS vanilla. **Sem bundler
  local** — assets estáticos enfileirados via `wp_enqueue_*`.
- **Conteúdo dinâmico:** CPTs + **ACF Free registrado via código PHP**.
- **Dados globais:** **Customizer API** nativa.
- **Mídia:** imagens por **ID de anexo** (`wp_get_attachment_image()`) → `srcset`/`sizes`.
- **Segurança:** *escaping* estrito na saída, coalescência nula em todo dado externo.

## Documentação

| Documento | Conteúdo |
| --- | --- |
| [`SETUP.md`](./SETUP.md) | Ficha do projeto + processo de setup inicial |
| [`CLAUDE.md`](./CLAUDE.md) | Regras essenciais + índice (Progressive Disclosure) |
| [`docs/architecture.md`](./docs/architecture.md) | Decisões de arquitetura e o *porquê* |
| [`docs/code-standards.md`](./docs/code-standards.md) | Padrões de PHP, segurança, HTML, CSS |
| [`docs/best-practices.md`](./docs/best-practices.md) | Longevidade, performance, plugins |
| [`docs/project-structure.md`](./docs/project-structure.md) | Estrutura, convenções, como crescer |
| [`docs/blocks.md`](./docs/blocks.md) | Landing pages com blocos (padrão sem build) |
| [`docs/wp-cli.md`](./docs/wp-cli.md) | Wrapper `bin/wp` + receitas de WP-CLI |
| [`docs/local-env.md`](./docs/local-env.md) | LocalWP, debug, MailPit |
