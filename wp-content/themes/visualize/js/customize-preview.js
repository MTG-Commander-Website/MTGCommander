/**
 * Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	// Footer copyright name.
	wp.customize( 'copy_name', function( value ) {
		value.bind( function( to ) {
			$( '#footer-copy-name' ).text( to );
		} );
	} );

	// Footer credits.
	wp.customize( 'powered_by_wp', function( value ) {
		value.bind( function( to ) {
			$( '.wordpress-credit' ).toggle( to );
		} );
	} );
	wp.customize( 'theme_meta', function( value ) {
		value.bind( function( to ) {
			$( '.theme-credit' ).toggle( to );
		} );
	} );

	wp.customize( 'hue', function( value ) {
		value.bind( function( to ) {
			// Update custom color CSS
			var style = $( '#visualize-colors' ),
			    color = style.data( 'cab_h' ),
			    css = style.html();
			css = css.split( color + ', ' ).join( to + ', ' ); // css.replaceAll, only do unitless numbers.
			$( '#visualize-colors' ).html( css )
			                        .data( 'cab_h', to );
		} );
	} );

	wp.customize( 'saturation', function( value ) {
		value.bind( function( to ) {
			// Update custom color CSS
			var style = $( '#visualize-colors' ),
			    color = style.data( 'cab_s' ),
			    css = style.html();
			css = css.split( ', ' + color + '%, ' ).join( ', ' + to + '%, ' ); // css.replaceAll, try to only change the center number.
			$( '#visualize-colors' ).html( css )
			                        .data( 'cab_s', to );
		} );
	} );

} )( jQuery );
