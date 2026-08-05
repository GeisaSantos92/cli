/**
 * cliconnect/hero — editor (JS puro, sem build; ver inc/blocks.php).
 *
 * Edição inline via RichText; imagem de fundo via MediaUpload no painel
 * lateral. As classes visuais são as mesmas do render.php (blocks.css).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var PanelBody = wp.components.PanelBody;
	var Button = wp.components.Button;

	wp.blocks.registerBlockType( 'cliconnect/hero', {
		edit: function ( props ) {
			var atts = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps( {
				className: 'cliconnect-bl-hero cliconnect-bl-hero--editor',
			} );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Imagem de fundo', 'cli' ), initialOpen: true },
						el(
							MediaUploadCheck,
							null,
							el( MediaUpload, {
								onSelect: function ( media ) {
									set( { imagemId: media.id, imagemUrl: media.url } );
								},
								allowedTypes: [ 'image' ],
								value: atts.imagemId,
								render: function ( ctl ) {
									return el(
										Button,
										{ variant: 'secondary', onClick: ctl.open },
										atts.imagemId
											? __( 'Trocar imagem', 'cli' )
											: __( 'Escolher imagem', 'cli' )
									);
								},
							} )
						),
						atts.imagemId
							? el(
									Button,
									{
										variant: 'link',
										isDestructive: true,
										onClick: function () {
											set( { imagemId: 0, imagemUrl: '' } );
										},
									},
									__( 'Remover imagem', 'cli' )
							  )
							: null
					)
				),
				el(
					'section',
					blockProps,
					el( 'div', {
						className: 'cliconnect-bl-hero-bg',
						style: atts.imagemUrl
							? { backgroundImage: 'url(' + atts.imagemUrl + ')' }
							: null,
						'aria-hidden': 'true',
					} ),
					el( 'div', { className: 'cliconnect-bl-hero-overlay', 'aria-hidden': 'true' } ),
					el(
						'div',
						{ className: 'cliconnect-bl-container cliconnect-bl-hero-conteudo' },
						el(
							'div',
							null,
							el( RichText, {
								tagName: 'span',
								className: 'cliconnect-bl-kicker cliconnect-bl-kicker--claro',
								value: atts.badge,
								allowedFormats: [],
								placeholder: __( 'Badge (ex.: Campanha)', 'cli' ),
								onChange: function ( v ) {
									set( { badge: v } );
								},
							} )
						),
						el( RichText, {
							tagName: 'h1',
							className: 'cliconnect-bl-hero-titulo',
							value: atts.titulo,
							allowedFormats: [],
							placeholder: __( 'Título da landing', 'cli' ),
							onChange: function ( v ) {
								set( { titulo: v } );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'cliconnect-bl-hero-subtitulo',
							value: atts.subtitulo,
							allowedFormats: [],
							placeholder: __( 'Subtítulo (opcional)', 'cli' ),
							onChange: function ( v ) {
								set( { subtitulo: v } );
							},
						} )
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
