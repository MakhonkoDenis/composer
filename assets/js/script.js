/**
 * cherry-composer
 */
( function( $ ){
	"use strict";
	var composer;

	composer = {

		target : $( '#cherry-composer' ),

		ajaxRequest : null,

		init: function () {
			this.addEvents( this.target );

			jQuery.ajaxSetup( {
				type: 'POST',
				url: $( 'button', this.target ).attr('formaction'),
				cache: false
			} );
		},
		addEvents: function ( target ) {
			$('#download-theme', target).on( 'click', this.aJaxRequest );
		},
		aJaxRequest: function( event ) {
			var button = $( this ),
				buttonText = button.text(),
				formData = false,
				validData = '';

			$('#message', composer.target ).css( 'visibility', 'hidden' );

			if ( null !== composer.ajaxRequest ) {
				composer.ajaxRequest.abort();
			}

			formData = composer.getFormData( $(composer.target) );
			validData = composer.validateData( formData );

			if( validData.validate ){
				composer.ajaxRequest = jQuery.ajax( {
					data: {
						form_data: formData
					},
					beforeSend: function() {
						button.attr('disabled', 'disabled' );
					},
					success: function( response ) {
						var parsedResponse = response.toLowerCase();

						if( parsedResponse.indexOf("error") !== -1 || parsedResponse.indexOf("warning") !== -1){
							composer.errorNotice( response );
						}else{
							window.location.href += response;
							button.removeAttr('disabled');
						}
					},
					error: function( response ) {
						composer.errorNotice( response );
						button.removeAttr('disabled');
					}
				} );
			}else{
				composer.errorNotice( 'ERROR: Invalid filled' );
			}

			event.preventDefault();
			event.stopPropagation();
			event.stopImmediatePropagation();
		},
		getFormData: function( target ) {
			var data = {},
				formData = target.serializeArray();

			$( formData ).each( function( index, field ) {
				data[ field.name ] = field.value;
			})


			return data;
		},
		validateData: function( data ) {
			var validData = {
					'validate' : true,
					'inValidateField' : []
				},
				field;

			for ( field in data ) {
				if( ! data[ field ] || ! data[ 'branch_name' ]){
					validData.validate = false;
					validData.inValidateField[field] = 'error';
				}
			}
			return validData;
		},
		errorNotice: function( message ) {
			$('#message', composer.target).css( 'visibility', 'visible' ).text( message );
		}
	}
	composer.init();
}( jQuery ) )