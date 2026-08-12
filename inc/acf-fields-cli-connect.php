<?php
/**
 * Grupo ACF da página CLI Connect.
 *
 * Zero texto fixo: todo conteúdo editável sai daqui.
 * Localização por page_template == page-cli-connect.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página CLI Connect.
 *
 * @return void
 */
function cliconnect_acf_fields_cli_connect() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$cc_text = static function ( $key, $label, $name, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_cc_' . $key,
				'label' => $label,
				'name'  => $name,
				'type'  => 'text',
			),
			$extra
		);
	};

	$cc_tab = static function ( $key, $label ) {
		return array(
			'key'       => 'field_cc_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$cc_link = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_cc_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'link',
			'return_format' => 'array',
		);
	};

	$cc_textarea = static function ( $key, $label, $name, $rows = 3 ) {
		return array(
			'key'       => 'field_cc_' . $key,
			'label'     => $label,
			'name'      => $name,
			'type'      => 'textarea',
			'rows'      => $rows,
			'new_lines' => '',
		);
	};

	$cc_image = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_cc_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
		);
	};

	$fields = array();

	/* --- 1. HERO ------------------------------------------------------------ */
	$fields[] = $cc_tab( 'hero', '1 · Hero' );
	$fields[] = $cc_text( 'hero_eyebrow', 'Eyebrow', 'cc_hero_eyebrow' );
	$fields[] = $cc_text( 'hero_titulo', 'Título (linha 1)', 'cc_hero_titulo' );
	$fields[] = $cc_text( 'hero_titulo_destaque', 'Título (linha 2 — destaque azul)', 'cc_hero_titulo_destaque' );
	$fields[] = $cc_textarea( 'hero_texto', 'Texto de apoio', 'cc_hero_texto' );
	$fields[] = $cc_link( 'hero_botao', 'Botão', 'cc_hero_botao' );
	$fields[] = $cc_image( 'hero_imagem', 'Imagem (órbita direita)', 'cc_hero_imagem' );

	/* --- 2. BRANDS ---------------------------------------------------------- */
	$fields[] = $cc_tab( 'brands', '2 · Brands' );
	$fields[] = $cc_text( 'brands_titulo', 'Título / legenda', 'cc_brands_titulo' );

	/* --- 3. SOLUÇÃO --------------------------------------------------------- */
	$fields[] = $cc_tab( 'solucao', '3 · Solução' );
	$fields[] = $cc_text( 'solucao_titulo', 'Título da seção', 'cc_solucao_titulo' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cc_image( "solucao_{$i}_imagem", "Card {$i} — imagem", "cc_solucao_{$i}_imagem" );
		$fields[] = $cc_text( "solucao_{$i}_titulo", "Card {$i} — título", "cc_solucao_{$i}_titulo" );
		$fields[] = $cc_textarea( "solucao_{$i}_texto", "Card {$i} — descrição", "cc_solucao_{$i}_texto", 3 );
		for ( $j = 1; $j <= 3; $j++ ) {
			$fields[] = $cc_text( "solucao_{$i}_bullet_{$j}", "Card {$i} — bullet {$j}", "cc_solucao_{$i}_bullet_{$j}" );
		}
	}

	/* --- 4. IMPLANTAÇÃO ----------------------------------------------------- */
	$fields[] = $cc_tab( 'implantacao', '4 · Implantação' );
	$fields[] = $cc_text( 'impl_eyebrow', 'Eyebrow', 'cc_impl_eyebrow' );
	$fields[] = $cc_text( 'impl_titulo', 'Título (linha 1)', 'cc_impl_titulo' );
	$fields[] = $cc_text( 'impl_titulo_2', 'Título (linha 2)', 'cc_impl_titulo_2' );
	$fields[] = $cc_textarea( 'impl_texto', 'Subtítulo', 'cc_impl_texto', 2 );
	$fields[] = $cc_text( 'impl_sem_label', 'Label "Sem" (ex: SEM CLI CONNECT)', 'cc_impl_sem_label' );
	$fields[] = $cc_text( 'impl_sem_tempo', 'Tempo "Sem" (ex: 1 MÊS)', 'cc_impl_sem_tempo' );
	for ( $i = 1; $i <= 7; $i++ ) {
		$fields[] = $cc_text( "impl_sem_etapa_{$i}", "Etapa sem CLI {$i}", "cc_impl_sem_etapa_{$i}" );
	}
	$fields[] = $cc_text( 'impl_com_label', 'Label "Com" (ex: COM CLI CONNECT)', 'cc_impl_com_label' );
	$fields[] = $cc_text( 'impl_com_tempo', 'Tempo "Com" (ex: 5 DIAS)', 'cc_impl_com_tempo' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cc_text( "impl_com_etapa_{$i}", "Etapa com CLI {$i}", "cc_impl_com_etapa_{$i}" );
	}

	/* --- 5. BOOMI ----------------------------------------------------------- */
	$fields[] = $cc_tab( 'boomi', '5 · Boomi' );
	$fields[] = $cc_text( 'boomi_eyebrow', 'Eyebrow', 'cc_boomi_eyebrow' );
	$fields[] = $cc_text( 'boomi_titulo', 'Título', 'cc_boomi_titulo' );
	$fields[] = $cc_textarea( 'boomi_texto', 'Texto de apoio', 'cc_boomi_texto', 3 );

	/* --- 6. OPERAÇÕES CRÍTICAS ---------------------------------------------- */
	$fields[] = $cc_tab( 'operacoes', '6 · Operações Críticas' );
	$fields[] = $cc_text( 'operacoes_eyebrow', 'Eyebrow', 'cc_operacoes_eyebrow' );
	$fields[] = $cc_text( 'operacoes_titulo', 'Título (linha 1)', 'cc_operacoes_titulo' );
	$fields[] = $cc_text( 'operacoes_titulo_2', 'Título (linha 2)', 'cc_operacoes_titulo_2' );
	$fields[] = $cc_textarea( 'operacoes_texto', 'Texto de apoio', 'cc_operacoes_texto', 3 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cc_text( "operacoes_bullet_{$i}", "Bullet {$i}", "cc_operacoes_bullet_{$i}" );
	}

	/* --- 5. DASHBOARD ------------------------------------------------------- */
	$fields[] = $cc_tab( 'dashboard', '5 · Dashboard' );
	$fields[] = $cc_text( 'dashboard_eyebrow', 'Eyebrow', 'cc_dashboard_eyebrow' );
	$fields[] = $cc_text( 'dashboard_titulo', 'Título', 'cc_dashboard_titulo' );
	$fields[] = $cc_textarea( 'dashboard_texto', 'Texto de apoio', 'cc_dashboard_texto', 3 );
	$fields[] = $cc_link( 'dashboard_botao', 'Botão', 'cc_dashboard_botao' );
	$fields[] = $cc_image( 'dashboard_imagem', 'Imagem', 'cc_dashboard_imagem' );

	/* --- 6. DEPOIMENTO ------------------------------------------------------ */
	$fields[] = $cc_tab( 'depoimento', '6 · Depoimento' );
	$fields[] = $cc_image( 'dep_foto', 'Foto', 'cc_dep_foto' );
	$fields[] = $cc_text( 'dep_nome', 'Nome', 'cc_dep_nome' );
	$fields[] = $cc_text( 'dep_cargo', 'Cargo / empresa', 'cc_dep_cargo' );
	$fields[] = $cc_textarea( 'dep_texto', 'Citação', 'cc_dep_texto', 4 );
	$fields[] = $cc_link( 'dep_botao', 'Botão (confira o case)', 'cc_dep_botao' );

	/* --- 7. AGENTSTUDIO ----------------------------------------------------- */
	$fields[] = $cc_tab( 'agentstudio', '7 · AgentStudio' );
	$fields[] = $cc_text( 'as_titulo', 'Título da seção', 'cc_as_titulo' );
	$fields[] = $cc_textarea( 'as_texto', 'Subtítulo/corpo', 'cc_as_texto', 2 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cc_text( "as_card_{$i}_titulo", "Card {$i} — título", "cc_as_card_{$i}_titulo" );
		$fields[] = $cc_textarea( "as_card_{$i}_texto", "Card {$i} — descrição", "cc_as_card_{$i}_texto", 3 );
		for ( $j = 1; $j <= 3; $j++ ) {
			$fields[] = $cc_text( "as_card_{$i}_item_{$j}", "Card {$i} — item {$j}", "cc_as_card_{$i}_item_{$j}" );
		}
	}
	$fields[] = $cc_text( 'as_criar_titulo', 'Card extra — título', 'cc_as_criar_titulo' );
	$fields[] = $cc_textarea( 'as_criar_texto', 'Card extra — texto', 'cc_as_criar_texto', 3 );

	/* --- 8. NA PRÁTICA ------------------------------------------------------ */
	$fields[] = $cc_tab( 'na_pratica', '8 · Na Prática' );
	$fields[] = $cc_text( 'np_eyebrow', 'Eyebrow', 'cc_np_eyebrow' );
	$fields[] = $cc_text( 'np_titulo', 'Título', 'cc_np_titulo' );
	$fields[] = $cc_textarea( 'np_texto', 'Texto de apoio', 'cc_np_texto', 2 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cc_text( "np_bullet_{$i}", "Bullet {$i}", "cc_np_bullet_{$i}" );
	}
	$fields[] = $cc_image( 'np_imagem', 'Imagem', 'cc_np_imagem' );

	/* --- 9. COMPARATIVO ----------------------------------------------------- */
	$fields[] = $cc_tab( 'comparativo', '9 · Comparativo' );
	$fields[] = $cc_text( 'comp_titulo', 'Título da seção', 'cc_comp_titulo' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $cc_text( "comp_row_{$i}", "Linha {$i} — texto", "cc_comp_row_{$i}" );
	}

	/* --- 10. DEPARTAMENTOS -------------------------------------------------- */
	$fields[] = $cc_tab( 'departamentos', '10 · Departamentos' );
	$fields[] = $cc_text( 'dep_titulo', 'Título da seção', 'cc_dep_titulo' );
	$fields[] = $cc_image( 'dep_imagem', 'Imagem (direita)', 'cc_dep_imagem' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $cc_text( "dep_item_{$i}_numero", "Item {$i} — número (ex: 01)", "cc_dep_item_{$i}_numero" );
		$fields[] = $cc_text( "dep_item_{$i}_titulo", "Item {$i} — título", "cc_dep_item_{$i}_titulo" );
		$fields[] = $cc_textarea( "dep_item_{$i}_texto", "Item {$i} — texto", "cc_dep_item_{$i}_texto", 2 );
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_cli_cli_connect',
			'title'    => 'CLI Connect — Conteúdo',
			'fields'   => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-cli-connect.php',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_cli_connect' );
