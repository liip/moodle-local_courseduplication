<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Assign capabilities to coursecreatorehl role
 */
function xmldb_local_courseduplication_install() {
    global $DB;

    if ($role = $DB->get_record('role', array('shortname' => 'coursecreatorehl'))) {
        $syscontext = context_system::instance();
        assign_capability('local/courseduplication:backup_course', CAP_ALLOW, $role->id, $syscontext->id, $overwrite = true);
        assign_capability('local/courseduplication:restore_course', CAP_ALLOW, $role->id, $syscontext->id, $overwrite = true);
    }
}
