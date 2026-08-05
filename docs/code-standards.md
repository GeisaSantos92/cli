# Padrões de escrita de código

Como escrever PHP, HTML, CSS e JS neste tema. Segue os WordPress Coding Standards com os
ajustes abaixo. Substitua `starter`/`cli` pelo prefixo/text-domain do projeto
(via `bin/rename.sh` — ver SETUP.md).

## PHP

- **Guarda no topo** de todo arquivo que não deva ser acessado direto:
  ```php
  if ( ! defined( 'ABSPATH' ) ) { exit; }
  ```
- **`functions.php` = maestro.** Só carrega módulos via `cliconnect_require()`:
  ```php
  cliconnect_require( '/inc/enqueue.php' );
  ```
  Nada de lógica de negócio aqui.
- **Prefixe** funções, hooks e handles com o prefixo do projeto (`cliconnect_` no starter)
  para evitar colisão.
- **Coalescência nula** em dados externos (ACF/Customizer/meta). PHP 8 dá Fatal Error com
  `null` em funções de string:
  ```php
  $titulo = get_field( 'hero_titulo' ) ?? '';
  echo esc_html( trim( $titulo ) );
  ```
- **Sem propriedades dinâmicas** em classes — declare todas explicitamente.
- **Evite `declare(strict_types=1)`** em arquivos que ligam em hooks do WP (o Core/plugins
  podem passar tipos inesperados).
- **Use APIs do WordPress**, não PHP cru: `wp_remote_get()`/`wp_remote_post()` (não cURL /
  `file_get_contents` remoto), Transient API para cache, `wp_kses_post()` para sanitizar.
- **Blinde dependências de plugin** antes de usar: `if ( function_exists('get_field') )`.

## Segurança — escaping na saída (obrigatório)

Escape **no último instante**, junto do `echo`. Escolha a função pelo contexto:

| Contexto | Função |
| --- | --- |
| Texto dentro de tags | `esc_html()` |
| Atributos (`class`, `id`, `value`, `data-*`) | `esc_attr()` |
| URLs (`href`, `src`) | `esc_url()` |
| HTML rico confiável (ex.: `the_content`) | `wp_kses_post()` |
| Strings traduzíveis com escape | `esc_html_e()` / `esc_html__()` |

```php
<a href="<?php echo esc_url( get_field('link') ?? '#' ); ?>"
   class="<?php echo esc_attr( $classe ); ?>">
  <?php echo esc_html( $rotulo ); ?>
</a>
```

## Internacionalização (i18n)

Todo texto estático em função de tradução com o text-domain do tema
(**`cli`** no starter):
```php
esc_html_e( 'Ler mais', 'cli' );
$label = __( 'Enviar', 'cli' );
```

## Enfileiramento de assets (`inc/enqueue.php`)

- Cache-busting com `cliconnect_asset_version()` (`filemtime()` com fallback).
- **CSS/JS por contexto**: carregue condicionalmente (`is_front_page()`,
  `is_post_type_archive()`, `is_singular()`) — não jogue tudo no global.
- Nunca use `<link>`/`<script>` direto em `header.php`/`footer.php`.

## HTML

- **Semântica**: `<header>`, `<main id="primary">`, `<section>`, `<article>`, `<footer>`.
  Reduza "divite" (divs aninhadas desnecessárias).
- **Um único `<h1>` por página.** Em internas, use `the_title()`.
- **Acessibilidade**: skip-link antes do header, `aria-label` em botões só-ícone,
  `aria-expanded` em toggles, `:focus-visible` visível, `alt` significativo (ou vazio se
  decorativo).
- **Menus**: `wp_nav_menu()` com `container` e `menu_class` ajustados; estilize os
  seletores nativos no CSS. Walker custom só quando o design exigir (mega menu).
- **Imagens**: `wp_get_attachment_image( $id, 'tamanho' )` — nunca hardcode `src`.

## Componentes (`template-parts/`)

Recebem dados por `$args`, não pelo loop global:
```php
get_template_part( 'template-parts/card', 'projeto', [
  'titulo' => get_the_title(),
  'imagem' => get_post_thumbnail_id(),
] );
```
Dentro do componente: `$titulo = $args['titulo'] ?? '';`

Exceção: os cards genéricos de archive (`content.php`, `content-{post_type}.php`) leem o
loop global de propósito.

## CSS

- Base e componentes globais em `assets/css/theme.css`; identidade visual nas custom
  properties do topo do arquivo (`--cor-primaria`, etc.).
- Estilos específicos de página em arquivos próprios, enfileirados por contexto.
- Nada de cor/estilo inline em PHP — aparência vive em classes CSS.
