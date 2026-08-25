<?php
/**
 * Seed — mecânica de tradução (português → qualquer idioma do Polylang).
 *
 * Traduzir no Polylang é criar **outro post** e vincular o par. A tradução tem
 * título, slug, template e valores de campo ACF próprios: preencher o
 * português não preenche os demais idiomas.
 *
 * Este trait não conhece nenhum idioma em particular. O texto vive nos traits
 * de conteúdo, um por idioma (`seed-en-*.php`, `seed-es-*.php`), e é alcançado
 * por convenção de nome: com `$this->lang = 'es'`, `dados( 'paginas' )` chama
 * `paginas_es()` e `texto( 'home' )` chama `texto_es_home()`. Idioma sem trait
 * de conteúdo simplesmente não gera nada — não quebra.
 *
 * Estratégia de campos: em vez de reescrever os ~60 campos de cada landing,
 * a tradução **copia todos os campos do original** (imagens, ícones, números,
 * relações) e sobrescreve só o que é texto. Assim uma seção nova no seed em
 * português já nasce presente nas traduções — em português, visível, e não
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
trait Cliconnect_Seed_I18n {

	/**
	 * Idioma de origem (o do conteúdo escrito no seed em português).
	 */
	const LANG_BASE = 'pt';

	/**
	 * Idioma que está sendo gerado na passada atual.
	 *
	 * @var string
	 */
	protected $lang = '';

	/**
	 * Tipos de campo ACF cujo valor é um (ou vários) ID de post.
	 *
	 * Precisam ser remapeados para a tradução do alvo; mídia fica de fora
	 * de propósito — a mesma imagem serve a todos os idiomas.
	 *
	 * @var string[]
	 */
	protected $campos_de_post = array( 'post_object', 'relationship', 'page_link' );

	/**
	 * Tipos de campo ACF que carregam texto redigido.
	 *
	 * Usados pelo modo stub: um idioma sem texto para uma landing recebe a
	 * estrutura (imagens, ícones, relações) e estes campos vazios, em vez de
	 * herdar o português.
	 *
	 * @var string[]
	 */
	protected $campos_de_texto = array( 'text', 'textarea', 'wysiwyg', 'link', 'url' );

	/**
	 * Caminhos internos: versão em português => versão no idioma atual.
	 *
	 * @var array<string,string>
	 */
	protected $caminhos = array();

	/* =====================================================================
	   DESPACHO POR IDIOMA
	   ===================================================================== */

	/**
	 * Sufixo do slug de seed no idioma atual (`:en`, `:es`, …).
	 *
	 * @return string
	 */
	protected function sufixo() {
		return ':' . $this->lang;
	}

	/**
	 * Lê um conjunto de dados do trait de conteúdo do idioma atual.
	 *
	 * @param string $nome   Nome base do método (ex.: 'paginas' → `paginas_es()`).
	 * @param mixed  $padrao Valor devolvido quando o idioma não tem esse método.
	 * @return mixed
	 */
	protected function dados( $nome, $padrao = array() ) {
		$metodo = $nome . '_' . $this->lang;

		return method_exists( $this, $metodo ) ? $this->$metodo() : $padrao;
	}

	/**
	 * Lê o mapa de textos de uma página ou solução no idioma atual.
	 *
	 * @param string $chave Chave com underscores (ex.: 'cli_connect').
	 * @return array<string,mixed>
	 */
	protected function texto( $chave ) {
		$metodo = 'texto_' . $this->lang . '_' . $chave;

		return method_exists( $this, $metodo ) ? $this->$metodo() : array();
	}

	/**
	 * Converte um slug de seed em sufixo de método (`cli-connect` → `cli_connect`).
	 *
	 * @param string $slug Slug com hífens.
	 * @return string
	 */
	protected function chave_de_metodo( $slug ) {
		return str_replace( '-', '_', $slug );
	}

	/**
	 * Idiomas a gerar: todos os do Polylang menos o de origem.
	 *
	 * @return string[]
	 */
	protected function idiomas_alvo() {
		$idiomas = (array) pll_languages_list();

		return array_values( array_diff( $idiomas, array( self::LANG_BASE ) ) );
	}

	/* =====================================================================
	   IDIOMAS
	   ===================================================================== */

	/**
	 * Idiomas do site, na ordem em que aparecem no seletor.
	 *
	 * O primeiro é o de origem: é dele que todo o resto é traduzido, e o slug
	 * precisa bater com self::LANG_BASE.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	protected function idiomas_do_site() {
		return array(
			array(
				'name'       => 'Português',
				'slug'       => 'pt',
				'locale'     => 'pt_BR',
				'flag'       => 'br',
				'term_group' => 0,
			),
			array(
				'name'       => 'English',
				'slug'       => 'en',
				'locale'     => 'en_US',
				'flag'       => 'us',
				'term_group' => 1,
			),
			array(
				'name'       => 'Español',
				'slug'       => 'es',
				'locale'     => 'es_ES',
				'flag'       => 'es',
				'term_group' => 2,
			),
		);
	}

	/**
	 * Cria no Polylang os idiomas que ainda não existem.
	 *
	 * Sem isso o seed só reproduz o site em uma máquina onde alguém já cadastrou
	 * os idiomas pelo painel — e um `--reset` em ambiente novo sairia monolíngue.
	 *
	 * @return void
	 */
	protected function garantir_idiomas() {
		$existentes = (array) pll_languages_list();
		$criados    = 0;

		foreach ( $this->idiomas_do_site() as $idioma ) {
			if ( in_array( $idioma['slug'], $existentes, true ) ) {
				continue;
			}

			$modelo = PLL()->model;
			$alvo   = isset( $modelo->languages ) ? $modelo->languages : $modelo;

			if ( ! method_exists( $alvo, 'add' ) && ! method_exists( $alvo, 'add_language' ) ) {
				WP_CLI::warning( '  API de idiomas do Polylang indisponível — cadastre os idiomas pelo painel.' );

				return;
			}

			$resultado = method_exists( $alvo, 'add' ) ? $alvo->add( $idioma ) : $alvo->add_language( $idioma );

			if ( is_wp_error( $resultado ) ) {
				WP_CLI::warning( sprintf( '  Idioma "%s": %s', $idioma['slug'], $resultado->get_error_message() ) );

				continue;
			}

			++$criados;
		}

		if ( $criados ) {
			$this->limpar_cache_polylang();
			WP_CLI::log( sprintf( '  idiomas criados: %d.', $criados ) );
		}
	}

	/* =====================================================================
	   ORQUESTRAÇÃO
	   ===================================================================== */

	/**
	 * Cria todas as camadas traduzidas do site.
	 *
	 * @param array $paginas        slug => ID das páginas em português.
	 * @param array $termos_solucao chave => term_id em português.
	 * @return void
	 */
	protected function traduzir_site( $paginas, $termos_solucao ) {
		if ( ! $this->polylang_ativo() ) {
			WP_CLI::warning( '  Polylang inativo — camadas traduzidas ignoradas.' );

			return;
		}

		WP_CLI::log( '— Conferindo os idiomas…' );
		$this->garantir_idiomas();

		WP_CLI::log( '— Habilitando tipos no Polylang…' );
		$this->habilitar_polylang(
			array( 'cli_solucao' ),
			array( 'cli_categoria_solucao' )
		);

		WP_CLI::log( '— Marcando o conteúdo existente como português…' );
		$this->marcar_idioma_base();

		$idiomas = $this->idiomas_alvo();

		if ( ! $idiomas ) {
			WP_CLI::warning( '  Nenhum idioma além do português configurado no Polylang.' );

			return;
		}

		foreach ( $idiomas as $lang ) {
			$this->traduzir_idioma( $lang, $termos_solucao );
		}

		$this->limpar_cache_polylang();
	}

	/**
	 * Gera a camada de um idioma.
	 *
	 * @param string $lang           Slug do idioma no Polylang.
	 * @param array  $termos_solucao chave => term_id em português.
	 * @return void
	 */
	protected function traduzir_idioma( $lang, $termos_solucao ) {
		$this->lang = $lang;

		if ( ! $this->dados( 'paginas' ) ) {
			WP_CLI::warning( sprintf( '  Idioma "%s" sem trait de conteúdo — ignorado.', $lang ) );

			return;
		}

		WP_CLI::log( sprintf( '— Idioma "%s" ————————————————————', $lang ) );

		WP_CLI::log( '  categorias de solução…' );
		$termos = $this->traduzir_termos_solucao( $termos_solucao );

		WP_CLI::log( '  páginas…' );
		$this->traduzir_paginas();

		WP_CLI::log( '  CPTs de catálogo…' );
		$this->traduzir_cpts();

		WP_CLI::log( '  FAQ das soluções…' );
		$this->traduzir_faq_solucoes();

		WP_CLI::log( '  landings de solução…' );
		$this->traduzir_solucoes( $termos );

		WP_CLI::log( '  menus…' );
		$this->criar_menus_traduzidos( $termos );

		WP_CLI::log( '  strings do Customizer…' );
		$this->traduzir_strings_polylang();

		WP_CLI::log( '  traduções órfãs…' );
		$this->remover_traducoes_orfas();
	}

	/* =====================================================================
	   PÁGINAS
	   ===================================================================== */

	/**
	 * Caminhos internos do idioma atual: `/contato/` => `/es/contacto/`.
	 *
	 * Os archives (`/cases/`, `/solucoes/`) mantêm o slug: quem traduz a URL de
	 * um archive é o prefixo de idioma do Polylang.
	 *
	 * @return array<string,string>
	 */
	protected function mapear_caminhos() {
		$mapa = array(
			'/cases/'    => '/' . $this->lang . '/cases/',
			'/solucoes/' => '/' . $this->lang . '/solucoes/',
		);

		foreach ( $this->dados( 'paginas' ) as $slug_pt => $pagina ) {
			$mapa[ '/' . $slug_pt . '/' ] = '/' . $this->lang . '/' . $pagina[0] . '/';
		}

		return $mapa;
	}

	/**
	 * Cria as páginas do idioma atual e preenche as que têm campos.
	 *
	 * @return void
	 */
	protected function traduzir_paginas() {
		$this->caminhos = $this->mapear_caminhos();

		$criadas = 0;

		foreach ( $this->dados( 'paginas' ) as $slug_pt => $pagina ) {
			$id = $this->traduzir_post(
				'pagina:' . $slug_pt,
				'page',
				array(
					'post_title' => $pagina[1],
					'post_name'  => $pagina[0],
				),
				$this->texto( $this->chave_de_metodo( $slug_pt ) )
			);

			if ( $id ) {
				++$criadas;
			}
		}

		WP_CLI::log( sprintf( '    páginas: %d.', $criadas ) );
	}

	/**
	 * Monta o valor de um campo ACF do tipo Link já apontando para o idioma atual.
	 *
	 * @param string $titulo  Texto do botão traduzido.
	 * @param string $caminho Caminho interno em português (ex.: '/contato/').
	 * @param string $target  Alvo do link.
	 * @return array{title:string,url:string,target:string}
	 */
	protected function link_traduzido( $titulo, $caminho, $target = '' ) {
		$partes  = explode( '#', $caminho, 2 );
		$base    = $partes[0];
		$ancora  = isset( $partes[1] ) ? '#' . $partes[1] : '';
		$destino = $this->caminhos[ $base ] ?? $base;

		return array(
			'title'  => $titulo,
			'url'    => $this->url_absoluta( $destino ) . $ancora,
			'target' => $target,
		);
	}

	/* =====================================================================
	   CPTs
	   ===================================================================== */

	/**
	 * Traduz agentes, eventos, cases e as FAQ gerais no idioma atual.
	 *
	 * @return void
	 */
	protected function traduzir_cpts() {
		$total = 0;

		foreach ( $this->dados( 'agentes' ) as $slug => $item ) {
			$total += $this->traduzir_post(
				'agente:' . $slug,
				'cli_agente',
				array( 'post_title' => $item[0] ),
				array( 'descricao' => $item[1] )
			) ? 1 : 0;
		}

		foreach ( $this->dados( 'eventos' ) as $slug => $item ) {
			$total += $this->traduzir_post(
				'evento:' . $slug,
				'cli_evento',
				array( 'post_title' => $item[0] ),
				array( 'descricao' => $item[1] )
			) ? 1 : 0;
		}

		foreach ( $this->dados( 'cases' ) as $slug => $item ) {
			$total += $this->traduzir_post(
				'case:' . $slug,
				'cli_case',
				array(
					'post_title'   => $item['titulo'],
					'post_excerpt' => $item['resumo'],
					'post_content' => $item['conteudo'] ?? '',
				),
				$item['campos'] ?? array()
			) ? 1 : 0;
		}

		foreach ( $this->dados( 'faq' ) as $slug => $item ) {
			$total += $this->traduzir_post(
				'faq:' . $slug,
				'cli_faq',
				array(
					'post_title'   => $item[0],
					'post_content' => $item[1],
				)
			) ? 1 : 0;
		}

		WP_CLI::log( sprintf( '    CPTs: %d itens.', $total ) );
	}

	/**
	 * Traduz as FAQ vinculadas às landings de solução.
	 *
	 * Roda antes das landings: é `traduzir_referencia()` que troca os IDs do
	 * português pelos do idioma atual em `solucao_faq_itens`. Sem a FAQ
	 * traduzida, o Polylang filtra a lista por idioma e a seção **some** da
	 * landing, em silêncio.
	 *
	 * @return void
	 */
	protected function traduzir_faq_solucoes() {
		$total = 0;

		foreach ( $this->dados( 'faq_solucoes' ) as $slug => $item ) {
			$id = $this->traduzir_post(
				'faq:' . $slug,
				'cli_faq',
				array(
					'post_title'   => $item[0],
					'post_content' => $item[1],
				)
			);

			if ( $id ) {
				++$total;
			}
		}

		WP_CLI::log( sprintf( '    FAQ de solução: %d.', $total ) );
	}

	/* =====================================================================
	   SOLUÇÕES
	   ===================================================================== */

	/**
	 * Cria os termos da taxonomia no idioma atual e vincula ao português.
	 *
	 * @param array<string,int> $termos_pt chave => term_id em português.
	 * @return array<string,int> chave => term_id traduzido.
	 */
	protected function traduzir_termos_solucao( $termos_pt ) {
		$tax        = 'cli_categoria_solucao';
		$traduzidos = array();
		$hierarquia = array();

		foreach ( $this->dados( 'termos_solucao' ) as $chave => $dados ) {
			if ( empty( $termos_pt[ $chave ] ) ) {
				continue;
			}

			$id = $this->traduzir_termo( (int) $termos_pt[ $chave ], $tax, $dados[0], $dados[1] );

			if ( ! $id ) {
				continue;
			}

			$traduzidos[ $chave ] = $id;
			$pai_pt               = (int) get_term_field( 'parent', (int) $termos_pt[ $chave ], $tax );

			if ( $pai_pt ) {
				$hierarquia[ $chave ] = $pai_pt;
			}
		}

		// A hierarquia só pode ser aplicada depois que todos os pais existem.
		foreach ( $hierarquia as $chave => $pai_pt ) {
			$pai = (int) pll_get_term( $pai_pt, $this->lang );

			if ( $pai ) {
				wp_update_term( $traduzidos[ $chave ], $tax, array( 'parent' => $pai ) );
			}
		}

		WP_CLI::log( sprintf( '    categorias de solução: %d.', count( $traduzidos ) ) );

		return $traduzidos;
	}

	/**
	 * Traduz as landings de solução.
	 *
	 * Solução sem método de texto no idioma vira stub: título e categoria
	 * traduzidos, seções de texto vazias — do mesmo jeito que uma solução ainda
	 * não preenchida em português. Melhor uma seção ausente que uma seção em
	 * português no meio de uma página em espanhol.
	 *
	 * @param array<string,int> $termos chave => term_id traduzido.
	 * @return void
	 */
	protected function traduzir_solucoes( $termos ) {
		$total  = 0;
		$stubs  = 0;

		foreach ( $this->dados( 'termos_solucao' ) as $chave => $dados ) {
			// Categorias (nível 1) não têm post; só os tipos têm.
			if ( ! $this->id_do_seed( 'solucao:' . $chave, 'cli_solucao' ) ) {
				continue;
			}

			$textos = $this->texto( 'solucao_' . $this->chave_de_metodo( $chave ) );
			$post   = array( 'post_title' => $dados[0] );

			if ( isset( $dados[2] ) ) {
				$post['post_name'] = $dados[2];
			}

			$id = $this->traduzir_post( 'solucao:' . $chave, 'cli_solucao', $post, $textos, ! $textos );

			if ( $id ) {
				++$total;

				if ( ! $textos ) {
					++$stubs;
				}
			}
		}

		WP_CLI::log( sprintf( '    landings de solução: %d (%d como stub).', $total, $stubs ) );
	}

	/**
	 * URL de um termo de solução no idioma atual.
	 *
	 * @param string            $chave  Chave do seed.
	 * @param array<string,int> $termos chave => term_id traduzido.
	 * @return string
	 */
	protected function url_termo( $chave, $termos ) {
		$base = home_url( '/' . $this->lang . '/solucoes/' );

		if ( empty( $termos[ $chave ] ) ) {
			return $base;
		}

		$link = get_term_link( (int) $termos[ $chave ], 'cli_categoria_solucao' );

		return is_wp_error( $link ) ? $base : $link;
	}

	/* =====================================================================
	   FORMULÁRIO (CF7)
	   ===================================================================== */

	/**
	 * Cria (ou atualiza) uma cópia traduzida do formulário do Contact Form 7.
	 *
	 * O CF7 guarda o formulário em um post `wpcf7_contact_form`, que fica fora
	 * do Polylang — sem uma cópia por idioma, a página traduzida renderiza
	 * rótulos e mensagens de erro em português. As configurações de envio
	 * (`_mail`) são copiadas do original: o e-mail vai para a própria CLI e não
	 * muda de idioma.
	 *
	 * @param string $slug      Slug do post do formulário traduzido.
	 * @param string $titulo    Título do formulário no painel.
	 * @param string $form      Corpo do formulário (shortcodes CF7).
	 * @param array  $mensagens Mensagens de validação traduzidas.
	 * @return int ID do formulário traduzido, 0 em caso de falha.
	 */
	protected function criar_form_cf7_traduzido( $slug, $titulo, $form, $mensagens ) {
		if ( ! post_type_exists( 'wpcf7_contact_form' ) ) {
			return 0;
		}

		$origem = get_posts(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'name'           => 'contato-cli',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
			)
		);

		if ( ! $origem ) {
			WP_CLI::warning( '  Formulário CF7 em português não encontrado — cópia traduzida ignorada.' );

			return 0;
		}

		$origem_id = (int) $origem[0]->ID;

		$existente = get_posts(
			array(
				'post_type'      => 'wpcf7_contact_form',
				'name'           => $slug,
				'posts_per_page' => 1,
				'post_status'    => 'publish',
			)
		);

		if ( $existente ) {
			$id = (int) $existente[0]->ID;
			wp_update_post(
				array(
					'ID'         => $id,
					'post_title' => $titulo,
				)
			);
		} else {
			$id = wp_insert_post(
				array(
					'post_title'  => $titulo,
					'post_name'   => $slug,
					'post_type'   => 'wpcf7_contact_form',
					'post_status' => 'publish',
				),
				true
			);

			if ( is_wp_error( $id ) || ! $id ) {
				WP_CLI::warning( sprintf( '  Falha ao criar o formulário CF7 "%s".', $slug ) );

				return 0;
			}
		}

		// Envio, destinatário e configurações extras seguem o original.
		foreach ( array( '_mail', '_mail_2', '_additional_settings' ) as $meta ) {
			update_post_meta( $id, $meta, get_post_meta( $origem_id, $meta, true ) );
		}

		update_post_meta( $id, '_form', $form );
		update_post_meta( $id, '_messages', $mensagens );

		return (int) $id;
	}

	/**
	 * Grava as traduções das strings de opção (Customizer, descrição do site)
	 * direto na tabela de strings do Polylang.
	 *
	 * São `theme_mod`/`option`: não são gettext nem post, e por isso não
	 * apareceriam traduzidas nem com o `.mo` do tema compilado. O registro de
	 * cada string está em `cliconnect_strings_customizer()` (inc/polylang.php);
	 * os textos traduzidos vêm do trait de conteúdo do idioma, para não
	 * depender do painel.
	 *
	 * @return void
	 */
	protected function traduzir_strings_polylang() {
		$traducoes = $this->dados( 'strings_polylang' );

		if ( ! $traducoes ) {
			return;
		}

		if ( ! class_exists( 'PLL_MO' ) || ! function_exists( 'PLL' ) ) {
			WP_CLI::warning( '  API de strings do Polylang indisponível — strings do Customizer não traduzidas.' );

			return;
		}

		$idioma = PLL()->model->get_language( $this->lang );

		if ( ! $idioma ) {
			return;
		}

		$mo = new PLL_MO();
		$mo->import_from_db( $idioma );

		foreach ( $traducoes as $original => $traduzido ) {
			$mo->add_entry( $mo->make_entry( $original, $traduzido ) );
		}

		$mo->export_to_db( $idioma );

		WP_CLI::log( sprintf( '    strings do Customizer: %d.', count( $traducoes ) ) );
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
	 * @param bool   $stub      Copia só a estrutura e deixa o texto vazio.
	 * @return int ID da tradução, 0 em caso de falha.
	 */
	protected function traduzir_post( $slug_seed, $post_type, $dados, $textos = array(), $stub = false ) {
		$origem_id = $this->id_do_seed( $slug_seed, $post_type );

		if ( ! $origem_id ) {
			WP_CLI::warning( sprintf( '  "%s" não existe em português — tradução ignorada.', $slug_seed ) );

			return 0;
		}

		if ( self::LANG_BASE !== pll_get_post_language( $origem_id ) ) {
			pll_set_post_language( $origem_id, self::LANG_BASE );
		}

		$slug_en = $slug_seed . $this->sufixo();

		// Adota uma tradução já vinculada (feita à mão) em vez de duplicá-la.
		$existente = (int) pll_get_post( $origem_id, $this->lang );

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

		pll_set_post_language( $traducao_id, $this->lang );
		/*
		 * pll_save_post_translations() grava o grupo **inteiro**: passar só o par
		 * pt + idioma atual apaga as demais traduções já vinculadas. Com dois ou
		 * mais idiomas isso desfaz, em silêncio, o trabalho da passada anterior.
		 */
		$grupo                    = pll_get_post_translations( $origem_id );
		$grupo[ self::LANG_BASE ] = $origem_id;
		$grupo[ $this->lang ]     = $traducao_id;

		pll_save_post_translations( $grupo );

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
		$this->copiar_campos_acf( $origem_id, $traducao_id, $textos, $slug_seed, $stub );

		return $traducao_id;
	}

	/**
	 * Copia os campos ACF do original para a tradução, aplicando os textos.
	 *
	 * @param int    $origem_id  Post em português.
	 * @param int    $destino_id Post em inglês.
	 * @param array  $textos     nome do campo => valor traduzido.
	 * @param string $rotulo     Identificador usado nos avisos.
	 * @param bool   $stub       Deixa vazios os campos de texto sem tradução.
	 * @return void
	 */
	protected function copiar_campos_acf( $origem_id, $destino_id, $textos, $rotulo = '', $stub = false ) {
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
			} elseif ( $stub && in_array( $campo['type'] ?? '', $this->campos_de_texto, true ) ) {
				// Sem tradução: melhor a seção ausente que em português.
				$valor = '';
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

		$traducao = (int) pll_get_post( (int) $valor, $this->lang );

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

		if ( isset( $this->caminhos[ $caminho ] ) ) {
			return str_replace( $caminho, $this->caminhos[ $caminho ], $url );
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
						$traducao = (int) pll_get_term( (int) $termo_id, $this->lang );

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

		$traducao_id = (int) pll_get_term( $origem_id, $this->lang );

		if ( ! $traducao_id ) {
			$existente = get_term_by( 'slug', $slug, $taxonomia );

			if ( $existente && $this->lang === pll_get_term_language( $existente->term_id ) ) {
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

		update_term_meta( $traducao_id, self::META, get_term_meta( $origem_id, self::META, true ) . $this->sufixo() );

		pll_set_term_language( $traducao_id, $this->lang );
		// Mesmo cuidado de traduzir_post(): o grupo é gravado por inteiro.
		$grupo                    = pll_get_term_translations( $origem_id );
		$grupo[ self::LANG_BASE ] = $origem_id;
		$grupo[ $this->lang ]     = $traducao_id;

		pll_save_term_translations( $grupo );

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
					'lang'           => $this->lang,
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
	 * Monta os menus do idioma atual.
	 *
	 * A estrutura de cada menu vive no trait de conteúdo (`criar_menus_es()`),
	 * porque os rótulos são texto traduzido; daqui sai só o despacho.
	 *
	 * @param array<string,int> $termos chave => term_id traduzido.
	 * @return void
	 */
	protected function criar_menus_traduzidos( $termos ) {
		$metodo = 'criar_menus_' . $this->lang;

		if ( ! method_exists( $this, $metodo ) ) {
			return;
		}

		$this->$metodo( $termos );
	}

	/**
	 * Cria um menu e o registra só na location do idioma atual.
	 *
	 * As locations do Polylang são por idioma; `montar_menu()` aponta o menu
	 * português para todas elas. Aqui sobrescrevemos apenas a do idioma atual.
	 *
	 * @param string $location Slug da location registrada.
	 * @param string $nome     Nome legível do menu.
	 * @param array  $itens    Itens no formato de normalizar_itens().
	 * @return void
	 */
	protected function montar_menu_traduzido( $location, $nome, $itens ) {
		$slug = 'cli-' . $location . '-' . $this->lang;
		$menu = wp_get_nav_menu_object( $slug );

		if ( $menu ) {
			wp_delete_nav_menu( $menu->term_id );
		}

		$menu_id = wp_create_nav_menu( $slug );

		if ( is_wp_error( $menu_id ) ) {
			WP_CLI::warning( sprintf( 'Falha ao criar o menu %s (%s).', $location, $this->lang ) );

			return;
		}

		wp_update_term( $menu_id, 'nav_menu', array( 'name' => $nome ) );

		$this->inserir_itens( $menu_id, $itens, 0 );

		$opcoes = get_option( 'polylang' );

		if ( is_array( $opcoes ) ) {
			$opcoes['nav_menus'][ get_stylesheet() ][ $location ][ $this->lang ] = $menu_id;
			update_option( 'polylang', $opcoes );
		}

		WP_CLI::log( sprintf( '    menu "%s" → location %s (%s).', $nome, $location, $this->lang ) );
	}

	/**
	 * URL absoluta de uma página do seed no idioma atual.
	 *
	 * @param string $slug Slug de seed da página em português (sem 'pagina:').
	 * @return string
	 */
	protected function url_pagina_traduzida( $slug ) {
		$origem = $this->id_do_seed( 'pagina:' . $slug, 'page' );
		$alvo   = $origem ? (int) pll_get_post( $origem, $this->lang ) : 0;
		$link   = $alvo ? get_permalink( $alvo ) : '';

		return $link ? $link : home_url( '/' . $this->lang . '/' );
	}
}
