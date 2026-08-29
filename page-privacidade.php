<?php
/**
 * Template Name: Política de Privacidade
 *
 * Página institucional de texto longo. Sem Figma: usa o design system do tema
 * numa coluna de leitura, no espírito da matéria do blog.
 * Zero texto fixo: todo conteúdo vem do grupo ACF `group_cli_privacidade`.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$cliconnect_secoes = array(
	'cabecalho',
	'corpo',
);
?>

<main id="primary" class="site-privacidade">
	<div class="pv-pagina">
		<div class="container">
			<div class="pv-coluna">
				<?php
				foreach ( $cliconnect_secoes as $cliconnect_secao ) {
					get_template_part( 'template-parts/privacidade/' . $cliconnect_secao );
				}
				?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
