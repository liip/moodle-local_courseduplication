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
 * @copyright 2014-2018 Liip AG <https://www.liip.ch/>
 * @author Brian King <brian.king@liip.ch>
 * @author Claude Bossy <claude.bossy@liip.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class courseduplication_duplication_form extends moodleform {

    private function get_groups($courseid) {
        $groups = [];
        foreach (groups_get_all_groups($courseid) as $groupobj) {
            $groups[$groupobj->id] = $groupobj->name;
        }
        return $groups;
    }

    /**
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function definition() {
        global $CFG, $DB;
        $basecourseid = $this->_customdata['id'];
        $basecategoryid = $this->_customdata['categoryid'];
        $basecoursecontext = context_course::instance($basecourseid);
        /** @var stdClass $basecourse */
        $basecourse = $DB->get_record('course', array('id' => $basecourseid));

        $mform =& $this->_form;
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Target category
        $mform->addElement('select', 'categoryid',
            get_string('targetcategory', 'local_courseduplication'),
            make_categories_options()
        );
        $mform->setType('categoryid', PARAM_INT);
        $mform->addRule('categoryid', get_string('errormissingcategory', 'local_courseduplication'),
            'required', null, 'client');

        // Copied course name
        $mform->addElement('text','targetfullname', get_string('fullnamecourse'),'maxlength="254" size="50"');
        $mform->addHelpButton('targetfullname', 'fullnamecourse');
        $mform->setType('targetfullname', PARAM_TEXT);
        $mform->setDefault('targetfullname', $basecoursecontext->get_context_name(false) . " copy 1");
        $mform->addRule('targetfullname', get_string('missingfullname'), 'required', null);

        // Copied course short name
        $mform->addElement('text', 'targetshortname', get_string('shortnamecourse'), 'maxlength="100" size="20"');
        $mform->addHelpButton('targetshortname', 'shortnamecourse');
        $mform->setType('targetshortname', PARAM_TEXT);
        $mform->setDefault('targetshortname', $basecoursecontext->get_context_name(false, true) . "_copy_1");
        $mform->addRule('targetshortname', get_string('missingshortname'), 'required', null);

        // Copied course startdate
        $mform->addElement('date_time_selector', 'targetstartdate', get_string('startdate'));
        $mform->addHelpButton('targetstartdate', 'startdate');
        $mform->setDefault('targetstartdate', $basecourse->startdate);

        // Copied course enddate
        $mform->addElement('date_time_selector', 'targetenddate', get_string('enddate'));
        $mform->addHelpButton('targetenddate', 'enddate');
        $mform->setDefault('targetenddate', $basecourse->enddate);

        if ($basecourse->format === "weeks") {
            $baseautomaticenddate = $DB->get_record('course_format_options', array(
                'courseid' => $basecourseid,
                'name' => 'automaticenddate'
            ))->value;

            $mform->addElement('advcheckbox', 'targetautomaticenddate', get_string('automaticenddate', 'format_weeks'));
            $mform->addHelpButton('targetautomaticenddate', 'automaticenddate', 'format_weeks');
            $mform->setDefault('targetautomaticenddate', $baseautomaticenddate);
            $mform->disabledIf('targetenddate', 'targetautomaticenddate', 'checked');
        }

        // Copy roles
        $mform->addElement('autocomplete', 'coursegroups', get_string('coursegroups', 'local_courseduplication'),
            $this->get_groups($basecourseid), array('multiple' => true)
        );
        $mform->addHelpButton('coursegroups', 'coursegroups', 'local_courseduplication');

        // Enrol from roles
        $mform->addElement('autocomplete', 'enrolfromroles', get_string('enrolfromroles', 'local_courseduplication'),
            get_default_enrol_roles($basecoursecontext), array('multiple' => true)
        );
        $mform->addHelpButton('enrolfromroles', 'enrolfromroles', 'local_courseduplication');

        $this->add_action_buttons(true, get_string('duplicate', 'local_courseduplication'));

    }

}
