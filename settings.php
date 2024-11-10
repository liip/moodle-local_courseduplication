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
 * @package local_courseduplication
 * @copyright 2014-2020 Liip AG <https://www.liip.ch/>
 * @author Liip <elearning@liip.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage(
        'local_courseduplication',
        'Course duplication settings' );

    /** @var admin_root $ADMIN */
    $ADMIN->add('localplugins', $settings);
    $options = array(
        '' => get_string('duplicatorrole_none', 'local_courseduplication')
    );
    $options = $options + get_viewable_roles(context_system::instance());

    $settings->add(
        new admin_setting_configselect(
            'local_courseduplication/duplicatorrole',
            get_string('duplicatorrole', 'local_courseduplication'),
            get_string('duplicatorrole_help', 'local_courseduplication'),
            '',
            $options
        )
    );
}
