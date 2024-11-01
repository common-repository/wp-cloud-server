/**
 * WP Cloud Server - Handling Dismissible Module Notices
 *
 * @Author: 			Gary Jordan (@Designed4Pixels)
 * @Date:   			24-06-2019
 * @Last Modified by:   Gary Jordan (@Designed4Pixels)
 * @Last Modified time: 13-08-2019
 *
*/

jQuery(document).ready( function($) {
	$('.spnotice.is-dismissible').on('click', '.notice-dismiss', function(event){
		// Read the "data-spnotice" information to track which notice
        // is being dismissed and send it via AJAX
        var reference = $( this ).closest( '.wpcs-notice' ).data( 'spnotice' );
		var data = {
		'action': 'module_dismiss',
		'reference': reference
		};
        // Make an AJAX call
        // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post( ajaxurl, data );
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