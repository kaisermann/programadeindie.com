<?php

namespace ICIT_Performance_Profiler;

/**
 * Basic Logger
 *
 * This will track the duration, number of queries and memory usage
 * And log them to the database with information about the request
 */
class Basic_Logger extends Base_Logger {
    public function __construct() {
        parent::__construct();
    }

    public function save() {
        $this->save_request();
    }

    public function render() {

    }
}
