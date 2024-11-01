jQuery( document ).ready( function($){
    "use strict";

	$( function() {
		UIkit.util.on('#js-modal-dialog', 'click', function (e) {
           var modal = UIkit.modal("#managed-server-modal");
					modal.show();
       });
	});
});




