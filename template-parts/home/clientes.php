<?php
/**
 * Home — esteira de logos de clientes.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$clientes = cliconnect_posts( 'cli_cliente' );

if ( ! $clientes ) {
	return;
}
?>

<section class="clientes" aria-label="<?php esc_attr_e( 'Clientes que confiam na CLI Connect', 'cli' ); ?>">
	<div class="clientes__pista">
		<div class="clientes__trilha">
			<?php for ( $passada = 0; $passada < 2; $passada++ ) : ?>
				<?php foreach ( $clientes as $cliente ) : ?>
					<span class="clientes__logo" <?php echo $passada ? 'aria-hidden="true"' : ''; ?>>
						<?php
						echo cliconnect_thumb(
							$cliente->ID,
							'medium',
							array( 'alt' => $passada ? '' : get_the_title( $cliente ) )
						);
						?>
					</span>
				<?php endforeach; ?>
			<?php endfor; ?>
		</div>
	</div>
</section>
