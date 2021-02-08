// This is just here to make sure that I'm putting the directory into the right place.
const iconsBtn = document.getElementById( 'menu_icon-btn' );
const iconsModal = document.getElementById( 'iconsModal' );

( function ( $ ) {
	$( iconsBtn ).click( function ( e ) {
		e.preventDefault();
		$( iconsModal ).modal( {
			backdrop: 'static',
			focus: true,
		}, 'show' );
	} );
} )( jQuery );
