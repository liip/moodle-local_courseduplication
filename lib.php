<?php
// This file is part of local/courseduplication
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package local/courseduplication
 * @copyright 2014-2017 Liip <https://www.liip.ch/>
 * @author Brian King <brian.king@liip.ch>
 * @author Claude Bossy <claude.bossy@liip.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function local_courseduplication_extend_settings_navigation($settingsnav, $context) {
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
