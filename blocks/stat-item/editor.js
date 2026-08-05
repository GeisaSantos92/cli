/**
 * cliconnect/stat-item — editor (JS puro, sem build; ver inc/blocks.php).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var RichText = wp.blockEditor.RichText;

	wp.blocks.registerBlockType( 'cliconnect/stat-item', {
		edit: function ( props ) {
			var atts = props.attributes;
			var set = props.setAttributes;
			var blockProps = useBlockProps( { className: 'cliconnect-bl-stat' } );

			return el(
				'div',
				blockProps,
				el( RichText, {
					tagName: 'span',
					className: 'cliconnect-bl-stat-numero',
					value: atts.numero,
					allowedFormats: [],
					placeholder: '+30',
					onChange: function ( v ) {
						set( { numero: v } );
					},
				} ),
				el( RichText, {
					tagName: 'span',
					className: 'cliconnect-bl-stat-rotulo',
					value: atts.rotulo,
					allowedFormats: [],
					placeholder: __( 'Rótulo do indicador', 'cli' ),
					onChange: function ( v ) {
						set( { rotulo: v } );
					},
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
