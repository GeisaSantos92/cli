<?php
/**
 * CLI Flow — Casos de Uso ("Comece por um processo pronto").
 *
 * Título em duas cores + abas de área (pills) + lista de processos da área à
 * esquerda e **um único player de vídeo** à direita. Cada processo traz no
 * botão as URLs do seu vídeo (WebM e MP4) e da capa; o JS troca a fonte do
 * player ao clicar num processo ou numa aba (que seleciona o primeiro
 * processo dela). Estratégia de desempenho (issue #231):
 *   - <video> nativo, muted + loop + playsinline + preload="none", sem
 *     autoplay; a capa e as fontes só entram quando a seção chega a ~200px
 *     da tela (IntersectionObserver) e só um vídeo é baixado por vez;
 *   - toca ao entrar na tela e pausa ao sair;
 *   - aspect-ratio 16/10 no player reserva o espaço.
 * Processo sem vídeo mostra a janela do CLI Flow em HTML (marca + nome do
 * processo + selo), que também é o conteúdo sem JavaScript.
 * Também é o alvo do botão "Veja como funciona" do hero (#como-funciona).
 *
 * Campos ACF (group_cli_cli_flow, aba "6 · Casos de Uso"):
 *   cf_casos_titulo / cf_casos_titulo_destaque
 *   cf_casos_janela_marca / cf_casos_selo_texto / cf_casos_selo_logo
 *   cf_casos_{1-6}_departamento                 — rótulo da aba
 *   cf_casos_{1-6}_{1-6}_nome / _webm / _mp4 / _capa — processos da aba
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$titulo          = cliconnect_campo_pagina( 'cf_casos_titulo' );
$titulo_destaque = cliconnect_campo_pagina( 'cf_casos_titulo_destaque' );
$marca           = cliconnect_campo_pagina( 'cf_casos_janela_marca' );
$selo_texto      = cliconnect_campo_pagina( 'cf_casos_selo_texto' );
$selo_logo       = (int) ( cliconnect_campo_pagina( 'cf_casos_selo_logo' ) ?? 0 );

$url_anexo = static function ( $id ) {
	$id = (int) $id;
	return $id ? (string) wp_get_attachment_url( $id ) : '';
};

$tabs = array();
for ( $i = 1; $i <= 6; $i++ ) {
	$dept = cliconnect_campo_pagina( "cf_casos_{$i}_departamento" );

	if ( ! $dept ) {
		continue;
	}

	$processos = array();
	for ( $j = 1; $j <= 6; $j++ ) {
		$nome = cliconnect_campo_pagina( "cf_casos_{$i}_{$j}_nome" );
		if ( ! $nome ) {
			continue;
		}
		$processos[] = array(
			'nome' => $nome,
			'webm' => $url_anexo( cliconnect_campo_pagina( "cf_casos_{$i}_{$j}_webm" ) ),
			'mp4'  => $url_anexo( cliconnect_campo_pagina( "cf_casos_{$i}_{$j}_mp4" ) ),
			'capa' => $url_anexo( cliconnect_campo_pagina( "cf_casos_{$i}_{$j}_capa" ) ),
		);
	}

	if ( $processos ) {
		$tabs[] = array(
			'indice'       => $i,
			'departamento' => $dept,
			'processos'    => $processos,
		);
	}
}

if ( empty( $tabs ) ) {
	return;
}

$primeiro = $tabs[0]['processos'][0];
?>
<section class="cf-casos" id="como-funciona" data-cf-casos>
	<div class="container">

		<?php if ( $titulo || $titulo_destaque ) : ?>
			<h2 class="cf-casos__titulo">
				<?php if ( $titulo ) : ?>
					<span class="cf-casos__titulo-linha"><?php echo esc_html( $titulo ); ?></span>
				<?php endif; ?>
				<?php if ( $titulo_destaque ) : ?>
					<span class="cf-casos__titulo-linha cf-casos__titulo-linha--destaque"><?php echo esc_html( $titulo_destaque ); ?></span>
				<?php endif; ?>
			</h2>
		<?php endif; ?>

		<?php /* Pills de área ----------------------------------------------- */ ?>
		<div class="cf-casos__nav" role="tablist" aria-label="<?php esc_attr_e( 'Departamentos', 'cli' ); ?>">
			<?php foreach ( $tabs as $index => $tab ) : ?>
				<button
					class="cf-casos__tab<?php echo 0 === $index ? ' ativo' : ''; ?>"
					type="button"
					role="tab"
					aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
					aria-controls="cf-caso-painel-<?php echo esc_attr( (string) $tab['indice'] ); ?>"
					id="cf-caso-aba-<?php echo esc_attr( (string) $tab['indice'] ); ?>"
					data-cf-caso-tab="<?php echo esc_attr( (string) $tab['indice'] ); ?>"
				>
					<?php echo esc_html( $tab['departamento'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="cf-casos__corpo">

			<?php /* Processos de cada área ----------------------------------- */ ?>
			<div class="cf-casos__listas">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<ul
						class="cf-casos__painel cf-casos__lista<?php echo 0 === $index ? ' ativo' : ''; ?>"
						role="tabpanel"
						id="cf-caso-painel-<?php echo esc_attr( (string) $tab['indice'] ); ?>"
						aria-labelledby="cf-caso-aba-<?php echo esc_attr( (string) $tab['indice'] ); ?>"
						data-cf-panel="<?php echo esc_attr( (string) $tab['indice'] ); ?>"
					>
						<?php foreach ( $tab['processos'] as $n => $processo ) : ?>
							<li>
								<button
									class="cf-casos__item<?php echo ( 0 === $index && 0 === $n ) ? ' ativo' : ''; ?>"
									type="button"
									aria-pressed="<?php echo ( 0 === $index && 0 === $n ) ? 'true' : 'false'; ?>"
									data-cf-caso-item
									data-webm="<?php echo esc_url( $processo['webm'] ); ?>"
									data-mp4="<?php echo esc_url( $processo['mp4'] ); ?>"
									data-capa="<?php echo esc_url( $processo['capa'] ); ?>"
								><?php echo esc_html( $processo['nome'] ); ?></button>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
			</div>

			<?php /* Player único -------------------------------------------- */ ?>
			<?php /* Começa como "sem-video": o JS só põe a fonte perto da tela. */ ?>
			<div class="cf-casos__player sem-video" data-cf-player>
				<video
					class="cf-casos__video"
					data-cf-video
					muted
					loop
					playsinline
					preload="none"
					disablepictureinpicture
					aria-label="<?php echo esc_attr( $primeiro['nome'] ); ?>"
				></video>

				<?php /* Reserva: processo sem vídeo (e conteúdo sem JS). */ ?>
				<div class="cf-casos__janela" aria-hidden="true">
					<div class="cf-casos__janela-topo">
						<span class="cf-casos__janela-marca">
							<?php echo cliconnect_imagem_tema( 'cf-logo-cli.svg', array( 'class' => 'cf-casos__janela-simbolo', 'alt' => 'CLI', 'width' => 82, 'height' => 21 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapa os atributos. ?>
							<?php if ( $marca ) : ?>
								<span class="cf-casos__janela-marca-texto"><?php echo esc_html( $marca ); ?></span>
							<?php endif; ?>
						</span>

						<p class="cf-casos__janela-titulo" data-cf-caso-titulo><?php echo esc_html( $primeiro['nome'] ); ?></p>

						<?php if ( $selo_texto ) : ?>
							<span class="cf-casos__selo">
								<?php if ( $selo_logo ) : ?>
									<?php echo wp_get_attachment_image( $selo_logo, 'medium', false, array( 'class' => 'cf-casos__selo-logo', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapa internamente. ?>
								<?php endif; ?>
								<?php echo esc_html( $selo_texto ); ?>
							</span>
						<?php endif; ?>
					</div>
					<div class="cf-casos__janela-corpo"></div>
				</div>
			</div>

		</div>

	</div>
</section>
