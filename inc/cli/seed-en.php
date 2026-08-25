<?php
/**
 * Seed — camada de tradução (pt → en).
 *
 * Traduzir no Polylang é criar **outro post** e vincular o par. A tradução tem
 * título, slug, template e valores de campo ACF próprios: preencher o
 * português não preenche o inglês.
 *
 * Este trait concentra a mecânica; o texto em inglês vive nos traits de
 * conteúdo (`seed-en-paginas.php`, `seed-en-solucoes.php`), um método por
 * página/solução, no mesmo desenho do seed em português.
 *
 * Estratégia de campos: em vez de reescrever os ~60 campos de cada landing,
 * a tradução **copia todos os campos do original** (imagens, ícones, números,
 * relações) e sobrescreve só o que é texto. Assim uma seção nova no seed em
 * português já nasce presente no inglês — em português, visível, e não
 * ausente e silenciosa.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Mecânica de tradução usada por Cliconnect_Seed.
 */
trait Cliconnect_Seed_En {

	/**
	 * Idioma das traduções criadas aqui.
	 */
	const LANG = 'en';

	/**
	 * Idioma de origem (o do conteúdo escrito no seed em português).
	 */
	const LANG_BASE = 'pt';

	/**
	 * Sufixo do slug de seed das traduções.
	 */
	const SUFIXO = ':en';

	/**
	 * Tipos de campo ACF cujo valor é um (ou vários) ID de post.
	 *
	 * Precisam ser remapeados para a tradução do alvo; mídia fica de fora
	 * de propósito — a mesma imagem serve aos dois idiomas.
	 *
	 * @var string[]
	 */
	protected $campos_de_post = array( 'post_object', 'relationship', 'page_link' );

	/**
	 * Caminhos internos: versão em português => versão em inglês.
	 *
	 * @var array<string,string>
	 */
	protected $caminhos_en = array();

	/* =====================================================================
	   ORQUESTRAÇÃO
	   ===================================================================== */

	/**
	 * Cria toda a camada em inglês do site.
	 *
	 * @param array $paginas        slug => ID das páginas em português.
	 * @param array $termos_solucao chave => term_id em português.
	 * @return void
	 */
	protected function traduzir_site( $paginas, $termos_solucao ) {
		if ( ! $this->polylang_ativo() ) {
			WP_CLI::warning( '  Polylang inativo — camada em inglês ignorada.' );

			return;
		}

		if ( ! in_array( self::LANG, pll_languages_list(), true ) ) {
			WP_CLI::warning( sprintf( '  Idioma "%s" não existe no Polylang — camada em inglês ignorada.', self::LANG ) );

			return;
		}

		WP_CLI::log( '— Habilitando tipos no Polylang…' );
		$this->habilitar_polylang(
			array( 'cli_solucao' ),
			array( 'cli_categoria_solucao' )
		);

		WP_CLI::log( '— Marcando o conteúdo existente como português…' );
		$this->marcar_idioma_base();

		WP_CLI::log( '— Traduzindo as categorias de solução…' );
		$termos_en = $this->traduzir_termos_solucao( $termos_solucao );

		WP_CLI::log( '— Traduzindo as páginas…' );
		$this->traduzir_paginas();

		WP_CLI::log( '— Traduzindo os CPTs de catálogo…' );
		$this->traduzir_cpts();

		WP_CLI::log( '— Traduzindo as FAQ das soluções…' );
		$this->traduzir_faq_solucoes();

		WP_CLI::log( '— Traduzindo as landings de solução…' );
		$this->traduzir_solucoes( $termos_en );

		WP_CLI::log( '— Montando os menus em inglês…' );
		$this->criar_menus_en( $termos_en );

		WP_CLI::log( '— Limpando traduções órfãs…' );
		$this->remover_traducoes_orfas();

		WP_CLI::log( '— Traduzindo as strings do Customizer…' );
		$this->traduzir_strings_polylang();

		$this->limpar_cache_polylang();
	}

	/**
	 * Grava as traduções das strings de opção (Customizer, nome e descrição
	 * do site) direto na tabela de strings do Polylang.
	 *
	 * São `theme_mod`/`option`: não são gettext nem post, e por isso não
	 * apareceriam traduzidas nem com o `.mo` do tema compilado. O registro de
	 * cada string está em `cliconnect_strings_customizer()` (inc/polylang.php);
	 * aqui entram os textos em inglês, para não depender do painel.
	 *
	 * @return void
	 */
	protected function traduzir_strings_polylang() {
		if ( ! class_exists( 'PLL_MO' ) || ! function_exists( 'PLL' ) ) {
			WP_CLI::warning( '  API de strings do Polylang indisponível — strings do Customizer não traduzidas.' );

			return;
		}

		$idioma = PLL()->model->get_language( self::LANG );

		if ( ! $idioma ) {
			return;
		}

		$traducoes = array(
			'Portal do Cliente'                                      => 'Client Portal',
			'Acessar Plataforma'                                     => 'Access Platform',
			"Planeje a evolução\ndas suas integrações"               => "Plan the evolution\nof your integrations",
			'Fale conosco no Whatsapp'                                => 'Talk to us on WhatsApp',
			'Integrações ilimitadas. Custo previsível. Sem surpresas.' => 'Unlimited integrations. Predictable cost. No surprises.',
		);

		$mo = new PLL_MO();
		$mo->import_from_db( $idioma );

		foreach ( $traducoes as $original => $traduzido ) {
			$mo->add_entry( $mo->make_entry( $original, $traduzido ) );
		}

		$mo->export_to_db( $idioma );

		WP_CLI::log( sprintf( '  strings do Customizer: %d traduzidas.', count( $traducoes ) ) );
	}

	/**
	 * Invalida o cache de idiomas do Polylang.
	 *
	 * O plugin guarda a lista de idiomas — inclusive o `page_on_front` de cada
	 * um — em transient. Vinculado o par pela linha de comando, o cache segue
	 * antigo e `/en/` redireciona para o slug da home em vez de servir a home.
	 *
	 * @return void
	 */
	protected function limpar_cache_polylang() {
		delete_transient( 'pll_languages_list' );

		if ( function_exists( 'pll_clean_languages_cache' ) ) {
			pll_clean_languages_cache();
		}

		WP_CLI::log( '  cache de idiomas do Polylang limpo.' );
	}

	/* =====================================================================
	   INFRAESTRUTURA
	   ===================================================================== */

	/**
	 * O Polylang está ativo e configurado?
	 *
	 * @return bool
	 */
	protected function polylang_ativo() {
		return function_exists( 'pll_languages_list' )
			&& function_exists( 'pll_set_post_language' )
			&& function_exists( 'pll_save_post_translations' )
			&& (bool) pll_languages_list();
	}

	/**
	 * Habilita CPTs e taxonomias para tradução.
	 *
	 * Efeito colateral que precisa ser conhecido: a partir daqui o Polylang
	 * filtra as queries por idioma — um tipo habilitado sem tradução some do
	 * site no outro idioma.
	 *
	 * @param string[] $post_types Slugs de CPT.
	 * @param string[] $taxonomias Slugs de taxonomia.
	 * @return void
	 */
	protected function habilitar_polylang( $post_types, $taxonomias = array() ) {
		$opcoes = get_option( 'polylang' );

		if ( ! is_array( $opcoes ) ) {
			return;
		}

		$opcoes['post_types'] = array_values( array_unique( array_merge( $opcoes['post_types'] ?? array(), $post_types ) ) );
		$opcoes['taxonomies'] = array_values( array_unique( array_merge( $opcoes['taxonomies'] ?? array(), $taxonomias ) ) );

		/*
		 * Sem `redirect_lang`, /en/ redireciona para o slug da home traduzida
		 * (/en/home-en/) — que é uma página comum e não usa front-page.php.
		 * Com ele, cada idioma tem a sua home na raiz do prefixo.
		 */
		$opcoes['redirect_lang'] = true;

		update_option( 'polylang', $opcoes );

		WP_CLI::log( sprintf( '  polylang: %d post types, %d taxonomias.', count( $opcoes['post_types'] ), count( $opcoes['taxonomies'] ) ) );
	}

	/**
	 * Atribui o idioma padrão a todo post/termo que ainda não tem um.
	 *
	 * Sem idioma, o Polylang esconde o objeto de todas as queries do front —
	 * é o que acontece com um CPT recém-habilitado.
	 *
	 * @return void
	 */
	protected function marcar_idioma_base() {
		$opcoes = get_option( 'polylang' );
		$tipos  = array_merge( array( 'post', 'page' ), (array) ( $opcoes['post_types'] ?? array() ) );
		$total  = 0;

		foreach ( array_unique( $tipos ) as $tipo ) {
			if ( ! post_type_exists( $tipo ) ) {
				continue;
			}

			$ids = get_posts(
				array(
					'post_type'      => $tipo,
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'lang'           => '',
				)
			);

			foreach ( $ids as $id ) {
				if ( ! pll_get_post_language( $id ) ) {
					pll_set_post_language( $id, self::LANG_BASE );
					++$total;
				}
			}
		}

		foreach ( (array) ( $opcoes['taxonomies'] ?? array() ) as $taxonomia ) {
			if ( ! taxonomy_exists( $taxonomia ) ) {
				continue;
			}

			$termos = get_terms(
				array(
					'taxonomy'   => $taxonomia,
					'hide_empty' => false,
					'fields'     => 'ids',
					'lang'       => '',
				)
			);

			foreach ( (array) $termos as $termo_id ) {
				if ( ! is_wp_error( $termo_id ) && ! pll_get_term_language( $termo_id ) ) {
					pll_set_term_language( $termo_id, self::LANG_BASE );
					++$total;
				}
			}
		}

		WP_CLI::log( sprintf( '  idioma padrão atribuído a %d objetos.', $total ) );
	}

	/* =====================================================================
	   TRADUÇÃO DE UM POST
	   ===================================================================== */

	/**
	 * Cria (ou atualiza) a versão em inglês de um post do seed.
	 *
	 * Copia todos os campos ACF do original e sobrescreve com `$textos` —
	 * imagens, ícones e números vêm de graça; só o texto é reescrito.
	 *
	 * @param string $slug_seed Slug de seed do post em português.
	 * @param string $post_type Post type.
	 * @param array  $dados     Dados da tradução (post_title, post_name, …).
	 * @param array  $textos    Campos ACF traduzidos (nome do campo => valor).
	 * @return int ID da tradução, 0 em caso de falha.
	 */
	protected function traduzir_post( $slug_seed, $post_type, $dados, $textos = array() ) {
		$origem_id = $this->id_do_seed( $slug_seed, $post_type );

		if ( ! $origem_id ) {
			WP_CLI::warning( sprintf( '  "%s" não existe em português — tradução ignorada.', $slug_seed ) );

			return 0;
		}

		if ( self::LANG_BASE !== pll_get_post_language( $origem_id ) ) {
			pll_set_post_language( $origem_id, self::LANG_BASE );
		}

		$slug_en = $slug_seed . self::SUFIXO;

		// Adota uma tradução já vinculada (feita à mão) em vez de duplicá-la.
		$existente = (int) pll_get_post( $origem_id, self::LANG );

		if ( $existente && ! $this->id_do_seed( $slug_en, $post_type ) ) {
			update_post_meta( $existente, self::META, $slug_en );
		}

		$dados = wp_parse_args(
			$dados,
			array(
				'post_type'    => $post_type,
				'post_status'  => get_post_status( $origem_id ),
				'post_content' => '',
				'menu_order'   => (int) get_post_field( 'menu_order', $origem_id ),
			)
		);

		$traducao_id = $this->upsert( $slug_en, $dados );

		if ( ! $traducao_id ) {
			return 0;
		}

		pll_set_post_language( $traducao_id, self::LANG );
		pll_save_post_translations(
			array(
				self::LANG_BASE => $origem_id,
				self::LANG      => $traducao_id,
			)
		);

		// Page template: é o que faz a tradução achar o layout e os campos ACF.
		$template = get_post_meta( $origem_id, '_wp_page_template', true );

		if ( $template ) {
			update_post_meta( $traducao_id, '_wp_page_template', $template );
		} else {
			// Sem isso, um 'default' herdado de edição manual esconde o layout.
			delete_post_meta( $traducao_id, '_wp_page_template' );
		}

		// Imagem destacada não é herdada.
		$thumb = get_post_thumbnail_id( $origem_id );

		if ( $thumb ) {
			set_post_thumbnail( $traducao_id, $thumb );
		}

		$this->copiar_taxonomias( $origem_id, $traducao_id );
		$this->copiar_campos_acf( $origem_id, $traducao_id, $textos, $slug_seed );

		return $traducao_id;
	}

	/**
	 * Copia os campos ACF do original para a tradução, aplicando os textos.
	 *
	 * @param int    $origem_id  Post em português.
	 * @param int    $destino_id Post em inglês.
	 * @param array  $textos     nome do campo => valor traduzido.
	 * @param string $rotulo     Identificador usado nos avisos.
	 * @return void
	 */
	protected function copiar_campos_acf( $origem_id, $destino_id, $textos, $rotulo = '' ) {
		if ( ! function_exists( 'get_field_objects' ) ) {
			return;
		}

		$campos = get_field_objects( $origem_id, false );

		if ( ! $campos ) {
			return;
		}

		foreach ( $campos as $nome => $campo ) {
			$original = $campo['value'] ?? '';
			$vazio    = ( '' === $original || null === $original || array() === $original );

			/*
			 * Campo vazio no português continua vazio no inglês: a tradução não
			 * inventa seção que o original não tem. Isso deixa os mapas de texto
			 * livres para trazer os rótulos comuns a todas as landings sem ligar
			 * um CTA onde ele não existe.
			 */
			if ( ! $vazio && array_key_exists( $nome, $textos ) ) {
				$valor = $textos[ $nome ];
			} else {
				$valor = $this->traduzir_valor( $original, $campo['type'] ?? '' );
			}

			update_field( $campo['key'], $valor, $destino_id );
		}

		/*
		 * Chave de tradução que não existe no grupo ACF é erro de digitação:
		 * sem o aviso, o texto simplesmente não aparece e ninguém percebe.
		 *
		 * A comparação é contra o grupo, não contra `$campos`: get_field_objects()
		 * lê da postmeta e só devolve os campos que têm valor gravado, então uma
		 * chave ausente ali só significa que o português deixou o campo vazio.
		 */
		$conhecidos = $this->nomes_de_campos( $origem_id );
		$sobrando   = $conhecidos ? array_diff( array_keys( $textos ), $conhecidos ) : array();

		if ( $sobrando ) {
			WP_CLI::warning(
				sprintf(
					'  %s: campo(s) inexistente(s) no grupo ACF — %s',
					$rotulo ? $rotulo : '#' . $destino_id,
					implode( ', ', $sobrando )
				)
			);
		}
	}

	/**
	 * Nomes de todos os campos dos grupos ACF associados a um post.
	 *
	 * @param int $post_id Post de referência.
	 * @return string[]
	 */
	protected function nomes_de_campos( $post_id ) {
		if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
			return array();
		}

		$nomes = array();

		foreach ( (array) acf_get_field_groups( array( 'post_id' => $post_id ) ) as $grupo ) {
			foreach ( (array) acf_get_fields( $grupo ) as $campo ) {
				if ( ! empty( $campo['name'] ) ) {
					$nomes[] = $campo['name'];
				}
			}
		}

		return $nomes;
	}

	/**
	 * Aponta um valor copiado para a versão em inglês do que ele referencia.
	 *
	 * @param mixed  $valor Valor cru do campo.
	 * @param string $tipo  Tipo do campo ACF.
	 * @return mixed
	 */
	protected function traduzir_valor( $valor, $tipo ) {
		if ( in_array( $tipo, $this->campos_de_post, true ) ) {
			return $this->traduzir_referencia( $valor );
		}

		if ( 'link' === $tipo && is_array( $valor ) ) {
			$valor['url'] = $this->traduzir_caminho( $valor['url'] ?? '' );

			return $valor;
		}

		if ( 'url' === $tipo && is_string( $valor ) ) {
			return $this->traduzir_caminho( $valor );
		}

		return $valor;
	}

	/**
	 * Troca IDs de post pela tradução, quando existir.
	 *
	 * @param mixed $valor ID, array de IDs ou vazio.
	 * @return mixed
	 */
	protected function traduzir_referencia( $valor ) {
		if ( is_array( $valor ) ) {
			return array_map( array( $this, 'traduzir_referencia' ), $valor );
		}

		if ( ! is_numeric( $valor ) ) {
			return $valor;
		}

		$traducao = (int) pll_get_post( (int) $valor, self::LANG );

		return $traducao ? $traducao : $valor;
	}

	/**
	 * Converte um caminho interno para a versão em inglês.
	 *
	 * @param string $url Caminho ou URL.
	 * @return string
	 */
	protected function traduzir_caminho( $url ) {
		$url = (string) $url;

		if ( '' === $url ) {
			return $url;
		}

		$caminho = str_replace( untrailingslashit( home_url() ), '', $url );

		if ( isset( $this->caminhos_en[ $caminho ] ) ) {
			return str_replace( $caminho, $this->caminhos_en[ $caminho ], $url );
		}

		return $url;
	}

	/**
	 * Replica os termos do original na tradução, usando os termos em inglês
	 * quando a taxonomia é traduzível.
	 *
	 * @param int $origem_id  Post em português.
	 * @param int $destino_id Post em inglês.
	 * @return void
	 */
	protected function copiar_taxonomias( $origem_id, $destino_id ) {
		$taxonomias = get_object_taxonomies( get_post_type( $origem_id ) );
		$opcoes     = get_option( 'polylang' );
		$traduziveis = (array) ( $opcoes['taxonomies'] ?? array() );

		foreach ( $taxonomias as $taxonomia ) {
			if ( in_array( $taxonomia, array( 'language', 'post_translations', 'term_language', 'term_translations' ), true ) ) {
				continue;
			}

			$termos = wp_get_object_terms( $origem_id, $taxonomia, array( 'fields' => 'ids' ) );

			if ( is_wp_error( $termos ) || ! $termos ) {
				continue;
			}

			if ( in_array( $taxonomia, $traduziveis, true ) && function_exists( 'pll_get_term' ) ) {
				$termos = array_map(
					function ( $termo_id ) {
						$traducao = (int) pll_get_term( (int) $termo_id, self::LANG );

						return $traducao ? $traducao : (int) $termo_id;
					},
					$termos
				);
			}

			wp_set_object_terms( $destino_id, array_map( 'intval', $termos ), $taxonomia );
		}
	}

	/* =====================================================================
	   TERMOS
	   ===================================================================== */

	/**
	 * Cria (ou atualiza) a versão em inglês de um termo e vincula ao original.
	 *
	 * @param int    $origem_id  ID do termo em português.
	 * @param string $taxonomia  Taxonomia.
	 * @param string $nome       Nome em inglês.
	 * @param string $slug       Slug em inglês.
	 * @return int ID do termo traduzido, 0 em caso de falha.
	 */
	protected function traduzir_termo( $origem_id, $taxonomia, $nome, $slug ) {
		if ( ! $origem_id || ! function_exists( 'pll_set_term_language' ) ) {
			return 0;
		}

		if ( self::LANG_BASE !== pll_get_term_language( $origem_id ) ) {
			pll_set_term_language( $origem_id, self::LANG_BASE );
		}

		$traducao_id = (int) pll_get_term( $origem_id, self::LANG );

		if ( ! $traducao_id ) {
			$existente = get_term_by( 'slug', $slug, $taxonomia );

			if ( $existente && self::LANG === pll_get_term_language( $existente->term_id ) ) {
				$traducao_id = (int) $existente->term_id;
			}
		}

		if ( $traducao_id ) {
			wp_update_term( $traducao_id, $taxonomia, array( 'name' => $nome ) );
		} else {
			$novo = wp_insert_term( $nome, $taxonomia, array( 'slug' => $slug ) );

			if ( is_wp_error( $novo ) ) {
				WP_CLI::warning( sprintf( '  Termo "%s": %s', $nome, $novo->get_error_message() ) );

				return 0;
			}

			$traducao_id = (int) $novo['term_id'];
		}

		update_term_meta( $traducao_id, self::META, get_term_meta( $origem_id, self::META, true ) . self::SUFIXO );

		pll_set_term_language( $traducao_id, self::LANG );
		pll_save_term_translations(
			array(
				self::LANG_BASE => $origem_id,
				self::LANG      => $traducao_id,
			)
		);

		return $traducao_id;
	}

	/* =====================================================================
	   LIMPEZA
	   ===================================================================== */

	/**
	 * Manda para a lixeira traduções soltas de execuções antigas.
	 *
	 * Um post em inglês sem vínculo com o português e sem slug de seed é
	 * resíduo: não aparece no seletor de idiomas, não é editável em par e
	 * duplica a listagem. Vai para a lixeira (reversível), nunca é apagado.
	 *
	 * @return void
	 */
	protected function remover_traducoes_orfas() {
		$opcoes = get_option( 'polylang' );
		$tipos  = array_merge( array( 'post', 'page' ), (array) ( $opcoes['post_types'] ?? array() ) );
		$total  = 0;

		foreach ( array_unique( $tipos ) as $tipo ) {
			if ( ! post_type_exists( $tipo ) ) {
				continue;
			}

			$ids = get_posts(
				array(
					'post_type'      => $tipo,
					'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'lang'           => self::LANG,
				)
			);

			foreach ( $ids as $id ) {
				$traducoes = pll_get_post_translations( $id );

				if ( ! empty( $traducoes[ self::LANG_BASE ] ) ) {
					continue;
				}

				if ( get_post_meta( $id, self::META, true ) ) {
					continue;
				}

				WP_CLI::log( sprintf( '  lixeira: #%d %s (%s)', $id, get_the_title( $id ), $tipo ) );
				wp_trash_post( $id );
				++$total;
			}
		}

		WP_CLI::log( sprintf( '  órfãos: %d.', $total ) );
	}

	/* =====================================================================
	   MENUS
	   ===================================================================== */

	/**
	 * Cria um menu em inglês e o registra só na location do idioma inglês.
	 *
	 * As locations do Polylang são por idioma; `montar_menu()` aponta o menu
	 * português para todos eles. Aqui sobrescrevemos apenas o inglês.
	 *
	 * @param string $location Slug da location registrada.
	 * @param string $nome     Nome legível do menu.
	 * @param array  $itens    Itens no formato de normalizar_itens().
	 * @return void
	 */
	protected function montar_menu_en( $location, $nome, $itens ) {
		$slug = 'cli-' . $location . '-en';
		$menu = wp_get_nav_menu_object( $slug );

		if ( $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}

		$menu_id = wp_create_nav_menu( $slug );

		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::warning( sprintf( 'Falha ao criar o menu %s (en).', $location ) );

			return;
		}

		wp_update_term( $menu_id, 'nav_menu', array( 'name' => $nome ) );

		$this->inserir_itens( $menu_id, $itens, 0 );

		$opcoes = get_option( 'polylang' );

		if ( is_array( $opcoes ) ) {
			$opcoes['nav_menus'][ get_stylesheet() ][ $location ][ self::LANG ] = $menu_id;
			update_option( 'polylang', $opcoes );
		}

		WP_CLI::log( sprintf( '  menu "%s" → location %s (en).', $nome, $location ) );
	}

	/**
	 * URL absoluta de uma página do seed na versão em inglês.
	 *
	 * @param string $slug Slug de seed da página em português (sem 'pagina:').
	 * @return string
	 */
	protected function url_pagina_en( $slug ) {
		$origem = $this->id_do_seed( 'pagina:' . $slug, 'page' );
		$alvo   = $origem ? (int) pll_get_post( $origem, self::LANG ) : 0;
		$link   = $alvo ? get_permalink( $alvo ) : '';

		return $link ? $link : home_url( '/' . self::LANG . '/' );
	}
}
