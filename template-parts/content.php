<?php
/**
 * Template Part: card padrão de listagem (fallback para qualquer post type).
 *
 * Usado por index.php/archive.php via
 * get_template_part( 'template-parts/content', get_post_type() ) — crie
 * content-{post_type}.php para variar por tipo.
 *
 * Este componente lê o loop global de propósito (é o card genérico de
 * archive). Componentes reutilizáveis fora do loop devem receber dados por
 * $args — ver docs/code-standards.md.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article <?php post_class( 'card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
	<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php the_post_thumbnail( 'medium_large' ); ?>
	</a>
	<?php endif; ?>

	<div class="card__body">
		<h2 class="card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<p class="card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		</p>

		<div class="card__excerpt">
			<?php the_excerpt(); ?>
		</div>
	</div>
</article>
