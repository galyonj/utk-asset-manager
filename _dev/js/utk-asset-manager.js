// This is just here to make sure that I'm putting the directory into the right place.
const iconsBtn = document.getElementById( 'menu_icon-btn' );
const iconsModal = document.getElementById( 'iconsModal' );
const icons = document.querySelectorAll( '.icons-list li svg' );

( function ( $ ) {
	$( iconsBtn ).click( function ( e ) {
		e.preventDefault();
		$( iconsModal ).modal(
			{
				focus: true,
			},
			'show'
		);
	} );

	$( document ).ready( function () {
		console.log( 'Hello!' );
	} );

	$( '.selected-icon' ).hide();

	// Listen for click on one of the icons
	$( icons ).click( function () {
		console.log( 'Click on ' + this.parentNode.id );

		// Make sure that the 'selected' class is removed from all of the list items
		$( '.icons-list' ).children( 'li' ).removeClass( 'selected' );

		// Then add it back to the list item parent of the click target
		this.parentNode.classList.add( 'selected' );

		// Display the selected SVG file in the modal footer for confirmation
		$( '.selected-icon' ).show().empty();
		this.clone().appendTo( $( '.selected-icon span' ) );
	} );

	$( '#icon-select-btn' ).click( function () {
		const selectedIcon = $( '.selected' ).attr( 'id' ).replace( '[', '-' ).replace( ']', '' );
		$( '#menu_icon' ).val( selectedIcon ).focus();
		console.log( $( '.selected' ).attr( 'id' ) + ' was selected' );
		$( iconsModal ).modal( 'hide' );
	} );
} )( jQuery );

// TODO: Detect whether a cpt or taxonomy has already been registered on the front-end?
