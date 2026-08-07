# As quatro camadas da tradução

Cada camada é independente e falha de um jeito próprio. Execute na ordem.

---

## Camada 0 — Habilitar tipos (pré-requisito de tudo)

O Polylang **só oferece tradução para post types e taxonomias explicitamente
habilitados**. Sem isso, a coluna de bandeiras nem aparece na listagem do CPT.

```bash
./bin/wp eval '
$o = get_option( "polylang" );
var_export( array( "post_types" => $o["post_types"] ?? [], "taxonomies" => $o["taxonomies"] ?? [] ) );
'
```

Para habilitar (pelo painel: Idiomas → Configurações → Tipos de conteúdo personalizados;
por código, no seed):

```php
	/**
	 * Habilita CPTs e taxonomias para tradução no Polylang.
	 *
	 * @param string[] $post_types  Slugs de CPT.
	 * @param string[] $taxonomias  Slugs de taxonomia.
	 * @return void
	 */
	protected function habilitar_polylang( $post_types, $taxonomias = array() ) {
		$opcoes = get_option( 'polylang' );

		if ( ! is_array( $opcoes ) ) {
			return;
		}

		$opcoes['post_types'] = array_values( array_unique( array_merge( $opcoes['post_types'] ?? array(), $post_types ) ) );
		$opcoes['taxonomies'] = array_values( array_unique( array_merge( $opcoes['taxonomies'] ?? array(), $taxonomias ) ) );

		update_option( 'polylang', $opcoes );
	}
```

**O efeito colateral que precisa ser dito antes:** habilitado o CPT, o Polylang filtra
as queries por idioma. `cliconnect_posts( 'cli_cliente' )` passa a devolver só os
clientes do idioma atual — e a esteira de logos **some** no site em inglês enquanto não
houver tradução.

Por isso a recomendação: catálogos de logo (`cli_cliente`, `cli_integracao`, `cli_selo`)
normalmente **não** entram. Logo não tem idioma. Já `cli_faq`, `cli_case` e `cli_agente`
têm texto e entram.

> Em WP-CLI não há idioma de requisição. Se um seed passar a devolver menos itens depois
> de habilitar um CPT, é isso. Force com `'lang' => 'pt'` no `WP_Query` quando precisar
> determinismo.

---

## Camada 1 — Estrutura

### 1a. Menus — o gotcha documentado no `CLAUDE.md`

As locations do Polylang são **por idioma**. Atribuir a location pelo tema não basta: o
Polylang sobrescreve `theme_mod_nav_menu_locations` no front e o `wp_nav_menu()` sai
**vazio**.

Já resolvido em `Cliconnect_Seed::sincronizar_polylang()` (`inc/cli/seed.php:1181`), que
grava:

```php
$opcoes['nav_menus'][ get_stylesheet() ][ $location ][ $lang ] = $menu_id;
```

Hoje ele aponta **o mesmo menu para todos os idiomas** — correto enquanto não houver
menu em inglês. Ao criar o menu EN, aponte cada idioma para o seu:

```php
$opcoes['nav_menus'][ $tema ]['principal']['pt'] = $menu_pt;
$opcoes['nav_menus'][ $tema ]['principal']['en'] = $menu_en;
```

Verificar:
```bash
./bin/wp eval '$o = get_option("polylang"); var_export( $o["nav_menus"][ get_stylesheet() ] ?? "vazio" );'
```

### 1b. Template das páginas traduzidas

`page-{slug}.php` casa pelo `post_name`. A tradução tem outro slug (`/en/platform/`) e
cai no `page.php` genérico.

Solução única para o tema (filtro `template_include` em `inc/helpers.php`) — o código
está em
[`.claude/skills/criar-pagina/references/estrutura-pagina.md`](../../criar-pagina/references/estrutura-pagina.md),
seção "Polylang: template da tradução". Verifique se já existe antes de adicionar.

### 1c. Front page por idioma

Cada idioma tem a **sua** home. Crie a página EN, vincule à home pt e confirme:

```bash
./bin/wp eval 'var_export( array( "page_on_front" => get_option("page_on_front"), "traducoes" => function_exists("pll_get_post_translations") ? pll_get_post_translations( (int) get_option("page_on_front") ) : [] ) );'
```

Se a home EN não estiver vinculada, `/en/` cai na listagem de posts.

---

## Camada 2 — Conteúdo

**Tradução é outro post.** Ele tem título, slug, imagem destacada e **valores de campo
ACF próprios** — preencher o pt não preenche o en.

Padrão para o seed:

```php
	/**
	 * Cria (ou atualiza) a versão em inglês de um post e vincula ao original.
	 *
	 * @param int    $origem_id ID do post em português.
	 * @param array  $dados     Dados da versão traduzida (post_title, post_name, ...).
	 * @param array  $campos    Campos ACF da tradução (nome => valor).
	 * @return int ID da tradução.
	 */
	protected function traduzir_post( $origem_id, $dados, $campos = array() ) {
		if ( ! $origem_id || ! function_exists( 'pll_set_post_language' ) ) {
			return 0;
		}

		$slug_seed = get_post_meta( $origem_id, self::META, true ) . ':en';

		$dados = wp_parse_args(
			$dados,
			array(
				'post_type'   => get_post_type( $origem_id ),
				'post_status' => 'publish',
			)
		);

		$traducao_id = $this->upsert( $slug_seed, $dados );

		if ( ! $traducao_id ) {
			return 0;
		}

		pll_set_post_language( $origem_id, 'pt' );
		pll_set_post_language( $traducao_id, 'en' );
		pll_save_post_translations(
			array(
				'pt' => $origem_id,
				'en' => $traducao_id,
			)
		);

		// A imagem destacada não é herdada: copie explicitamente.
		$thumb = get_post_thumbnail_id( $origem_id );

		if ( $thumb ) {
			set_post_thumbnail( $traducao_id, $thumb );
		}

		foreach ( $campos as $nome => $valor ) {
			update_field( $nome, $valor, $traducao_id );
		}

		return $traducao_id;
	}
```

Cuidados:

- **`pll_set_post_language()` antes de `pll_save_post_translations()`** — na ordem
  inversa o vínculo não grava.
- **Slug do seed com sufixo `:en`**, senão o `upsert()` sobrescreve o post em português.
- **Campos ACF de link** apontando para URL interna precisam apontar para a **versão EN**
  do destino (`pll_get_post( $id, 'en' )`), senão o botão joga o visitante de volta ao pt.
- **Anexos** podem ficar sem idioma; a mesma imagem serve aos dois — não duplique mídia.

---

## Camada 3 — Strings do tema (`.pot` / `.po` / `.mo`)

Textos em `__()`, `_e()`, `esc_html_e()` com text-domain `cli`. **Não** passam pelo
Polylang: são gettext, e hoje `languages/` está **vazio** (só `.gitkeep`) — ou seja,
nenhuma string de interface do tema está traduzida.

```bash
# 1. Extrair as strings do código para um template .pot
./bin/wp i18n make-pot . languages/cli.pot --domain=cli

# 2. Criar o .po de inglês (a partir do .pot) e traduzir as entradas
cp languages/cli.pot languages/cli-en_US.po
#    edite msgstr "" de cada msgid — ou use Poedit

# 3. Compilar o .mo (é o arquivo que o WordPress lê)
./bin/wp i18n make-mo languages/
```

- Nome do arquivo: `{text-domain}-{locale}.mo` → `cli-en_US.mo`. Errar o nome faz o
  WordPress ignorar em silêncio.
- `load_theme_textdomain( 'cli', get_theme_file_path( '/languages' ) )` já está em
  `functions.php`.
- `.po` **e** `.mo` vão para o git — o `.mo` é o que roda em produção.
- Rode `make-pot` de novo sempre que adicionar string nova, e mescle no `.po`.

Strings deste tema hoje: rótulos de interface (`'Integrações'`, `'Ler mais'`), títulos do
`archive-cli_case.php` e labels de menu registradas em `functions.php`.

---

## Camada 4 — Strings de opção (Customizer)

Texto digitado pelo cliente no Customizer (`cliconnect_portal_texto`,
`cliconnect_header_cta_texto`, telefone, endereço) não é gettext nem post: é
`theme_mod`. Para traduzir, registre e leia com as funções do Polylang.

Registro (em `inc/customizer.php` ou módulo próprio):

```php
/**
 * Registra as strings do Customizer para tradução no Polylang.
 *
 * @return void
 */
function cliconnect_registrar_strings_polylang() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	$strings = array(
		'cliconnect_portal_texto'     => 'Header — texto do Portal do Cliente',
		'cliconnect_header_cta_texto' => 'Header — texto do CTA',
	);

	foreach ( $strings as $mod => $rotulo ) {
		$valor = get_theme_mod( $mod, '' );

		if ( $valor ) {
			pll_register_string( $rotulo, $valor, 'CLI Connect' );
		}
	}
}
add_action( 'init', 'cliconnect_registrar_strings_polylang' );
```

Leitura, no template:

```php
$texto = get_theme_mod( 'cliconnect_header_cta_texto', '' );
$texto = function_exists( 'pll__' ) ? pll__( $texto ) : $texto;
echo esc_html( $texto );
```

A tradução é preenchida em **Idiomas → Traduções de strings** no painel. Blindar com
`function_exists()` mantém o tema funcionando sem o plugin — regra do projeto.

---

## Diagnóstico rápido por sintoma

| Sintoma | Camada | Causa |
|---|---|---|
| Menu vazio em `/en/` | 1a | `polylang['nav_menus'][tema][location]['en']` não definido |
| `/en/` cai no blog | 1c | home EN não criada ou não vinculada |
| Página EN sem o layout | 1b | falta o filtro `template_include` |
| Seção some em EN | 0 | CPT habilitado sem conteúdo traduzido |
| Campos ACF vazios em EN | 2 | tradução é outro post — preencher os campos dele |
| Botão joga para o site pt | 2 | campo Link apontando para o post pt |
| Interface segue em português | 3 | `.mo` ausente ou com nome errado |
| Texto do Customizer não traduz | 4 | falta `pll_register_string()` + `pll__()` |
| Aba de tradução não aparece no CPT | 0 | CPT fora de `polylang['post_types']` |
