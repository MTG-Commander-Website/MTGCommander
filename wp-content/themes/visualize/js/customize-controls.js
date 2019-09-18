/**
 * Scripts within the customizer controls window.
 *
 * Contextually shows the color hue control based on the selected saturation.
 */

(function() {
	wp.customize.bind( 'ready', function() {

		// Only show the color hue control when there's a custom color scheme.
		wp.customize( 'saturation', function( setting ) {
			wp.customize.control( 'hue', function( control ) {
				var visibility = function() {
					if ( 0 == setting.get() ) {
						control.container.slideUp( 180 );
					} else {
						control.container.slideDown( 180 );
					}
				};

				visibility();
				setting.bind( visibility );
			});

		});

	});
})( jQuery );
