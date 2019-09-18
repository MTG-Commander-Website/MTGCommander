/**
 * Functionality specific to Visualize.
 *
 * Provides helper functions to enhance the theme experience.
 */

( function( $ ) {
	var body    = $( 'body' ),
	    _window = $( window );

	/**
	 * Makes "skip to content" link work correctly in IE9 and Chrome for better
	 * accessibility.
	 *
	 * @link http://www.nczonline.net/blog/2013/01/15/fixing-skip-to-content-links/
	 */
	_window.on( 'hashchange.visualize', function() {
		var element = document.getElementById( location.hash.substring( 1 ) );

		if ( element ) {
			if ( ! /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) )
				element.tabIndex = -1;

			element.focus();
		}
	} );

	/**
	 * Arranges footer widgets to fit the available space.
	 */
+	$( function() {
		if ( ! $.isFunction( $.fn.masonry ) ) {
			return;
		}

		var widgetArea = $( '#secondary .inner' );
		widgetArea.masonry( {
			itemSelector: '.widget',
			columnWidth: 320,
			gutterWidth: 32,
			isRTL: body.is( '.rtl' )
		} );

		if ( 'undefined' !== typeof wp && wp.customize && wp.customize.selectiveRefresh  ) {
			wp.customize.selectiveRefresh.bind( 'sidebar-updated', function( sidebarPartial ) {
				if ( 'footer' === sidebarPartial.sidebarId ) {
					widgetArea.masonry( 'reloadItems' );
					widgetArea.masonry( 'layout' );
				}
			} );
		}
	});

	/**
	 * Emulates background-size: cover for featured images filling half the screen.
	 */
+	$( function() {

		repositionBackgrounds();
		
		$( window ).resize( repositionBackgrounds );
		
		function repositionBackgrounds() {
			// Get the viewport aspect ratio.
			var vh = window.innerHeight,
			    vw = window.innerWidth,
			    vratio = vw * .5 / vh,
			    ih, iw, iratio, offset;

			if ( 600 >= vw ) {
				return;
			}

			$( '.entry-visual' ).each( function() {

				ih = $( this ).data( 'height' );
				iw = $( this ).data( 'width' );
				iratio = iw / ih;
				
				if ( iratio < vratio ) {
					$( this ).addClass( 'more-portrait' )
					         .removeClass( 'more-landscape' );
					$( this ).css( 'background-position-x', 0 );
				} else {
					$( this ).addClass( 'more-landscape' )
					         .removeClass( 'more-portrait' );
					
					// Calculate and set background-position-x.
					// Scale iw to the scaled height
					offset = - ( iw * vh / ih - vw * .5 ) / 2;
					$( this ).css( 'background-position-x', offset );
				}
			});
		}
	});

} )( jQuery );