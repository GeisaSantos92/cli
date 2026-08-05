# Boas práticas

Diretrizes para manter o site rápido, seguro e vivo por muitos anos.

## Longevidade (código à prova de futuro)

- **Programação defensiva contra `null`**: sempre `?? ''` (ou checagem) antes de passar
  dados a funções de string. É a causa nº 1 de Fatal Error ao atualizar o PHP.
- **APIs do WP sobre PHP cru**: o Core mantém retrocompatibilidade há décadas; funções
  nativas do PHP mudam. Use `wp_remote_*`, Transients, `wp_date()`, etc.
- **Menos plugins = mais anos de vida.** Tudo que dá para resolver limpo em `inc/` deve
  ficar no tema.
- **Sem propriedades dinâmicas** e sem `strict_types` em código de hooks (ver
  [code-standards](code-standards.md)).

## Performance

- Enfileiramento condicional por contexto; `filemtime()` para cache-busting.
- Imagens por ID (`srcset` nativo) + conversão para WebP/AVIF em produção (Imagify ou
  similar).
- Deep clean do `wp_head` (emoji, feeds, generator) — ver `inc/clean-head.php`.
- Em produção, plugin de cache (WP Rocket ou similar) faz cache, minificação e Critical
  CSS.
- Opcional de elite: Perfmatters para desligar scripts de plugin por página (ex.: plugin
  de formulário só na página de contato).

## Gestão de conteúdo no painel (UX do cliente)

- **Esconda o editor** onde a página é 100% ACF:
  `remove_post_type_support( 'page', 'editor' )`.
- **Organize o ACF** com campos `Tab` por seção e `instructions` claras (ex.: proporção
  de imagem esperada) — ver `inc/acf-fields-exemplo.php`.
- **CPTs amigáveis**: `menu_icon` (dashicon) e `menu_position` coerentes.
- Rótulos e placeholders claros, no idioma do cliente.

## Plugins homologados (produção)

| Plugin | Papel |
| --- | --- |
| ACF (Free) | UI dos campos definidos em código |
| WP Rocket (ou similar) | Cache, minificação, Critical CSS |
| Rank Math SEO (ou similar) | SEO leve + Search Console no painel |
| WPForms (ou similar) | Único plugin de formulários |
| Polylang | Multilíngue leve (se o projeto precisar) |
| Imagify (ou similar) | WebP/AVIF |

**Só em desenvolvimento** (não vão para produção): Query Monitor, Show Current Template.

**Search Console / Analytics:** sem plugin dedicado. Search Console via plugin de SEO;
GA/GTM injetado por `inc/analytics.php` lendo o Customizer (só em produção).

## Plugins de risco (evitar)

- **Page/grid builders e "listagens sem código"**: prendem design/queries em shortcodes ou
  JSON no banco. Já mitigado por usarmos CPT + `WP_Query` no template.
- **Jetpack**: monolito com dependência do WordPress.com; conflita com temas puristas.
- **Utilitários de dev solo** (um plugin só p/ CPT, breadcrumb, analytics): abandono →
  Fatal Error futuro. Faça em `inc/`.
- **Otimizadores de banco agressivos**: podem corromper índices/metadados do ACF. Use a
  otimização nativa do plugin de cache.
- **Dois plugins para a mesma função** (ex.: Contact Form 7 **e** WPForms): escolha um.
