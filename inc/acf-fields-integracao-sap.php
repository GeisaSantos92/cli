<?php
/**
 * Grupo ACF da página Integração SAP.
 *
 * Zero texto fixo: todo conteúdo editável sai daqui.
 * Localização por page_template == page-integracao-sap.php.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra o grupo de campos da página Integração SAP.
 *
 * @return void
 */
function cliconnect_acf_fields_integracao_sap() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$sap_text = static function ( $key, $label, $name, $extra = array() ) {
		return array_merge(
			array(
				'key'   => 'field_sap_' . $key,
				'label' => $label,
				'name'  => $name,
				'type'  => 'text',
			),
			$extra
		);
	};

	$sap_tab = static function ( $key, $label ) {
		return array(
			'key'       => 'field_sap_tab_' . $key,
			'label'     => $label,
			'name'      => '',
			'type'      => 'tab',
			'placement' => 'left',
		);
	};

	$sap_link = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_sap_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'link',
			'return_format' => 'array',
		);
	};

	$sap_textarea = static function ( $key, $label, $name, $rows = 3 ) {
		return array(
			'key'       => 'field_sap_' . $key,
			'label'     => $label,
			'name'      => $name,
			'type'      => 'textarea',
			'rows'      => $rows,
			'new_lines' => '',
		);
	};

	$sap_image = static function ( $key, $label, $name ) {
		return array(
			'key'           => 'field_sap_' . $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'image',
			'return_format' => 'id',
			'preview_size'  => 'medium',
		);
	};

	$fields = array();

	/* --- 1. HERO --------------------------------------------------------------- */
	$fields[] = $sap_tab( 'hero', '1 · Hero' );
	$fields[] = $sap_text( 'hero_titulo_azul', 'Título — parte azul', 'sap_hero_titulo_azul' );
	$fields[] = $sap_text( 'hero_titulo_escuro', 'Título — parte escura', 'sap_hero_titulo_escuro' );
	$fields[] = $sap_textarea( 'hero_texto', 'Texto de apoio', 'sap_hero_texto', 3 );
	$fields[] = $sap_link( 'hero_botao', 'Botão', 'sap_hero_botao' );
	$fields[] = $sap_image( 'hero_imagem', 'Imagem (direita)', 'sap_hero_imagem' );

	/* --- 2. VELOCIDADE --------------------------------------------------------- */
	$fields[] = $sap_tab( 'velocidade', '2 · Velocidade' );
	$fields[] = $sap_text( 'vel_eyebrow', 'Eyebrow', 'sap_vel_eyebrow' );
	$fields[] = $sap_text( 'vel_titulo', 'Título', 'sap_vel_titulo' );
	$fields[] = $sap_textarea( 'vel_texto', 'Texto de apoio', 'sap_vel_texto', 2 );
	$fields[] = $sap_text( 'vel_sem_label', 'Rótulo — sem CLI Connect', 'sap_vel_sem_label' );
	$fields[] = $sap_text( 'vel_sem_tempo', 'Tempo — sem CLI Connect', 'sap_vel_sem_tempo' );
	for ( $i = 1; $i <= 7; $i++ ) {
		$fields[] = $sap_text( "vel_sem_{$i}", "Etapa cinza {$i}", "sap_vel_sem_{$i}" );
	}
	$fields[] = $sap_text( 'vel_com_label', 'Rótulo — com CLI Connect', 'sap_vel_com_label' );
	$fields[] = $sap_text( 'vel_com_tempo', 'Tempo — com CLI Connect', 'sap_vel_com_tempo' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $sap_text( "vel_com_{$i}", "Etapa azul {$i}", "sap_vel_com_{$i}" );
	}

	/* --- 3. SAP INTEGRADO (CONECTAR) ------------------------------------------ */
	$fields[] = $sap_tab( 'conectar', '3 · SAP Integrado' );
	$fields[] = $sap_text( 'con_eyebrow', 'Eyebrow', 'sap_con_eyebrow' );
	$fields[] = $sap_textarea( 'con_titulo', 'Título', 'sap_con_titulo', 2 );
	$fields[] = $sap_textarea( 'con_texto', 'Texto de apoio', 'sap_con_texto', 3 );
	$fields[] = $sap_image( 'con_imagem', 'Imagem (direita)', 'sap_con_imagem' );

	/* --- 4. SAP SINCRONIZADO --------------------------------------------------- */
	$fields[] = $sap_tab( 'sincronizar', '4 · SAP Sincronizado' );
	$fields[] = $sap_text( 'sin_eyebrow', 'Eyebrow', 'sap_sin_eyebrow' );
	$fields[] = $sap_textarea( 'sin_titulo', 'Título', 'sap_sin_titulo', 2 );
	$fields[] = $sap_textarea( 'sin_texto', 'Texto de apoio', 'sap_sin_texto', 3 );
	$fields[] = $sap_image( 'sin_imagem', 'Imagem (direita)', 'sap_sin_imagem' );

	/* --- 5. LIBERE RECURSOS ---------------------------------------------------- */
	$fields[] = $sap_tab( 'recursos', '5 · Libere Recursos' );
	$fields[] = $sap_text( 'rec_eyebrow', 'Eyebrow', 'sap_rec_eyebrow' );
	$fields[] = $sap_textarea( 'rec_titulo', 'Título', 'sap_rec_titulo', 2 );
	$fields[] = $sap_textarea( 'rec_texto', 'Texto de apoio', 'sap_rec_texto', 2 );
	$fields[] = $sap_text( 'rec_metrica_numero', 'Métrica — número', 'sap_rec_metrica_numero' );
	$fields[] = $sap_text( 'rec_metrica_label', 'Métrica — rótulo', 'sap_rec_metrica_label' );
	$fields[] = $sap_image( 'rec_imagem', 'Imagem base (direita)', 'sap_rec_imagem' );
	$fields[] = $sap_image( 'rec_imagem_overlay', 'Imagem overlay (direita)', 'sap_rec_imagem_overlay' );

	/* --- 6. DEPOIMENTO --------------------------------------------------------- */
	$fields[] = $sap_tab( 'depoimento', '6 · Depoimento' );
	$fields[] = $sap_image( 'dep_foto', 'Foto do depoente', 'sap_dep_foto' );
	$fields[] = $sap_text( 'dep_nome', 'Nome', 'sap_dep_nome' );
	$fields[] = $sap_text( 'dep_cargo', 'Cargo', 'sap_dep_cargo' );
	$fields[] = $sap_textarea( 'dep_frase', 'Frase / quote', 'sap_dep_frase', 3 );
	$fields[] = $sap_link( 'dep_botao', 'Botão "Confira o case"', 'sap_dep_botao' );

	/* --- 7. SISTEMAS ----------------------------------------------------------- */
	$fields[] = $sap_tab( 'sistemas', '7 · Sistemas' );
	$fields[] = $sap_textarea( 'sis_titulo', 'Título (2 linhas)', 'sap_sis_titulo', 2 );
	$fields[] = $sap_text( 'sis_subtitulo', 'Subtítulo', 'sap_sis_subtitulo' );
	for ( $i = 1; $i <= 10; $i++ ) {
		$fields[] = $sap_text( "sis_{$i}", "Sistema {$i}", "sap_sis_{$i}" );
	}

	/* --- 8. CLEAN CORE --------------------------------------------------------- */
	$fields[] = $sap_tab( 'cleancore', '8 · Clean Core' );
	$fields[] = $sap_text( 'cc_eyebrow', 'Eyebrow', 'sap_cc_eyebrow' );
	$fields[] = $sap_text( 'cc_titulo', 'Título', 'sap_cc_titulo' );
	$fields[] = $sap_textarea( 'cc_texto', 'Texto de apoio', 'sap_cc_texto', 2 );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $sap_text( "cc_{$i}_titulo", "Card {$i} — título", "sap_cc_{$i}_titulo" );
		$fields[] = $sap_textarea( "cc_{$i}_texto", "Card {$i} — texto", "sap_cc_{$i}_texto", 2 );
		$fields[] = $sap_image( "cc_{$i}_imagem", "Card {$i} — imagem", "sap_cc_{$i}_imagem" );
	}
	$fields[] = $sap_image( 'cc_3_imagem_b', 'Card 3 — imagem overlay', 'sap_cc_3_imagem_b' );

	/* --- 9. INTEGRAÇÕES INCLUSAS ----------------------------------------------- */
	$fields[] = $sap_tab( 'integracoes', '9 · Integrações' );
	$fields[] = $sap_text( 'int_eyebrow', 'Eyebrow', 'sap_int_eyebrow' );
	$fields[] = $sap_textarea( 'int_titulo', 'Título (2 linhas)', 'sap_int_titulo', 2 );
	$fields[] = $sap_link( 'int_botao', 'Botão CTA', 'sap_int_botao' );
	$fields[] = $sap_text( 'int_nota', 'Nota abaixo do botão', 'sap_int_nota' );
	for ( $i = 1; $i <= 8; $i++ ) {
		$fields[] = $sap_text( "int_{$i}_titulo", "Integração {$i} — título", "sap_int_{$i}_titulo" );
		$fields[] = $sap_text( "int_{$i}_desc", "Integração {$i} — descrição", "sap_int_{$i}_desc" );
		$fields[] = $sap_image( "int_{$i}_logo", "Integração {$i} — logo parceiro", "sap_int_{$i}_logo" );
	}

	/* --- 10. MIGRAÇÃO ---------------------------------------------------------- */
	$fields[] = $sap_tab( 'migracao', '10 · Migração' );
	$fields[] = $sap_textarea( 'mig_titulo', 'Título', 'sap_mig_titulo', 2 );
	$fields[] = $sap_textarea( 'mig_texto', 'Texto de apoio', 'sap_mig_texto', 3 );
	$fields[] = $sap_link( 'mig_botao', 'Botão CTA', 'sap_mig_botao' );

	/* --- 11. BENEFÍCIOS -------------------------------------------------------- */
	$fields[] = $sap_tab( 'beneficios', '11 · Benefícios' );
	$fields[] = $sap_text( 'ben_eyebrow', 'Eyebrow', 'sap_ben_eyebrow' );
	$fields[] = $sap_text( 'ben_titulo', 'Título', 'sap_ben_titulo' );
	$fields[] = $sap_link( 'ben_botao', 'Botão CTA', 'sap_ben_botao' );
	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[] = $sap_text( "ben_{$i}_rotulo", "Benefício {$i} — rótulo", "sap_ben_{$i}_rotulo" );
		$fields[] = $sap_textarea( "ben_{$i}_desc", "Benefício {$i} — descrição", "sap_ben_{$i}_desc", 2 );
	}

	/* --- 12. EVENTOS AUTOMÁTICOS ----------------------------------------------- */
	$fields[] = $sap_tab( 'automacao', '12 · Automação' );
	$fields[] = $sap_text( 'aut_eyebrow', 'Eyebrow', 'sap_aut_eyebrow' );
	$fields[] = $sap_textarea( 'aut_titulo', 'Título (2 linhas)', 'sap_aut_titulo', 2 );
	$fields[] = $sap_textarea( 'aut_texto', 'Texto de apoio', 'sap_aut_texto', 2 );
	for ( $i = 1; $i <= 5; $i++ ) {
		$fields[] = $sap_text( "aut_{$i}_etapa1", "Coluna {$i} — etapa 1 (trigger)", "sap_aut_{$i}_etapa1" );
		$fields[] = $sap_text( "aut_{$i}_etapa2", "Coluna {$i} — etapa 2", "sap_aut_{$i}_etapa2" );
		$fields[] = $sap_text( "aut_{$i}_etapa3", "Coluna {$i} — etapa 3", "sap_aut_{$i}_etapa3" );
	}

	/* --- 13. FAQ --------------------------------------------------------------- */
	$fields[] = $sap_tab( 'faq', '13 · FAQ' );
	$fields[] = $sap_text( 'faq_eyebrow', 'Eyebrow', 'sap_faq_eyebrow' );
	$fields[] = $sap_text( 'faq_titulo', 'Título', 'sap_faq_titulo' );
	for ( $i = 1; $i <= 3; $i++ ) {
		$fields[] = $sap_text( "faq_{$i}_pergunta", "Pergunta {$i}", "sap_faq_{$i}_pergunta" );
		$fields[] = $sap_textarea( "faq_{$i}_resposta", "Resposta {$i}", "sap_faq_{$i}_resposta", 3 );
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_cli_integracao_sap',
			'title'    => 'Integração SAP — Conteúdo',
			'fields'   => $fields,
			'location' => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'page-integracao-sap.php',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'cliconnect_acf_fields_integracao_sap' );
