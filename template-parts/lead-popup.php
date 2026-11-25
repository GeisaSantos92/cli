<?php
/**
 * Popup de lead do botão flutuante de WhatsApp.
 *
 * Qualquer elemento com `data-lead-popup` abre este <dialog> (theme.js). O
 * formulário é o mesmo da página Contato; o `retURL` dele é a conversa do
 * WhatsApp e o envio sai em nova aba (target="_blank"): o Salesforce grava o
 * lead e redireciona a aba nova para o chat, enquanto a aba do site fica onde
 * está e o popup mostra a confirmação. Sem JS, o link do botão abre o
 * WhatsApp em nova aba.
 *
 * Argumentos: whatsapp — URL da conversa (Customizer cliconnect_whatsapp_url).
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_whatsapp = (string) ( $args['whatsapp'] ?? '' );

if ( ! $cliconnect_whatsapp ) {
	return;
}
?>
<dialog class="lead-popup" id="lead-popup" aria-labelledby="lead-popup-titulo" data-lead-dialog>
	<div class="lead-popup__caixa">
		<button class="lead-popup__fechar" type="button" data-lead-fechar aria-label="<?php esc_attr_e( 'Fechar', 'cli' ); ?>">
			<?php echo cliconnect_icone( 'fechar', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
		</button>

		<div class="lead-popup__cabecalho">
			<span class="lead-popup__icone" aria-hidden="true">
				<?php echo cliconnect_icone( 'whatsapp', 28 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cliconnect_icone retorna SVG estático do tema. ?>
			</span>
			<p class="lead-popup__titulo" id="lead-popup-titulo"><?php esc_html_e( 'Fale com a gente no WhatsApp', 'cli' ); ?></p>
			<p class="lead-popup__texto"><?php esc_html_e( 'Preencha seus dados para iniciar a conversa com o nosso time.', 'cli' ); ?></p>
		</div>

		<?php
		get_template_part(
			'template-parts/form-lead',
			null,
			array(
				'retorno' => $cliconnect_whatsapp,
				'origem'  => 'whatsapp',
				'prefixo' => 'popup-lead',
				'botao'    => __( 'Continuar no WhatsApp', 'cli' ),
				'nova_aba' => true,
			)
		);
		?>

		<div class="form-lead__sucesso" role="status" data-lead-sucesso hidden>
			<p class="form-lead__sucesso-titulo"><?php esc_html_e( 'Tudo certo!', 'cli' ); ?></p>
			<p><?php esc_html_e( 'Abrimos a conversa no WhatsApp em uma nova aba.', 'cli' ); ?></p>
		</div>
	</div>
</dialog>
