/**
 * WP Cloud Server - Handling Dismissible Module Notices
 *
 * @link       https://designedforpixels.com
 * @since      1.0.0
 *
 * @package		WP_Cloud_Server
 * @Author		Gary Jordan (gary@designedforpixels.com)
 */

jQuery( document ).ready( function($){
    "use strict";

	$('.spnotice.is-dismissible').on('click', '.notice-dismiss', function( event ){
		// Read the "data-spnotice" information to track which notice
		// is being dismissed and send it via AJAX
        var type = $( this ).closest( '.wpcs-notice' ).data( 'spnotice' );
		var data = {
			'action': 'module_dismiss',
			'module_nonce': wpcs_module_ajax_script.ajax_module_nonce,
			'module_type': type
		};
        // Make an AJAX call
        $.post( wpcs_module_ajax_script.ajaxurl, data );
		});

	$( '.spnotice.is-dismissible' ).each( function() {
			var $el = $( this ),
				$button = $( '<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>' ),
				btnText = commonL10n.dismiss || '';

			// Ensure plain text
			$button.find( '.screen-reader-text' ).text( btnText );
			$button.on( 'click.wp-dismiss-notice', function( event ) {
				event.preventDefault();
				$el.fadeTo( 100, 0, function() {
					$el.slideUp( 100, function() {
						$el.remove();
					});
				});
			});

			$el.append( $button );
		});
});