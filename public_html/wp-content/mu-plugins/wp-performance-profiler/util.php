<?php

if( ! function_exists( 'icit_profiler_is_loaded' ) ) {
    /**
     * Is the profiler already loaded
     *
     * This means the files are loaded, and not necesserily that the plugin is active and collecting data
     * And it can therefore always return true
     */
    function icit_profiler_is_loaded() {
        return true;
    }
}

if( ! function_exists( 'icit_profiler_is_active' ) ) {
    /**
     * Is the profiler active
     *
     * This means it's running and showing up in the admin area
     * It does not necesserily mean it's collecting, which depends on the setup
     */
    function icit_profiler_is_active() {
        $settings        = get_option( 'icit_performance_profiler' );
        $plugins         = get_option( 'active_plugins', array() );
        $settings_active = isset( $settings['active'] ) ? $settings['active'] : true;
        $plugin_active   = array_search( ICIT_PERFORMANCE_PROFILER_PLUGIN_FILE, $plugins ) !== false;

        return $settings_active || $plugin_active;
    }
}

if( ! function_exists( 'icit_profiler_is_mu_plugin' ) ) {
    /**
     * Is the plugin installed in the mu-plugins directory
     */
    function icit_profiler_is_mu_plugin() {
        $this_path = wp_normalize_path( plugin_dir_path( __FILE__ ) );
        $mu_path   = wp_normalize_path( WPMU_PLUGIN_DIR );

        return strpos( $this_path, $mu_path ) !== false;
    }
}

if( ! function_exists( 'icit_profiler_is_installed' ) ) {
    /**
     * Is the plugin installed?
     *
     * This ensures we have the correct database schema and default options in the database
     */
    function icit_profiler_is_installed() {
        global $wpdb;

        $settings = get_option( 'icit_performance_profiler' );
        $tables   = $wpdb->get_col( "SHOW TABLES" );
        $tables   = array_flip( $tables );

        return ! empty( $settings ) &&
               isset( $tables["{$wpdb->prefix}profiler_functions"] ) &&
               isset( $tables["{$wpdb->prefix}profiler_plugins"] ) &&
               isset( $tables["{$wpdb->prefix}profiler_queries"] ) &&
               isset( $tables["{$wpdb->prefix}profiler_requests"] );
    }
}

if( ! function_exists( 'icit_profiler_request_types' ) ) {
    /**
     * Returns an array of all the supported request types
     *
     * The array key is the internal name and the value is the human readable name
     */
    function icit_profiler_request_types() {
        return array(
            'front'  => 'Front-end',
            'admin'  => 'Admin',
            'ajax'   => 'AJAX',
            'cron'   => 'Cron',
            // 'feed'   => 'Feed',
            // '404'    => '404',
            // 'search' => 'Search',
        );
    }
}

if( ! function_exists( 'icit_profiler_current_request_type' ) ) {
    /**
     * Return the internal name for the current request type
     */
    function icit_profiler_current_request_type() {
        if( defined( 'DOING_CRON' ) && DOING_CRON ) return 'cron';
        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) return 'ajax';
        // if( is_404() ) return '404';
        // if( is_search() ) return 'search';
        // if( is_feed() ) return 'feed';
        if( is_admin() ) return 'admin';
        return 'front';
    }
}

if( ! function_exists( 'icit_profiler_cross_platform_path' ) ) {
    function icit_profiler_cross_platform_path( $path ) {
        return str_replace( '\\', '/', $path );
    }
}
