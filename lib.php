<?php

function local_courseduplication_extends_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE;

    // Only add navigation item on non-site course pages.
    if (!$PAGE->course or $PAGE->course->id == 1) {
        return;
    }

    if (!has_capability('local/courseduplication:backup_course', context_course::instance($PAGE->course->id))) {
        return;
    }

    if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
        $strduplicate = get_string('duplicatecourse', 'local_courseduplication');
        $url = new moodle_url('/local/courseduplication/duplicate.php', array('id' => $PAGE->course->id));
        $duplicatenode = navigation_node::create(
            $strduplicate,
            $url,
            navigation_node::NODETYPE_LEAF,
            'duplicatecourse',
            'duplicatecourse',
            new pix_icon('t/copy', $strduplicate)
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $duplicatenode->make_active();
        }
        $settingnode->add_node($duplicatenode);
    }
}
