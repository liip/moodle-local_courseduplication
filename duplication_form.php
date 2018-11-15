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

class courseduplication_duplication_form extends moodleform {

    protected function definition() {
        global $CFG;
        $mform =& $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('select', 'categoryid',
            get_string('targetcategory', 'local_courseduplication'),
            make_categories_options()
        );
        $mform->setType('categoryid', PARAM_INT);
        $mform->addRule('categoryid', get_string('errormissingcategory', 'local_courseduplication'),
            'required', null, 'client');
        $this->add_action_buttons(true, get_string('duplicate', 'local_courseduplication'));
    }

}
