<?php

namespace ICIT_Performance_Profiler;

/**
 * Admin bootstrap functionality
 */

class Admin {
    private static $instance;

    public static function instance() {
        if( self::$instance === null ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     *
     */
    public function __construct() {
        $this->install();

        add_action( 'admin_menu', array( $this, 'setup_menu' ) );
        add_filter( 'parent_file', array( $this, 'set_correct_submenu_item' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );

        // Setup our settings if we're on the settings tab of our plugin
        require_once ICIT_PERFORMANCE_PROFILER_DIR . 'admin/settings.php';
        $settings = Settings::instance();
        add_action( 'admin_init', array( $settings, 'register_settings' ) );

        $action = isset( $_GET['tab'] ) ? $_GET['tab'] : 'requests';

        if( $action === 'maintenance' ) {
            require_once ICIT_PERFORMANCE_PROFILER_DIR . 'admin/maintenance.php';
            $controller = new Maintenance_Controller;

            add_action( 'admin_init', array( $controller, 'run' ) );
        }
    }

    private function install() {
        global $wpdb;

        // If this is already installed, we have nothing to do
        if( icit_profiler_is_installed() ) return;

        // Create the default options
        add_option( 'icit_performance_profiler', array(
            'basic_frequency'    => 10,
            'advanced_frequency' => 1,
            'request_types'      => array( 'front' => 'on', 'admin' => 'on', 'cron' => 'on', 'ajax' => 'on' ),
            'active'             => true,
        ) );

        // Create the database schema
        // There are a total of 4 tables
        require_once  ABSPATH . 'wp-admin/includes/upgrade.php';

        if ( ( ! function_exists( 'maybe_create_table' ) || ! function_exists( 'check_column' ) ) && file_exists( ABSPATH . '/wp-admin/install-helper.php' ) )
            require_once( ABSPATH . '/wp-admin/install-helper.php' );

        if ( ! empty( $wpdb->charset ) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

        if ( ! empty( $wpdb->collate ) )
            $charset_collate .= " COLLATE $wpdb->collate";

        maybe_create_table( "{$wpdb->prefix}profiler_functions", "CREATE TABLE {$wpdb->prefix}profiler_functions (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `request_id` int(11) DEFAULT NULL,
            `plugin` varchar(64) DEFAULT NULL,
            `function` varchar(256) DEFAULT NULL,
            `count` int(11) DEFAULT NULL,
            `duration` float DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `request_id` (`request_id`),
            KEY `plugin` (`plugin`),
            KEY `function` (`function`),
            KEY `count` (`count`),
            KEY `duration` (`duration`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 $charset_collate;" );

        maybe_create_table( "{$wpdb->prefix}profiler_plugins", "CREATE TABLE {$wpdb->prefix}profiler_plugins (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `request_id` int(11) DEFAULT NULL,
            `plugin` varchar(64) DEFAULT NULL,
            `count` int(11) DEFAULT NULL,
            `duration` double DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `request_id` (`request_id`),
            KEY `count` (`count`),
            KEY `duration` (`duration`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 $charset_collate;" );

        maybe_create_table( "{$wpdb->prefix}profiler_queries", "CREATE TABLE {$wpdb->prefix}profiler_queries (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `request_id` int(11) DEFAULT NULL,
            `duration` double DEFAULT NULL,
            `plugin` varchar(64) DEFAULT NULL,
            `the_query` varchar(2048) DEFAULT NULL,
            `stack` varchar(2048) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `request_id` (`request_id`),
            KEY `duration` (`duration`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 $charset_collate;" );

        maybe_create_table( "{$wpdb->prefix}profiler_requests", "CREATE TABLE {$wpdb->prefix}profiler_requests (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `timestamp` int(11) DEFAULT NULL,
            `request` varchar(1024) DEFAULT NULL,
            `type` varchar(8) DEFAULT NULL,
            `template` varchar(1024) DEFAULT NULL,
            `duration` double DEFAULT NULL,
            `memory` int(11) DEFAULT NULL,
            `queries` int(11) DEFAULT NULL,
            `payload` text,
            PRIMARY KEY (`id`),
            KEY `timestamp` (`timestamp`),
            KEY `type` (`type`),
            KEY `template` (`template`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 $charset_collate;" );
    }

    public function setup_menu() {
        add_menu_page( 'Performance Profiler', 'Profiler', 'manage_options', 'icit-profiler', array( $this, 'render_page' ), 'dashicons-chart-line' );
        add_submenu_page( 'icit-profiler', 'Requests', 'Requests', 'manage_options', 'icit-profiler' );
        add_submenu_page( 'icit-profiler', 'Plugins', 'Plugins', 'manage_options', 'admin.php?page=icit-profiler&tab=plugins' );
        add_submenu_page( 'icit-profiler', 'Database', 'Database', 'manage_options', 'admin.php?page=icit-profiler&tab=database' );
        add_submenu_page( 'icit-profiler', 'Settings', 'Settings', 'manage_options', 'admin.php?page=icit-profiler&tab=settings' );
        add_submenu_page( 'icit-profiler', 'Maintenance', 'Maintenance', 'manage_options', 'admin.php?page=icit-profiler&tab=maintenance' );
        add_submenu_page( 'icit-profiler', 'Help', 'Help', 'manage_options', 'admin.php?page=icit-profiler&tab=help' );
    }

    public function set_correct_submenu_item( $parent ) {
        global $submenu_file;

        if ( isset( $_GET['page'] ) && $_GET['page'] === 'icit-profiler' && isset( $_GET['tab'] ) ) {
            $submenu_file = 'admin.php?page=icit-profiler&tab=' . $_GET['tab'];
        }

        return $parent;
    }

    public function admin_assets() {
        // Only proceed if we're on our admin pages
        if( ! isset( $_GET['page'] ) || $_GET['page'] !== 'icit-profiler' ) return;

        wp_enqueue_style( 'icit-profiler-admin', ICIT_PERFORMANCE_PROFILER_URL . 'assets/css/admin.css' );
        wp_enqueue_script( 'icit-profiler-admin', ICIT_PERFORMANCE_PROFILER_URL . 'assets/js/admin.js', array( 'jquery' ) );
    }

    public function render_page() {
        global $wpdb;
        ?>
        <div class="wrap">
            <h2>Performance Profiler</h2>

            <h2 class="nav-tab-wrapper">
                <a href="?page=icit-profiler&tab=requests" class="nav-tab <?php $this->is_tab_active('requests')?>">Requests</a>
                <a href="?page=icit-profiler&tab=plugins" class="nav-tab <?php $this->is_tab_active('plugins')?>">Plugins</a>
                <a href="?page=icit-profiler&tab=detail" class="nav-tab <?php $this->is_tab_active('detail')?>">In Depth</a>
                <a href="?page=icit-profiler&tab=database" class="nav-tab <?php $this->is_tab_active('database')?>">Database</a>
                <a href="?page=icit-profiler&tab=settings" class="nav-tab <?php $this->is_tab_active('settings')?>">Settings</a>
                <a href="?page=icit-profiler&tab=maintenance" class="nav-tab <?php $this->is_tab_active('maintenance')?>">Maintenance</a>
                <a href="?page=icit-profiler&tab=help" class="nav-tab <?php $this->is_tab_active('help')?>">Help</a>
            </h2>
        </div>

        <div class="wrap">
            <?php
            $tab  = $this->get_active_tab();

            switch( $tab ) {
                case 'requests':
                    // Setup the database query
                    $results_query = "SELECT * FROM {$wpdb->prefix}profiler_requests WHERE 1 = 1";
                    $count_query   = "SELECT COUNT(id) FROM {$wpdb->prefix}profiler_requests WHERE 1 = 1";
                    $values        = array();

                    // Are we filtering by date?
                    if( ! empty( $_GET['date'] ) ) {
                        $results_query .= " AND timestamp > %d";
                        $count_query   .= " AND timestamp > %d";
                        $values[]       = strtotime( $_GET['date'] );
                    }

                    // Do we want to filter on the URL?
                    if( ! empty( $_GET['url'] ) ) {
                        $results_query .= " AND request LIKE %s";
                        $count_query   .= " AND request LIKE %s";
                        $values[]       = '%' . $_GET['url'] . '%';
                    }

                    // Do we want to filter just slow loading request?
                    if( ! empty( $_GET['duration'] ) ) {
                        $results_query .= " AND duration > %d";
                        $count_query   .= " AND duration > %d";
                        $values[]       = absint( $_GET['duration'] );
                    }

                    // Do we want to filter by memory consumption
                    if( ! empty( $_GET['memory'] ) ) {
                        $results_query .= " AND memory > %d";
                        $count_query   .= " AND memory > %d";
                        $values[]       = absint( $_GET['memory'] ) * 1024 * 1024;
                    }

                    // Are we filtering by number of database queries
                    if( ! empty( $_GET['queries'] ) ) {
                        $results_query .= " AND queries > %d";
                        $count_query   .= " AND queries > %d";
                        $values[]       = absint( $_GET['queries'] );
                    }

                    // Are we filtering by template?
                    if( ! empty( $_GET['template'] ) ) {
                        $results_query .= " AND template = %s";
                        $count_query   .= " AND template = %s";
                        $values[]       = $_GET['template'];
                    }

                    // Are we filtering by type?
                    if( ! empty( $_GET['type'] ) ) {
                        $results_query .= " AND type = %s";
                        $count_query   .= " AND type = %s";
                        $values[]       = $_GET['type'];
                    }

                    // Set the order
                    $results_query .= " ORDER BY id DESC";

                    // Add pagination
                    $results_query .= " LIMIT %d, 100";
                    $values[]       = isset( $_GET['p'] ) ? ( $_GET['p'] - 1 ) * 100 : 0;

                    // Execute the query
                    $results_query = $wpdb->prepare( $results_query, $values );
                    $rows          = $wpdb->get_results( $results_query );

                    // Get the total number of rows
                    if( count( $values) > 0 ) {
                        array_pop($values);
                    }
                    if( count( $values ) ) {
                        $count_query = $wpdb->prepare( $count_query, $values );
                    }

                    $total_rows      = $wpdb->get_var( $count_query );

                    break;
                case 'plugins':
                    // Get the plugin stats for each request type
                    $front   = $this->get_plugin_stats_by_request_type( 'front' );
                    $admin   = $this->get_plugin_stats_by_request_type( 'admin' );
                    $ajax    = $this->get_plugin_stats_by_request_type( 'ajax' );
                    $cron    = $this->get_plugin_stats_by_request_type( 'cron' );
                    $average = $this->get_plugin_stats_by_request_type( 'all' );

                    // Merge all the data into a single, structured array
                    $types = compact( 'front', 'admin', 'ajax', 'cron', 'average' );
                    $data    = array();

                    foreach( $types as $type => $plugins ) {
                        foreach( $plugins as $plugin ) {
                            if( ! isset( $data[ $plugin->plugin ] ) ) {
                                $data[ $plugin->plugin ] = array( 'plugin' => $plugin->plugin );
                            }

                            $data[ $plugin->plugin ][ $type ] = $plugin->duration;
                        }
                    }

                    // Sort the plugins based on the average order
                    usort( $data, function( $a, $b ) {
                        return $a['average'] < $b['average'];
                    } );

                    break;
                case 'detail':
                    if( ! isset( $_GET['request_id'] ) ) break;

                    $request_id = absint( $_GET['request_id'] );

                    // Get basic request details
                    $query   = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}profiler_requests WHERE id = %d", $request_id );
                    $request = $wpdb->get_row( $query );

                    // Get all the function level stats from the database
                    $query      = "SELECT * FROM {$wpdb->prefix}profiler_functions WHERE request_id = %d";
                    $query      = $wpdb->prepare( $query, $request_id );
                    $rows       = $wpdb->get_results( $query );

                    // Pre-process them so it's all in a nice, structured array
                    $data = array();

                    foreach( $rows as $row ) {
                        // Is this the first stat for this plugin?
                        // If so, we need to initialise the data structures
                        if( ! isset( $data[ $row->plugin ] ) ) {
                            $data[ $row->plugin ] = array(
                                'plugin'    => $row->plugin,
                                'count'     => 0,
                                'duration'  => 0,
                                'functions' => array(),
                            );
                        }

                        $data[ $row->plugin ]['count']      += $row->count;
                        $data[ $row->plugin ]['duration']   += $row->duration;
                        $data[ $row->plugin ]['functions'][] = $row;
                    }

                    // And now sort everything so we have the slowest at the top
                    // Do this at both the plugin and function level
                    usort( $data, array( __NAMESPACE__ . '\Helpers', 'order' ) );

                    foreach( $data as $index => $row ) {
                        usort( $row['functions'], array( __NAMESPACE__ . '\Helpers', 'order' ) );

                        $data[ $index ] = $row;
                    }

                    // Get the payload data
                    $payload = maybe_unserialize( $request->payload );

                    break;
                case 'database':
                    require_once ICIT_PERFORMANCE_PROFILER_DIR . 'admin/database.php';

                    // Setup the database queries
                    // We'll have one for the results and one for the count
                    $results_query = "SELECT queries.request_id, queries.duration, queries.plugin, queries.the_query, requests.timestamp, requests.type
                                       FROM {$wpdb->prefix}profiler_queries queries
                                       JOIN {$wpdb->prefix}profiler_requests requests
                                       WHERE queries.request_id = requests.id";
                    $count_query   = "SELECT COUNT(queries.request_id)
                                       FROM {$wpdb->prefix}profiler_queries queries
                                       JOIN {$wpdb->prefix}profiler_requests requests
                                       WHERE queries.request_id = requests.id";
                    $values        = array();

                    // Are we filtering by request?
                    if( ! empty( $_GET['request_id'] ) ) {
                        $results_query .= " AND request_id = %d";
                        $count_query   .= " AND request_id = %d";
                        $values[]       = absint( $_GET['request_id'] );
                    }

                    // Are we filtering by date?
                    if( ! empty( $_GET['date'] ) ) {
                        $results_query .= " AND timestamp > %d";
                        $count_query   .= " AND timestamp > %d";
                        $values[]       = strtotime( $_GET['date'] );
                    }

                    // Are we filtering by plugin?
                    if( ! empty( $_GET['plugin'] ) ) {
                        $results_query .= " AND plugin = %s";
                        $count_query   .= " AND plugin = %s";
                        $values[]       = $_GET['plugin'];
                    }

                    // Do we want to filter on the SQL query
                    if( ! empty( $_GET['the_query'] ) ) {
                        $results_query .= " AND the_query LIKE %s";
                        $count_query   .= " AND the_query LIKE %s";
                        $values[]       = '%' . $_GET['the_query'] . '%';
                    }

                    // Do we want to filter just slow loading request?
                    if( ! empty( $_GET['duration'] ) ) {
                        $results_query .= " AND queries.duration > %d";
                        $count_query   .= " AND queries.duration > %d";
                        $values[]       = absint( $_GET['duration'] );
                    }

                    // Are we filtering by type?
                    if( ! empty( $_GET['type'] ) ) {
                        $results_query .= " AND type = %s";
                        $count_query   .= " AND type = %s";
                        $values[]       = $_GET['type'];
                    }

                    // Set the order
                    // We want the slowest ones first
                    $results_query .= " ORDER BY duration DESC";

                    // Add pagination
                    $results_query   .= " LIMIT %d, 100";
                    $values[] = isset( $_GET['p'] ) ? ( $_GET['p'] - 1 ) * 100 : 0;

                    // Execute the query
                    $results_query = $wpdb->prepare( $results_query, $values );
                    $rows          = $wpdb->get_results( $results_query );

                    // Get the total number of rows
                    if( count( $values) > 0 ) {
                        array_pop($values);
                    }
                    if( count( $values ) ) {
                        $count_query = $wpdb->prepare( $count_query, $values );
                    }

                    $total_rows      = $wpdb->get_var( $count_query );

                    break;
                case 'maintenance':
                    if( isset( $_GET['action'] ) ) {
                        $action = $_GET['action'];

                        if( $action === 'uninstall' ) {

                        } else if( $action === 'delete' ) {
                            $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}profiler_functions" );
                            $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}profiler_plugins" );
                            $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}profiler_queries" );
                            $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}profiler_requests" );
                        }
                    }

                    break;
            }

            include ICIT_PERFORMANCE_PROFILER_DIR . 'views/admin/' . $tab . '.php';
            ?>
        </div>
        <?php
    }

    private function get_active_tab() {
        $valid   = array( 'requests', 'plugins', 'detail', 'database', 'settings', 'maintenance', 'help' );
        $default = 'requests';

        if( ! isset( $_GET['tab'] ) ) return $default;
        if( ! in_array( $_GET['tab'], $valid ) ) return $default;

        return $_GET['tab'];
    }

    public function admin_notices() {
        if( ! icit_profiler_is_mu_plugin() ):
            ?>
            <div class="updated">
                <p>
                    The WordPress Performance Profiler works best as a must-use plugin but is currently installed as a regular plugin.
                    <a href="<?php echo admin_url( 'admin.php?page=icit-profiler&tab=help' )?>">Install the plugin as a must-use plugin now.</a>
                </p>
            </div>
            <?php
        endif;
    }

    private function is_tab_active( $tab ) {
        echo $tab == $this->get_active_tab() ? 'nav-tab-active' : '';
    }

    private function get_plugin_stats_by_request_type( $type ) {
        global $wpdb;

        if( $type === 'all' ) {
            return $wpdb->get_results( "
                SELECT plugin.plugin, ( SUM(plugin.duration) / COUNT(plugin.duration) ) AS duration
                FROM {$wpdb->prefix}profiler_plugins plugin
                WHERE 1 = 1
                GROUP BY plugin.plugin
                ORDER BY duration DESC
            " );
        } else {
            return $wpdb->get_results( $wpdb->prepare( "
                SELECT plugin.plugin, ( SUM(plugin.duration) / COUNT(plugin.duration) ) AS duration
                FROM {$wpdb->prefix}profiler_plugins plugin
                JOIN {$wpdb->prefix}profiler_requests request
                WHERE 1 = 1
                AND plugin.request_id = request.id
                AND request.type = %s
                GROUP BY plugin.plugin
                ORDER BY duration DESC
            ", $type ) );
        }
    }
}
Admin::instance();
