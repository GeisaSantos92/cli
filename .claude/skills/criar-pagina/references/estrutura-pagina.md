# Estrutura de uma página nova — arquivos e moldes

Uma página mapeada com ACF toca **6 lugares**. Ordem de criação importa (cada passo
depende do anterior).

```
inc/helpers.php                     ← infra de página (uma vez por projeto)
inc/acf-fields-{slug}.php           ← grupo ACF da página        (novo)
template-parts/{slug}/{secao}.php   ← uma seção por arquivo      (novos)
page-{slug}.php                     ← orquestrador               (novo)
assets/css/page-{slug}.css          ← estilos da página          (novo)
inc/enqueue.php                     ← enfileira o CSS por contexto
functions.php                       ← cliconnect_require() do grupo ACF
inc/cli/seed.php                    ← conteúdo (ver seed.md)
```

---

## 0. Infra — helpers de página (criar **uma vez**, se ainda não existirem)

`cliconnect_campo()` lê só a home (`page_on_front`). Páginas internas precisam destes
três helpers em `inc/helpers.php`. **Cheque com `grep` antes de adicionar** — eles podem
já ter sido criados por uma página anterior.

```php
/**
 * IDs de uma página do site pelo slug, incluindo as traduções do Polylang.
 *
 * Resolver por slug (e não por ID fixo) mantém o código válido em qualquer
 * ambiente: local, homologação e produção têm IDs diferentes.
 *
 * @param string $slug Slug da página (post_name).
 * @return int[] IDs encontrados (vazio se a página ainda não existe).
 */
function cliconnect_pagina_ids( $slug ) {
	static $cache = array();

	$slug = (string) $slug;

	if ( isset( $cache[ $slug ] ) ) {
		return $cache[ $slug ];
	}

	$pagina = get_page_by_path( $slug );

	if ( ! $pagina ) {
		$cache[ $slug ] = array();

		return $cache[ $slug ];
	}

	$ids = array( (int) $pagina->ID );

	if ( function_exists( 'pll_get_post_translations' ) ) {
		foreach ( pll_get_post_translations( $pagina->ID ) as $traducao ) {
			$ids[] = (int) $traducao;
		}
	}

	$cache[ $slug ] = array_values( array_unique( array_filter( $ids ) ) );

	return $cache[ $slug ];
}

/**
 * Verifica se a requisição atual é uma página do site (ou sua tradução).
 *
 * @param string $slug Slug da página.
 * @return bool
 */
function cliconnect_e_pagina( $slug ) {
	if ( ! is_page() ) {
		return false;
	}

	return in_array( (int) get_queried_object_id(), cliconnect_pagina_ids( $slug ), true );
}

/**
 * Lê um campo ACF da página em exibição.
 *
 * Equivalente de cliconnect_campo() para páginas internas: em vez da home,
 * lê o objeto consultado (funciona também nas traduções do Polylang).
 *
 * @param string $nome   Nome do campo.
 * @param mixed  $padrao Valor devolvido quando o campo está vazio.
 * @return mixed
 */
function cliconnect_campo_pagina( $nome, $padrao = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $padrao;
	}

	$id = (int) get_queried_object_id();

	if ( ! $id ) {
		return $padrao;
	}

	$valor = get_field( $nome, $id ) ?? '';

	if ( '' === $valor || null === $valor || array() === $valor ) {
		return $padrao;
	}

	return $valor;
}
```

### Polylang: template da tradução (só se a página for traduzida)

A hierarquia do WordPress casa `page-{slug}.php` pelo `post_name`. A tradução tem outro
slug (`/en/platform/`) e cairia em `page.php`. Um filtro resolve isso de uma vez para
todas as páginas do tema — adicione a `inc/helpers.php` **apenas quando a primeira
página traduzida existir**:

```php
/**
 * Usa o template da página original nas traduções do Polylang.
 *
 * page-{slug}.php casa pelo post_name; a tradução tem slug próprio e cairia
 * em page.php. Aqui a tradução herda o template do original.
 *
 * @param string $template Caminho do template escolhido pela hierarquia.
 * @return string
 */
function cliconnect_template_pagina_traduzida( $template ) {
	if ( ! is_page() || ! function_exists( 'pll_get_post_translations' ) ) {
		return $template;
	}

	if ( 'page.php' !== basename( $template ) ) {
		return $template;
	}

	foreach ( pll_get_post_translations( get_queried_object_id() ) as $id ) {
		$candidato = get_theme_file_path( '/page-' . get_post_field( 'post_name', $id ) . '.php' );

		if ( file_exists( $candidato ) ) {
			return $candidato;
		}
	}

	return $template;
}
add_filter( 'template_include', 'cliconnect_template_pagina_traduzida' );
```

---

## 1. Grupo ACF — `inc/acf-fields-{slug}.php`

Padrão idêntico ao de `inc/acf-fields-home.php`: helpers locais de campo, abas
numeradas por seção, `message` documentando o que vem de CPT ou de asset do tema.

```php
<?php
/**
 * Grupo ACF da página "{Título}".
 *
 * Todo o texto da página é editável aqui — nada de conteúdo fixo no template.
 * Listas que crescem vêm de CPT (inc/cpt.php); conjuntos de tamanho fixo usam
 * campos numerados (o ACF Free não tem Repeater).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Localização do grupo: a página do slug e suas traduções.
 *
 * Cada regra vira um grupo OR. Se a página ainda não existe (antes do seed),
 * devolve uma regra que não casa com nada — o grupo continua registrado, e por
 * isso update_field()/get_field() seguem funcionando pelo nome do campo.
 *
 * @param string $slug Slug da página.
 * @return array
 */
function cliconnect_acf_local_pagina( $slug ) {
	$ids      = function_exists( 'cliconnect_pagina_ids' ) ? cliconnect_pagina_ids( $slug ) : array();
	$location = array();

	foreach ( $ids as $id ) {
		$location[] = array(
			array(
				'param'    => 'page',
				'operator' => '==',
				'value'    => (string) $id,
			),
		);
	}

	if ( ! $location ) {
		$location[] = array(
			array(
				'param'    => 'page',
				'operator' => '==',
				'value'    => '0',
			),
		);
	}

	return $location;
}

/**
 * Registra o grupo de campos da página.
 *
 * @return void
 */
function cliconnect_acf_fields_{slug_php}() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$fields = array();

	/* --- 1. HERO --------------------------------------------------------- */
	$fields[] = array(
		'key'       => 'field_{slug}_tab_hero',
		'label'     => '1 · Hero',
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'left',
	);
	$fields[] = array(
		'key'   => 'field_{slug}_hero_eyebrow',
		'label' => 'Selo acima do título',
		'name'  => '{slug}_hero_eyebrow',
		'type'  => 'text',
	);
	$fields[] = array(
		'key'   => 'field_{slug}_hero_titulo',
		'label' => 'Título',
		'name'  => '{slug}_hero_titulo',
		'type'  => 'text',
	);
	$fields[] = array(
		'key'       => 'field_{slug}_hero_texto',
		'label'     => 'Texto de apoio',
		'name'      => '{slug}_hero_texto',
		'type'      => 'textarea',
		'rows'      => 2,
		'new_lines' => '',
	);
	$fields[] = array(
		'key'           => 'field_{slug}_hero_botao',
		'label'         => 'Botão',
		'name'          => '{slug}_hero_botao',
		'type'          => 'link',
		'return_format' => 'array',
	);

	/* --- 2. ... ---------------------------------------------------------- */

	acf_add_local_field_group(
		array(
			'key'             => 'group_cli_{slug}',
			'title'           => 'Conteúdo da Página {Título}',
			'location'        => cliconnect_acf_local_pagina( '{slug}' ),
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'default',
			'label_placement' => 'top',
			'hide_on_screen'  => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'          => $fields,
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_{slug_php}' );
```

**Convenções obrigatórias**

- Chave do grupo: `group_cli_{slug}` · chaves de campo: `field_{slug}_*`.
- Nome do campo prefixado pelo slug (`plataforma_hero_titulo`): evita colisão de nomes
  entre grupos, porque no ACF o `name` é global na hora do `update_field()`.
- Uma **aba por seção**, numerada na mesma ordem em que a seção aparece na página.
- Campo `message` sempre que a seção não for editável ali (vem de CPT, ou é ilustração
  fechada em `assets/img/`) — quem edita precisa entender por que o campo não existe.
- Tipos permitidos (ACF Free): `text`, `textarea`, `wysiwyg`, `link`, `image`,
  `select`, `true_false`, `number`, `post_object`, `relationship`, `tab`, `message`.
- Campo de imagem: `'return_format' => 'id'`.
- `cliconnect_acf_local_pagina()` é compartilhada — declare-a **uma vez** (no primeiro
  `inc/acf-fields-{slug}.php` criado, ou em `inc/helpers.php`) e proteja com
  `if ( ! function_exists( ... ) )` se houver risco de duplicar.

---

## 2. Template-part de seção — `template-parts/{slug}/{secao}.php`

```php
<?php
/**
 * {Título} — "{nome da seção no layout}".
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = cliconnect_campo_pagina( '{slug}_hero_eyebrow' );
$titulo  = cliconnect_campo_pagina( '{slug}_hero_titulo' );
$texto   = cliconnect_campo_pagina( '{slug}_hero_texto' );
$botao   = cliconnect_campo_pagina( '{slug}_hero_botao', array() );

if ( ! $titulo ) {
	return;
}
?>

<section class="secao {slug}-hero">
	<div class="container">

		<?php if ( $eyebrow ) : ?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>

		<h1 class="{slug}-hero__titulo"><?php echo esc_html( $titulo ); ?></h1>

		<?php if ( $texto ) : ?>
			<p class="{slug}-hero__texto"><?php echo esc_html( $texto ); ?></p>
		<?php endif; ?>

		<?php cliconnect_botao( $botao ); ?>

	</div>
</section>
```

Regras:

- Leitura dos campos **no topo**, marcação depois — nada de `get_field()` no meio do HTML.
- `return` cedo quando a seção não tem conteúdo (é assim que o cliente "desliga" uma
  seção pelo painel).
- Um único `<h1>` na página inteira (normalmente no hero); as demais seções usam `<h2>`.
- `alt` significativo em imagem de conteúdo, `alt=""` em decorativa.
- Se a seção precisar de dado do orquestrador (índice, variante), receba por `$args`:
  `$indice = $args['indice'] ?? 1;`.
- `wp_kses_post()` só para campo `wysiwyg`; `esc_html()` para o resto.

---

## 3. Orquestrador — `page-{slug}.php`

```php
<?php
/**
 * Página "{Título}".
 *
 * Só orquestra: cada seção do layout vira um template-part em
 * template-parts/{slug}/. Todo o conteúdo vem do ACF da própria página
 * (inc/acf-fields-{slug}.php) ou dos CPTs de catálogo (inc/cpt.php).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-{slug}">
	<?php
	$cliconnect_secoes = array(
		'hero',
		'recursos',
		'faq',
	);

	foreach ( $cliconnect_secoes as $cliconnect_secao ) {
		get_template_part( 'template-parts/{slug}/' . $cliconnect_secao );
	}
	?>
</main>

<?php
get_footer();
```

Variáveis em arquivo de template são globais: **prefixe com `cliconnect_`** para não
colidir com o Core/plugins (mesmo padrão de `front-page.php`).

---

## 4. CSS — `assets/css/page-{slug}.css`

```css
/* ==========================================================================
   CLI Connect — estilos da página "{Título}" (page-{slug}.php).

   Enfileirado só em cliconnect_e_pagina('{slug}') (inc/enqueue.php). Depende
   dos tokens declarados em theme.css.
   ========================================================================== */

/* ==========================================================================
   1. HERO
   ========================================================================== */

.{slug}-hero {
	text-align: center;
	background: var(--cor-fundo-suave);
}

.{slug}-hero__titulo {
	max-width: 20ch;
	margin-inline: auto;
	text-wrap: balance;
}
```

- Seções numeradas na mesma ordem do orquestrador, com cabeçalho de comentário.
- **Só tokens.** Valor literal exige comentário justificando (ex.: largura tirada
  direto do Figma para travar a quebra de linha).
- Mobile: siga o breakpoint já usado no tema (`@media (max-width: 768px)`), no fim do
  arquivo ou no fim de cada bloco de seção — mas de forma consistente.
- Nada de `!important`, nada de seletor de tag solto (`section`, `div`) que vaze para
  fora da página.

---

## 5. Enfileiramento — `inc/enqueue.php`

Dentro de `cliconnect_enqueue_assets()`, depois dos blocos existentes:

```php
	// Página "{Título}".
	if ( cliconnect_e_pagina( '{slug}' ) ) {
		wp_enqueue_style(
			'cliconnect-page-{slug}',
			get_theme_file_uri( '/assets/css/page-{slug}.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/page-{slug}.css' )
		);
	}
```

JS só se a seção realmente exigir comportamento novo. Antes disso, verifique
`assets/js/theme.js` — acordeão, menu e toggles genéricos já estão lá; prefira
reaproveitar o mesmo padrão de atributos (`aria-expanded`, `data-*`) a escrever script
novo.

---

## 6. Registro — `functions.php`

Ao lado dos outros grupos, no bloco "Conteúdo do projeto":

```php
cliconnect_require( '/inc/acf-fields-{slug}.php' );
```

`functions.php` é só maestro: nenhuma lógica além do `cliconnect_require()`.

---

## Checklist final da implementação

- [ ] `ABSPATH` no topo de todo PHP novo.
- [ ] Nenhum texto de conteúdo fixo no PHP.
- [ ] Toda saída escapada; `?? ''` em todo dado do ACF.
- [ ] Nomes de campo prefixados pelo slug; chaves `field_{slug}_*`.
- [ ] CSS só com tokens; classes BEM com o nome do template-part.
- [ ] CSS enfileirado por contexto, com `cliconnect_asset_version()`.
- [ ] `functions.php` atualizado.
- [ ] Seed criado e executado (ver [`seed.md`](seed.md)).
