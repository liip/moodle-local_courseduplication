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
 * @copyright 2017 Liip <https://www.liip.ch/>
 * @author Didier Raboud <didier.raboud@liip.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_courseduplication\event;
defined('MOODLE_INTERNAL') || die();

class duplication_failed extends \core\event\base {

    protected function init() {
        $this->data['crud'] = '';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'course';
    }

    protected function validate_data() {
        if (!isset($this->data['other']['newcategoryid'])) {
            throw new \coding_exception('newcategoryid is mandatory in \'other\'.');
        }
    }

    public static function get_name() {
        return get_string('event_duplication_failed', 'local_courseduplication');
    }

    public function get_description() {
        return "The duplication of course {$this->objectid} to category ".
               "{$this->data['other']['newcategoryid']} asked by user id {$this->userid} failed!";
    }

    public function get_url() {
        return new \moodle_url('course', array('id' => $this->objectid));
    }

    public function get_legacy_logdata() {
        return array($this->courseid,
            'courseduplication', 'restore', '',
            "duplication failed.  course id {$this->objectid} to category {$this->data['other']['newcategoryid']}";
    }
}
