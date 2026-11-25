<?php
/**
 * CLI Flow — Como funciona ("Qualquer tela, dashboard ou processo").
 *
 * Cabeçalho centralizado (eyebrow + título em duas cores + texto) e um painel
 * azul-claro com a demonstração: vídeo (.mp4) ou imagem, quando houver; sem
 * mídia, um cartão de tela com o cursor, como no quadro do Figma. Na borda de
 * baixo do painel, três pílulas conectadas mostram o caminho do dado.
 *
 * Campos ACF (group_cli_cli_flow, aba "12 · Como funciona"):
 *   cf_dash_eyebrow, cf_dash_titulo, cf_dash_titulo_destaque, cf_dash_texto
 *   cf_dash_video_webm / cf_dash_video / cf_dash_capa — demonstração em vídeo
 *     (WebM + MP4 de fallback + capa; tem prioridade sobre a imagem)
 *   cf_dash_imagem — demonstração em imagem
 *   cf_dash_card_titulo — título do cartão de tela (sem mídia)
 *   cf_dash_etapa_{1-3}_destaque / _texto — pílulas (parte em negrito + resto)
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow   = cliconnect_campo_pagina( 'cf_dash_eyebrow' );
$titulo    = cliconnect_campo_pagina( 'cf_dash_titulo' );
$destaque  = cliconnect_campo_pagina( 'cf_dash_titulo_destaque' );
$texto     = cliconnect_campo_pagina( 'cf_dash_texto' );
$video_mp4  = (int) ( cliconnect_campo_pagina( 'cf_dash_video' ) ?? 0 );
$video_webm = (int) ( cliconnect_campo_pagina( 'cf_dash_video_webm' ) ?? 0 );
$video_capa = (int) ( cliconnect_campo_pagina( 'cf_dash_capa' ) ?? 0 );
$tem_video  = $video_mp4 || $video_webm;
$imagem_id = (int) ( cliconnect_campo_pagina( 'cf_dash_imagem' ) ?? 0 );
$card      = cliconnect_campo_pagina( 'cf_dash_card_titulo' );

if ( ! $titulo && ! $destaque ) {
	return;
}

$etapas = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$etapa_destaque = cliconnect_campo_pagina( "cf_dash_etapa_{$i}_destaque" );
	$etapa_texto    = cliconnect_campo_pagina( "cf_dash_etapa_{$i}_texto" );
	if ( $etapa_destaque || $etapa_texto ) {
		$etapas[] = array(
			'destaque' => $etapa_destaque,
			'texto'    => $etapa_texto,
		);
	}
}
?>
<section class="cf-dashboard">
	<div class="container">

		<header class="cf-dashboard__header">
			<?php if ( $eyebrow ) : ?>
				<p class="cf-dashboard__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<h2 class="cf-dashboard__titulo">
				<?php if ( $titulo ) : ?>
					<span class="cf-dashboard__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
				<?php endif; ?>
				<?php if ( $destaque ) : ?>
					<span class="cf-dashboard__titulo-linha cf-dashboard__titulo-linha--destaque"><?php echo esc_html( $destaque ); ?></span>
				<?php endif; ?>
			</h2>
			<?php if ( $texto ) : ?>
				<p class="cf-dashboard__texto"><?php echo esc_html( $texto ); ?></p>
			<?php endif; ?>
		</header>

		<div class="cf-dashboard__painel<?php echo $tem_video ? ' cf-dashboard__painel--video' : ''; ?>">
			<?php if ( $tem_video ) : ?>
				<?php /* Sem autoplay: o JS toca perto da tela e pausa fora dela. */ ?>
				<video
					class="cf-dashboard__midia"
					data-cf-video-lazy
					muted
					loop
					playsinline
					preload="none"
					disablepictureinpicture
					<?php if ( $video_capa ) : ?>
						poster="<?php echo esc_url( (string) wp_get_attachment_url( $video_capa ) ); ?>"
					<?php endif; ?>
				>
					<?php if ( $video_webm ) : ?>
						<source src="<?php echo esc_url( (string) wp_get_attachment_url( $video_webm ) ); ?>" type="video/webm">
					<?php endif; ?>
					<?php if ( $video_mp4 ) : ?>
						<source src="<?php echo esc_url( (string) wp_get_attachment_url( $video_mp4 ) ); ?>" type="video/mp4">
					<?php endif; ?>
				</video>
			<?php elseif ( $imagem_id ) : ?>
				<?php echo wp_get_attachment_image( $imagem_id, 'full', false, array( 'class' => 'cf-dashboard__midia', 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
			<?php else : ?>
				<div class="cf-dashboard__tela" aria-hidden="true">
					<?php if ( $card ) : ?>
						<p class="cf-dashboard__tela-titulo"><?php echo esc_html( $card ); ?></p>
					<?php endif; ?>
				</div>
				<span class="cf-dashboard__cursor" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="44" height="44" focusable="false"><path d="M4.5 3.2 20 10.4l-6.6 2.2-2.6 6.9z" fill="currentColor" stroke="#fff" stroke-width="1.2" stroke-linejoin="round"/></svg>
				</span>
			<?php endif; ?>

			<?php if ( $etapas ) : ?>
				<ol class="cf-dashboard__etapas" role="list">
					<?php foreach ( $etapas as $etapa ) : ?>
						<li class="cf-dashboard__etapa">
							<?php if ( $etapa['destaque'] ) : ?>
								<strong><?php echo esc_html( $etapa['destaque'] ); ?></strong>
							<?php endif; ?>
							<?php if ( $etapa['destaque'] && $etapa['texto'] ) : ?>
								<span aria-hidden="true">·</span>
							<?php endif; ?>
							<?php if ( $etapa['texto'] ) : ?>
								<span><?php echo esc_html( $etapa['texto'] ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>

	</div>
</section>
