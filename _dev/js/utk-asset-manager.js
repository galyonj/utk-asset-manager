// This is just here to make sure that I'm putting the directory into the right place.
const iconsBtn = document.getElementById( 'menu_icon-btn' );
const iconsModal = document.getElementById( 'iconsModal' );

( function ( $ ) {
	iconsBtn.addEventListener( 'click', ( e ) => {
		$( iconsModal ).modal( 'show' );
	} );
} )( jQuery );
