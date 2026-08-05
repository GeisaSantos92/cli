<?php
/**
 * Template Part: card de resultado de busca (com rótulo do tipo de conteúdo).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_post_type_obj = get_post_type_object( get_post_type() );
$cliconnect_tipo          = $cliconnect_post_type_obj ? $cliconnect_post_type_obj->labels->singular_name : get_post_type();
?>

<article <?php post_class( 'card card--search' ); ?>>
	<div class="card__body">
		<span class="card__badge"><?php echo esc_html( $cliconnect_tipo ); ?></span>

		<h2 class="card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="card__excerpt">
			<?php the_excerpt(); ?>
		</div>
	</div>
</article>
