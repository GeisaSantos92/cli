/**
 * cliconnect/stats — editor (JS puro, sem build; ver inc/blocks.php).
 *
 * Itens repetíveis via InnerBlocks restrito a cliconnect/stat-item: adicionar,
 * remover e reordenar usam a UI nativa do Gutenberg.
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var useInnerBlocksProps = wp.blockEditor.useInnerBlocksProps;
	var InnerBlocks = wp.blockEditor.InnerBlocks;
	var RichText = wp.blockEditor.RichText;

	wp.blocks.registerBlockType( 'cliconnect/stats', {
		edit: function ( props ) {
			var atts = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps( { className: 'cliconnect-bl-stats' } );
			var innerProps = useInnerBlocksProps(
				{ className: 'cliconnect-bl-stats-grid' },
				{
					allowedBlocks: [ 'cliconnect/stat-item' ],
					template: [
						[ 'cliconnect/stat-item' ],
						[ 'cliconnect/stat-item' ],
						[ 'cliconnect/stat-item' ],
						[ 'cliconnect/stat-item' ],
					],
				}
			);

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: 'cliconnect-bl-container' },
					el(
						'div',
						{ className: 'cliconnect-bl-stats-header' },
						el(
							'div',
							null,
							el( RichText, {
								tagName: 'span',
								className: 'cliconnect-bl-kicker cliconnect-bl-kicker--claro',
								value: atts.badge,
								allowedFormats: [],
								placeholder: __( 'Badge (ex.: Números)', 'cli' ),
								onChange: function ( v ) {
									set( { badge: v } );
								},
							} )
						),
						el( RichText, {
							tagName: 'h2',
							className: 'cliconnect-bl-stats-titulo',
							value: atts.titulo,
							allowedFormats: [],
							placeholder: __( 'Título da faixa de números', 'cli' ),
							onChange: function ( v ) {
								set( { titulo: v } );
							},
						} )
					),
					el( 'div', innerProps )
				)
			);
		},
		save: function () {
			return el( InnerBlocks.Content );
		},
	} );
} )( window.wp );
