# Moldes de CPT

Código copiável, no padrão exato dos 7 CPTs existentes. Substitua `{nome}`,
`{Singular}`, `{Plural}`.

---

## 1. Registro — `inc/cpt.php`

Dentro de `cliconnect_register_post_types()`, no fim, antes do `}`.

### Catálogo (alimenta seções; sem URL própria) — o caso mais comum

```php
	// --- {Plural} ------------------------------------------------------------
	register_post_type(
		'cli_{nome}',
		array(
			'labels'        => cliconnect_cpt_labels( '{Singular}', '{Plural}' ),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-format-quote',
			'menu_position' => 28,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'   => false,
			'rewrite'       => false,
		)
	);
```

### Público (archive + single, como `cli_case`)

```php
	register_post_type(
		'cli_{nome}',
		array(
			'labels'        => cliconnect_cpt_labels( '{Singular}', '{Plural}' ),
			'public'        => true,
			'show_in_rest'  => true,
			'menu_icon'     => 'dashicons-awards',
			'menu_position' => 28,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'has_archive'   => '{slug-plural}',
			'rewrite'       => array(
				'slug'       => '{slug-plural}',
				'with_front' => false,
			),
		)
	);
```

**Decisões que importam:**

| Chave | Regra |
|---|---|
| `public` | `false` para catálogo (não gera URL, não aparece em busca), `true` só se o item tiver página própria |
| `show_ui` + `show_in_menu` | obrigatórios quando `public => false`, senão o CPT some do painel |
| `page-attributes` | sempre — é o que habilita o campo **Ordem** (`menu_order`), a ordenação usada por `cliconnect_posts()` |
| `thumbnail` | sempre que houver imagem/logo — a imagem destacada substitui campo de imagem no ACF |
| `editor` | só se o item tiver texto longo (FAQ, case). Catálogo de logo não precisa |
| `menu_position` | próximo livre (hoje 21–27 estão ocupados) |
| `menu_icon` | dashicon que faça sentido — evite repetir os já usados |

**Gênero nas labels:** `cliconnect_cpt_labels( 'Integração', 'Integrações', 'f' )`.
Sem o `'f'` o painel escreve "Nenhum Integração encontrado".

**Não esqueça** de atualizar o docblock no topo de `inc/cpt.php` (lista os CPTs) e, se
o CPT for de catálogo ordenável, o array em `cliconnect_admin_order_cpts()`:

```php
	$ordenaveis = array( 'cli_agente', 'cli_integracao', ..., 'cli_{nome}' );
```

---

## 2. Taxonomia — `inc/cpt.php`

Dentro de `cliconnect_register_taxonomies()`:

```php
	register_taxonomy(
		'cli_{taxonomia}',
		array( 'cli_{nome}' ),
		array(
			'labels'            => array(
				'name'          => '{Plural da taxonomia}',
				'singular_name' => '{Singular da taxonomia}',
				'menu_name'     => '{Plural}',
				'all_items'     => 'Tod{o|a}s {o|a}s {Plural}',
				'edit_item'     => 'Editar {Singular}',
				'add_new_item'  => 'Adicionar {Singular}',
			),
			'public'            => false,   // true só se os termos tiverem URL
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,    // true = categoria, false = tag
			'rewrite'           => false,   // array( 'slug' => '...' ) se public
		)
	);
```

---

## 3. Grupo ACF — `inc/acf-fields-cpt.php`

Dentro de `cliconnect_acf_fields_cpt()`, no fim:

```php
	/* =====================================================================
	   {SINGULAR EM CAIXA ALTA}
	   ===================================================================== */
	acf_add_local_field_group(
		array(
			'key'            => 'group_cli_{nome}',
			'title'          => 'Dados d{o|a} {Singular}',
			'location'       => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'cli_{nome}',
					),
				),
			),
			'menu_order'     => 0,
			'position'       => 'normal',
			'hide_on_screen' => array( 'the_content', 'excerpt', 'custom_fields', 'discussion', 'comments' ),
			'fields'         => array(
				array(
					'key'     => 'field_{nome}_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => 'A <strong>foto</strong> é a <em>Imagem destacada</em>. O título é o nome de quem depõe.',
				),
				array(
					'key'   => 'field_{nome}_cargo',
					'label' => 'Cargo',
					'name'  => 'cargo',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_{nome}_texto',
					'label'        => 'Depoimento',
					'name'         => 'texto',
					'type'         => 'textarea',
					'rows'         => 4,
					'new_lines'    => '',
				),
			),
		)
	);
```

**Regras:**

- Nome do campo **sem prefixo** aqui (`cargo`, não `depoimento_cargo`): o campo é lido
  com o ID do post (`get_field( 'cargo', $post->ID )`), então não há colisão — é assim
  em todos os CPTs existentes. (Grupos de **página** levam prefixo, porque `update_field()`
  resolve por nome global.)
- `hide_on_screen` sempre igual aos irmãos — deixa a tela de edição limpa.
- Campo `message` no topo explicando o que vem do título e da imagem destacada. Quem
  edita não adivinha.
- Só tipos do ACF Free: `text`, `textarea`, `wysiwyg`, `link`, `image`, `select`,
  `true_false`, `number`, `post_object`, `relationship`, `tab`, `message`.
- Imagem: `'return_format' => 'id'`.
- Relação com outro CPT:
  ```php
  array(
      'key'           => 'field_{nome}_integracoes',
      'label'         => 'Integrações',
      'name'          => 'integracoes',
      'type'          => 'relationship',
      'post_type'     => array( 'cli_integracao' ),
      'return_format' => 'id',
      'max'           => 4,
  ),
  ```

---

## 4. Card — `template-parts/card-{nome}.php`

Recebe dados por `$args` (não lê o loop global):

```php
<?php
/**
 * Template Part: card de {Singular}.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = absint( $args['id'] ?? 0 );

if ( ! $post_id ) {
	return;
}

$cargo = get_field( 'cargo', $post_id ) ?? '';
$texto = get_field( 'texto', $post_id ) ?? '';
$foto  = cliconnect_thumb( $post_id, 'medium', array( 'alt' => '' ) );
?>

<article class="card-{nome}">
	<?php if ( $foto ) : ?>
		<div class="card-{nome}__foto"><?php echo $foto; // montado com escape em cliconnect_thumb(). ?></div>
	<?php endif; ?>

	<?php if ( $texto ) : ?>
		<blockquote class="card-{nome}__texto"><?php echo esc_html( $texto ); ?></blockquote>
	<?php endif; ?>

	<p class="card-{nome}__autor">
		<?php echo esc_html( get_the_title( $post_id ) ); ?>
		<?php if ( $cargo ) : ?>
			<span class="card-{nome}__cargo"><?php echo esc_html( $cargo ); ?></span>
		<?php endif; ?>
	</p>
</article>
```

Consumo, em qualquer seção:

```php
$itens = cliconnect_posts( 'cli_{nome}', 6 );

if ( ! $itens ) {
	return;
}

foreach ( $itens as $item ) {
	get_template_part( 'template-parts/card', '{nome}', array( 'id' => $item->ID ) );
}
```

---

## 5. Archive e single (só se `public => true`)

`archive-cli_{nome}.php` segue o padrão de `archive-cli_case.php`: **não usa o loop
principal**, busca por `cliconnect_posts()` para manter a ordenação por `menu_order`
igual à da home.

```php
<?php
/**
 * Archive: {Plural}.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$itens = cliconnect_posts( 'cli_{nome}' );
?>

<main id="primary" class="site-{nome}s">
	<section class="secao">
		<div class="container">
			<h1><?php esc_html_e( '{Plural}', 'cli' ); ?></h1>

			<?php if ( $itens ) : ?>
				<div class="{nome}s__grid">
					<?php foreach ( $itens as $item ) : ?>
						<?php get_template_part( 'template-parts/card', '{nome}', array( 'id' => $item->ID ) ); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
```

E o enqueue do CSS, em `inc/enqueue.php`:

```php
	if ( is_post_type_archive( 'cli_{nome}' ) || is_singular( 'cli_{nome}' ) ) {
		wp_enqueue_style(
			'cliconnect-{nome}',
			get_theme_file_uri( '/assets/css/{nome}.css' ),
			array( 'cliconnect-theme' ),
			cliconnect_asset_version( '/assets/css/{nome}.css' )
		);
	}
```

---

## 6. Seed — `inc/cli/seed.php`

Método no bloco de CPTs, e chamada em `__invoke()`:

```php
	/**
	 * {Plural}.
	 *
	 * @return void
	 */
	protected function criar_{nome}s() {
		$itens = array(
			array( 'Nome da pessoa', 'Cargo', 'Texto do depoimento.', 'depoimento-pessoa' ),
		);

		foreach ( $itens as $ordem => $item ) {
			list( $titulo, $cargo, $texto, $imagem ) = $item;

			$id = $this->upsert(
				'{nome}:' . sanitize_title( $titulo ),
				array(
					'post_type'  => 'cli_{nome}',
					'post_title' => $titulo,
					'menu_order' => $ordem,
				)
			);

			if ( ! $id ) {
				continue;
			}

			$this->definir_thumb( $id, $imagem );

			update_field( 'cargo', $cargo, $id );
			update_field( 'texto', $texto, $id );
		}

		WP_CLI::log( sprintf( '  {nome}s: %d.', count( $itens ) ) );
	}
```

```php
		WP_CLI::log( '— Criando CPTs…' );
		// ...
		$this->criar_{nome}s();
```

- `menu_order` vem do índice do array: a ordem do array **é** a ordem no site.
- Imagens de conteúdo vão em `assets/seed/` (importadas por `importar_midia()`), com
  nome em kebab-case; `definir_thumb()` recebe o nome **sem extensão**.
- Idempotente pelo slug do `upsert()` — rodar de novo atualiza, não duplica.

---

## Armadilhas conhecidas

| Sintoma | Causa |
|---|---|
| "Nenhum Integração encontrado" no painel | faltou o `'f'` em `cliconnect_cpt_labels()` |
| CPT não aparece no menu do admin | `public => false` sem `show_ui`/`show_in_menu` |
| 404 no archive/single | falta `./bin/wp rewrite flush` depois de registrar |
| Campo **Ordem** não aparece | falta `page-attributes` em `supports` |
| Ordem errada no admin | CPT fora do `$ordenaveis` de `cliconnect_admin_order_cpts()` |
| Grupo ACF aparece em post/página | `location` com `post_type` errado |
| Imagem não aparece | falta `thumbnail` em `supports`, ou `definir_thumb()` com extensão no nome |
| Seção some do front | `cliconnect_posts()` sem itens e `return` cedo — comportamento correto, popule o seed |
