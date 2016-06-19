/* global i10n_WPTermImages, ajaxurl */
(function($, Models, Collections, Views) {
	$(document).ready(function() {
    'use strict';
	/* Globals */
	var ae_tax_images_modal,
		term_image_working;
	Views.ae_tax = Backbone.View.extend({
		el: 'body',
		model: [],
		events: {
			'click .ae-tax-images-media': 'openImage',
			'click .ae-tax-images-remove': 'removeImage'
		},
		initialize: function () {
		},
		openImage: function(e){
			var view = this;
			e.preventDefault();
			view.data_id = $(e.currentTarget).attr('data-id');
			// Already adding
			if ( term_image_working ) {
				return;
			}

			// Open the modal
			if ( ae_tax_images_modal ) {
				ae_tax_images_modal.open();
				return;
			}

			// First time modal
			ae_tax_images_modal = wp.media.frames.ae_tax_images_modal = wp.media( {
				title:    i10n_WPTermImages.insertMediaTitle,
				button:   { text: i10n_WPTermImages.insertIntoPost },
				library:  { type: 'image' },
				multiple: false
			} );
			var clicked = $( this );
			ae_tax_images_modal.on( 'select', function () {
				// Prevent doubles
				view.term_image_lock( 'lock' );
				// Get the image URL
				var image = ae_tax_images_modal.state().get( 'selection' ).first().toJSON();
				if ( '' !== image ) {
					if ( ! clicked.hasClass( 'quick' ) ) {
						$('#'+view.data_id).val(image.id);
						$( '#'+view.data_id + '_photo' ).attr( 'src', image.url ).show();
						$(  '.'+view.data_id + '_photo_remove'  ).show();
					} else {
						$( 'button.ae-tax-images-media' ).hide();
						$( 'a.button', '.inline-edit-row' ).show();
						$( ':input[name="'+view.data_id+'"]', '.inline-edit-row' ).val( image.id );
						$( 'img.'+view.data_id+', .inline-edit-row' ).attr( 'src', image.url ).show();
					}
				}
				view.term_image_lock( 'unlock' );
			} );

			// Open the modal
			ae_tax_images_modal.open();
		},
		removeImage: function(e){
			var view = this;
			e.preventDefault();
			// Clear image metadata
			view.data_id = $(e.currentTarget).attr('data-id');
			if ( ! $( this ).hasClass( 'quick' ) ) {
				$( '#'+view.data_id ).val( 0 );
				$( '#'+view.data_id+'_photo' ).attr( 'src', '' ).hide();
				$( '.'+ view.data_id+'_photo_remove' ).hide();
			} else {
				$( ':input[name="'+view.data_id+'"]', '.inline-edit-row' ).val( '' );
				$( 'img.'+view.data_id, '.inline-edit-row' ).attr( 'src', '' ).hide();
				$( 'a.button', '.inline-edit-row' ).hide();
				$( 'button.'+view.data_id + '_button' ).show();
			}
		},
		term_image_lock: function( lock_or_unlock ) {
			if ( lock_or_unlock === 'unlock' ) {
				term_image_working = false;
				$( '.ae-tax-images-media' ).prop( 'disabled', false );
			} else {
				term_image_working = true;
				$( '.ae-tax-images-media' ).prop( 'disabled', true );
			}
		}
	});
		new Views.ae_tax();
	/**
	 * Lock the image fieldset
	 *
	 * @param {boolean} lock_or_unlock
	 */

});
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
