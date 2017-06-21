jQuery( function( $ ) {
    $( 'body' ).on( 'click', '.icit-profiler-table-functions .summary', toggle_details );

    function toggle_details( event ) {
        event.preventDefault();

        var $this      = $( this ),
            plugin     = $this.data( 'plugin' ),
            $functions = $( '.plugin-' + plugin );

        $functions.slideToggle();
    }
} );
