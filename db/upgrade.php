<?php
// This file is part of Moodle - http://moodle.org/
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
 * Course Duplication upgrade script (run when version.php's version changes)
 *
 * @package local_courseduplication
 * @copyright Liip AG <https://www.liip.ch/>
 * @author Didier Raboud <didier.raboud@liip.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function launched when local_courseduplication upgrades.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_local_courseduplication_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2019093001) {

        $table = new xmldb_table('courseduplication_queue');
        $fields = [
            new xmldb_field('fullname', XMLDB_TYPE_CHAR, '254', null, null, null, null, 'runid'),
            new xmldb_field('shortname', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'fullname'),
            new xmldb_field('startdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'shortname'),
            new xmldb_field('enddate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'startdate'),
            new xmldb_field('coursegroups', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'enddate'),
            new xmldb_field('enrolfromroles', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'coursegroups'),
            new xmldb_field('automaticenddate', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'enrolfromroles')
        ];

        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        upgrade_plugin_savepoint(true, 2019093001, 'local', 'courseduplication');
    }

    return true;
}
