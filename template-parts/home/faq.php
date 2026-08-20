<?php
/**
 * Home — Dúvidas frequentes (acordeão acessível).
 *
 * Perguntas do CPT cli_faq: título = pergunta, conteúdo = resposta.
 * O comportamento de abrir/fechar está em assets/js/theme.js.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$perguntas = get_field( 'faq_itens', (int) get_option( 'page_on_front' ) );

if ( ! $perguntas ) {
	return;
}

$eyebrow = cliconnect_campo( 'faq_eyebrow' );
$titulo  = cliconnect_campo( 'faq_titulo' );
?>

<section class="secao faq">
	<div class="container">

		<header class="secao__cabecalho">
			<?php if ( $eyebrow ) : ?>
				<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $titulo ) : ?>
				<h2 class="secao__titulo"><?php echo esc_html( $titulo ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="faq__lista">
			<?php foreach ( $perguntas as $indice => $pergunta ) : ?>
				<?php
				$id_resposta = 'faq-resposta-' . $pergunta->ID;
				$resposta    = apply_filters( 'the_content', $pergunta->post_content );
				?>
				<div class="faq__item">
					<h3>
						<button
							class="faq__gatilho"
							type="button"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $id_resposta ); ?>"
							data-faq-gatilho
						>
							<span><?php echo esc_html( get_the_title( $pergunta ) ); ?></span>
							<?php echo cliconnect_icone( 'chevron-baixo', 20 ); // SVG estático. ?>
						</button>
					</h3>

					<div class="faq__resposta" id="<?php echo esc_attr( $id_resposta ); ?>" hidden>
						<?php echo wp_kses_post( $resposta ); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
