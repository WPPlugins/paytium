/**
 * Paytium Admin JS
 *
 * @package PT
 * @author  David de Boer <david@davdeb.com>
 */

/* global jQuery, sc_script */
(function ($) {
	'use strict';

	// Set debug flag.
	var script_debug = ( (typeof sc_script != 'undefined' ) && sc_script.script_debug == true);

	$(function () {

		if ( script_debug ) {
			console.log( 'sc_script', sc_script );
		}

		var $body = $( document.body );
		var $this = '';


		// Tab click
		$( '#pt-setup-wizard' ).on( 'click', '.tabs a, .tab-button', function() {

			if ( $( this ).data( 'target' ) !== undefined ) {

				// Tabs
				var tabs = $( '#pt-setup-wizard' ).find( '.tabs' );
				tabs.find( 'li' ).removeClass( 'active' );
				tabs.find( '[data-target=' + $( this).data( 'target' ) + ']' ).parent( 'li' ).addClass( 'active' );

				// Panel
				var panels = tabs.parent().find( '.panels' );
				panels.find( '.panel' ).removeClass( 'active' ).hide();
				panels.find( '.panel#' + $( this ).data( 'target' ) ).addClass( 'active' ).show();

			}

            // If not Mollie username or password are stored, sent user to connect-mollie.php
            console.log( 'test');


		});

		// Target button/area to show/hide areas
		$( '#pt-setup-wizard' ).on( 'click', '.target-button', function() {

			if ( $( this ).data( 'target' ) !== undefined ) {

				var target = $( this ).data( 'target' );
				$( '.target-area:not(#' + target + ')' ).removeClass( 'active' ).slideUp();
				$( '.target-area#' + target ).addClass( 'active' ).slideDown();

			}

		});

		/**************************************************************
		 * Connect Mollie account
		 *************************************************************/

		// Login data
		$body.on( 'click', '#login-mollie', function() {

			$( this ).next( '.spinner' ).addClass( 'is-active' );

			$this = $( this );
			var data = {
				action: 'paytium_mollie_login_data',
				form: $( this ).closest( 'form' ).serialize(),
				nonce: paytium.nonce,
			};
			$.post( ajaxurl, data, function( response ) {

				response = JSON.parse( response );
				if ( undefined !== response.message ) {
					$this.parent( 'form' ).prev( '.ajax-response' ).html( response.message );
				}

				if ( response.status == 'success' ) {
					$this.parents( 'form' ).slideUp();
					$this.parents( 'form' ).next( '.continue-button' ).slideDown();
				}

				$this.next( '.spinner' ).removeClass( 'is-active' );
			});

		});

		// Create account
		$body.on( 'click', '#create-mollie-account', function() {

			$( this ).next( '.spinner' ).addClass( 'is-active' );

			$this = $( this );
			var data = {
				action: 'paytium_mollie_create_account',
				form: $( this ).closest( 'form' ).serialize(),
				nonce: paytium.nonce,
			};
			$.post( ajaxurl, data, function( response ) {

				response = JSON.parse( response );
				if ( undefined !== response.message ) {
					$this.parent( 'form' ).prev( '.ajax-response' ).html( response.message );
				}

				if ( response.status == 'success' ) {
					$this.parents( 'form' ).slideUp();
					$this.parents( 'form' ).next( '.continue-button' ).slideDown();
				}

				$this.next( '.spinner' ).removeClass( 'is-active' );
			});

		});

		/**************************************************************
		 * Website profile
		 *************************************************************/

		// Create profile
		$body.on( 'click', '#create-mollie-profile', function() {

			$( this ).next( '.spinner' ).addClass( 'is-active' );

			$this = $( this );
			var data = {
				action: 'paytium_mollie_create_profile',
				form: $( this ).closest( 'form' ).serialize(),
				nonce: paytium.nonce,
			};
			$.post( ajaxurl, data, function( response ) {

				response = JSON.parse( response );
				if ( undefined !== response.message ) {
					$this.parent( 'form' ).prev( '.ajax-response' ).html( response.message );
				}

				if ( response.status == 'success' ) {
					$this.parents( 'form' ).slideUp();
					$this.parents( 'form' ).next( '.continue-button' ).slideDown();
				}

				$this.next( '.spinner' ).removeClass( 'is-active' );

			});

		});

		/**************************************************************
		 * First product
		 *************************************************************/

        // Change button text to "Done? Continue", disabled because this is now the last step in the wizard

		// Continue click on first product
		//var second_click = false;
		//$( '#pt-setup-wizard' ).on( 'click', '#create-product', function() {


			//$( this ).html( 'Done? Continue' );
			//if ( second_click ) {
			//	$( this ).attr( 'href', 'javascript:void(0);' ).attr( 'data-target', 'payment-test' ).addClass( 'tab-button' );
			//	$( this ).trigger( 'click' );
			//	return e;
			//}

			// Make sure we know its the second click
		//	second_click = true;
        //
		//	return e;
        //
		//});

		/**************************************************************
		 * Payment test
		 *************************************************************/

		$body.on( 'click', '#check-payment', function() {

			$( '#payment-test' ).find( '.spinner-wrap' ).fadeIn();

			$this = $( this );
			var data = {
				action: 'paytium_check_payment_exists',
				nonce: paytium.nonce,
			};
			$.post( ajaxurl, data, function( response ) {

				response = JSON.parse( response );
				if ( undefined !== response.message ) {
					$( '#payment-test' ).find( '.ajax-response' ).html( response.message );
				}

				if ( response.status == 'success' ) {
					$( '#payment-test' ).find( '.spinner-wrap' ).fadeOut();
					$this.parents( 'form' ).next( '.continue-button' ).slideDown();
				}

			});

		});

		/**************************************************************
		 * Activate account
		 *************************************************************/

		// Check profile status
		$body.on( 'click', '[data-target="activate-account"], #check-profile-status', function() {

			$( '#profile-not-verified, #profile-verified' ).fadeOut();
			$( '.spinner-wrap' ).fadeIn();

			$this = $( this );
			var data = {
				action: 'paytium_mollie_check_profile_status',
				nonce: paytium.nonce,
			};
			$.post( ajaxurl, data, function( response ) {

				if ( 'verified' == response ) {
					$( '#profile-not-verified').fadeOut();
					$('#profile-verified').fadeIn();
				} else {
					$( '#profile-verified').fadeOut();
					$( '#profile-not-verified').fadeIn();
				}

				$( '.spinner-wrap' ).hide();

			});

		});

		// Activate live orders
		$body.on( 'click', '#activate-account-pt', function() {

			$this = $( this );
			var data = {
				action: 'paytium_mollie_activate_live_orders',
				nonce: paytium.nonce,
			};
			$.post( ajaxurl, data, function( response ) {

			});

		});


	});

}(jQuery));
