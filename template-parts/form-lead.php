<?php
/**
 * Formulário de lead — Web-to-Lead do Salesforce.
 *
 * Mesmo formulário na página Contato e no popup do botão flutuante de
 * WhatsApp. O envio é o POST nativo para o Salesforce; as UTMs entram pelo
 * script assets/js/utm-web-to-lead.js, que identifica o formulário pelo
 * `action` (WebToLead). Ver inc/lead.php e docs/lead-salesforce.md.
 *
 * Campos do Salesforce: last_name, phone, email, company, description e o
 * oculto lead_source. `last_name` é o único nome obrigatório do Lead, então o
 * nome completo vai nele.
 *
 * Argumentos (get_template_part( ..., null, $args )):
 *   retorno — URL absoluta para onde o Salesforce devolve o visitante (retURL).
 *   origem  — rótulo gravado no bloco "Origem do lead" da Descrição.
 *   prefixo — prefixo dos IDs dos campos (único por formulário na página).
 *   botao   — texto do botão de envio.
 *   nova_aba — envia em nova aba (o Salesforce redireciona ESSA aba para o
 *              retURL); a aba do site fica onde está. Usado no popup do
 *              WhatsApp, para a conversa abrir em nova aba.
 *
 * @package Cliconnect
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cliconnect_args    = wp_parse_args(
	$args ?? array(),
	array(
		'retorno'  => '',
		'origem'   => 'contato',
		'prefixo'  => 'lead',
		'botao'    => __( 'Enviar', 'cli' ),
		'nova_aba' => false,
	)
);
$cliconnect_org     = cliconnect_lead_org_id();
$cliconnect_prefixo = sanitize_html_class( $cliconnect_args['prefixo'] );
$cliconnect_action  = add_query_arg( 'orgId', $cliconnect_org, CLICONNECT_SF_ENDPOINT );
$cliconnect_id      = static function ( $campo ) use ( $cliconnect_prefixo ) {
	return $cliconnect_prefixo . '-' . $campo;
};
?>
<form
	class="form-lead"
	action="<?php echo esc_url( $cliconnect_action ); ?>"
	method="POST"
	<?php if ( $cliconnect_args['nova_aba'] ) : ?>
	target="_blank"
	<?php endif; ?>
	data-lead-origem="<?php echo esc_attr( $cliconnect_args['origem'] ); ?>"
>
	<input type="hidden" name="oid" value="<?php echo esc_attr( $cliconnect_org ); ?>">
	<input type="hidden" name="retURL" value="<?php echo esc_url( $cliconnect_args['retorno'] ); ?>">
	<input type="hidden" name="lead_source" value="Web">

	<div class="form-lead__campo">
		<label for="<?php echo esc_attr( $cliconnect_id( 'nome' ) ); ?>"><?php esc_html_e( 'Nome', 'cli' ); ?></label>
		<input id="<?php echo esc_attr( $cliconnect_id( 'nome' ) ); ?>" name="last_name" type="text" maxlength="80" autocomplete="name" required placeholder="<?php esc_attr_e( 'Nome', 'cli' ); ?>">
	</div>

	<div class="form-lead__campo">
		<label for="<?php echo esc_attr( $cliconnect_id( 'telefone' ) ); ?>"><?php esc_html_e( 'Telefone', 'cli' ); ?></label>
		<input id="<?php echo esc_attr( $cliconnect_id( 'telefone' ) ); ?>" name="phone" type="tel" maxlength="40" autocomplete="tel" required placeholder="<?php esc_attr_e( '(00) 00000-0000', 'cli' ); ?>">
	</div>

	<div class="form-lead__campo">
		<label for="<?php echo esc_attr( $cliconnect_id( 'email' ) ); ?>"><?php esc_html_e( 'E-mail', 'cli' ); ?></label>
		<input id="<?php echo esc_attr( $cliconnect_id( 'email' ) ); ?>" name="email" type="email" maxlength="80" autocomplete="email" required placeholder="<?php esc_attr_e( 'E-mail', 'cli' ); ?>">
	</div>

	<div class="form-lead__campo">
		<label for="<?php echo esc_attr( $cliconnect_id( 'empresa' ) ); ?>"><?php esc_html_e( 'Empresa', 'cli' ); ?></label>
		<input id="<?php echo esc_attr( $cliconnect_id( 'empresa' ) ); ?>" name="company" type="text" maxlength="40" autocomplete="organization" required placeholder="<?php esc_attr_e( 'Empresa', 'cli' ); ?>">
	</div>

	<div class="form-lead__campo">
		<label for="<?php echo esc_attr( $cliconnect_id( 'mensagem' ) ); ?>"><?php esc_html_e( 'Mensagem', 'cli' ); ?></label>
		<textarea id="<?php echo esc_attr( $cliconnect_id( 'mensagem' ) ); ?>" name="description" rows="4" placeholder="<?php esc_attr_e( 'Digite sua mensagem', 'cli' ); ?>"></textarea>
	</div>

	<?php // Armadilha para robôs: invisível para pessoas; o theme.js barra o envio se vier preenchida e a desativa antes do POST. ?>
	<div class="form-lead__armadilha" aria-hidden="true">
		<label for="<?php echo esc_attr( $cliconnect_id( 'site' ) ); ?>">Website</label>
		<input id="<?php echo esc_attr( $cliconnect_id( 'site' ) ); ?>" name="website" type="text" tabindex="-1" autocomplete="off" data-lead-armadilha>
	</div>

	<label class="form-lead__aceite">
		<input type="checkbox" required>
		<span><?php esc_html_e( 'Ao enviar, concordo em receber comunicações da CLI', 'cli' ); ?></span>
	</label>

	<button class="form-lead__enviar" type="submit">
		<?php echo esc_html( $cliconnect_args['botao'] ); ?>
	</button>
</form>
