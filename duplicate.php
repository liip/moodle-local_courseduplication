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

require_once(__DIR__ . '/../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once(__DIR__ . '/duplication_form.php');
require_once(__DIR__ . '/locallib.php');

$id = required_param('id', PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);

$PAGE->set_url('/local/courseduplication/duplicate.php', array('id' => $id));

if (! $course = $DB->get_record("course", array('id' => $id))) {
    print_error('invalidcourseid');
}

require_course_login($course);

$coursecontext = context_course::instance($course->id);
$strduplicate = get_string('courseduplication', 'local_courseduplication');

$PAGE->set_pagelayout('incourse');
$PAGE->set_title($strduplicate);
$PAGE->set_heading($course->fullname);

$mform = new courseduplication_duplication_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('duplicatecourseheader', 'local_courseduplication', $course));

if (!has_capability('local/courseduplication:backup_course', $coursecontext)) {
    print_error(
        'nopermissions',
        'error',
        new moodle_url('/course/view.php', array('id' => $course->id)),
        $strduplicate
    );
}

echo $OUTPUT->box(
    get_string('duplicatedesc', 'local_courseduplication')
);

if ($form = $mform->get_data()) {
    if (!$category = $DB->get_record('course_categories', array('id' => $form->categoryid))) {
        print_error('errornosuchcategory', 'local_courseduplication');
    }
    if (!has_capability('local/courseduplication:restore_course', context_coursecat::instance($category->id))) {
        print_error(
            'errornopermsintarget',
            'local_courseduplication',
            new moodle_url('/course/view.php', array('id' => $course->id))
        );
    }

    local_course_duplication_queue::queue($course->id, $category->id, $USER->id);
    echo $OUTPUT->notification(
        get_string('duplicationscheduled', 'local_courseduplication'), 'coursedup-notice'
    );

    echo $OUTPUT->continue_button(new moodle_url('/my/index.php'));

} else {
    // This branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
    echo $OUTPUT->container(get_string('duplicationwillbescheduled', 'local_courseduplication'), 'coursedup-notice');

    $mform->set_data(array(
        'categoryid' => $categoryid ? $categoryid : $course->category,
        'id' => $course->id));
    $mform->display();
}
echo $OUTPUT->footer();
