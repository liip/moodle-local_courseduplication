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

require_once(__DIR__ . '/../../backup/util/includes/backup_includes.php');
require_once(__DIR__ . '/../../backup/util/includes/restore_includes.php');
require_once(__DIR__ . '/../../group/lib.php');
require_once(__DIR__ . '/../../course/format/weeks/lib.php');

class local_courseduplication_controller
{

    protected $options = array(
        'activities' => 1,
        'blocks' => 1,
        'filters' => 1,
        'users' => 0,
        'role_assignments' => 0,
        'comments' => 0,
        'logs' => 0,
        'groups' => 0,
    );

    public function backup_course($courseid, $userid, $log=false) {
        $bc = new backup_controller(
            backup::TYPE_1COURSE, $courseid, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_SAMESITE, $userid);
        if ($log) {
            ob_end_flush();
            $bc->get_logger()->set_next(new output_indented_logger(backup::LOG_DEBUG, false, true));
        }

        foreach ($this->options as $name => $value) {
            $setting = $bc->get_plan()->get_setting($name);
            $setting->set_status(backup_setting::NOT_LOCKED);
            $setting->set_value($value);
        }
        $backupid       = $bc->get_backupid();
        $backupbasepath = $bc->get_plan()->get_basepath();

        $bc->execute_plan();
        $results = $bc->get_results();
        $backupfile = $results['backup_destination'];
        $bc->destroy();
        return array(
            'id' => $backupid,
            'basepath' => $backupbasepath,
            'file' => $backupfile
        );
    }

    /**
     * Restore a course that was backed up.
     * @param object $course containing fields: fullname, shortname, category
     * @param array $backup array as returned from $this->backup_course()
     * @param int $userid
     * @param bool $removebackupfile whether or not to remove the backup file after restoring
     * @param bool $log
     * @return int newcourseid
     * @throws base_logger_exception
     * @throws base_plan_exception
     * @throws base_setting_exception
     * @throws moodle_exception
     * @throws restore_controller_exception
     */
    public function restore_course($course, $backup, $userid, $removebackupfile=true, $log=false) {
        global $DB, $CFG;

        $backupfile = $backup['file'];
        // Check if we need to unzip the file because the backup temp dir does not contains backup files.
        if (!file_exists($backup['basepath'] . "/moodle_backup.xml")) {
            $backupfile->extract_to_pathname(get_file_packer('application/vnd.moodle.backup'), $backup['basepath']);
        }

        $newcourseid = restore_dbops::create_new_course($course->fullname, $course->shortname, $course->category);

        $rc = new restore_controller(
            $backup['id'], $newcourseid, backup::INTERACTIVE_NO,
            backup::MODE_SAMESITE, $userid, backup::TARGET_NEW_COURSE
        );

        if ($log) {
            $rc->get_logger()->set_next(new output_indented_logger(backup::LOG_DEBUG, false, true));
        }

        foreach ($this->options as $name => $value) {
            $setting = $rc->get_plan()->get_setting($name);
            $setting->set_status(backup_setting::NOT_LOCKED);
            $setting->set_value($value);
        }

        if (!$rc->execute_precheck()) {
            $precheckresults = $rc->get_precheck_results();
            if (is_array($precheckresults) && !empty($precheckresults['errors'])) {
                if (empty($CFG->keeptempdirectoriesonbackup)) {
                    fulldelete($backup['basepath']);
                }
                $errorinfo = '';
                foreach ($precheckresults['errors'] as $error) {
                    $errorinfo .= ' ' . $error;
                }
                if (array_key_exists('warnings', $precheckresults)) {
                    foreach ($precheckresults['warnings'] as $warning) {
                        $errorinfo .= ' ' . $warning;
                    }
                }
                $rc->destroy();
                throw new moodle_exception('errorrestoreprecheck', 'local_courseduplication', '', $errorinfo);
            }
        }

        // Executing restoration will copy content (sections, activities, ...)
        // But it will override the data defined in the duplication_form.
        // It also uses the default course format options.
        $rc->execute_plan();
        $rc->destroy();

        // So, update the record afterward with our custom values.
        $course->id = $newcourseid;
        $DB->update_record('course', $course);

        // Also update course format option (weeks -> automaticenddate).
        $cfo = $DB->get_record('course_format_options', array(
            'courseid' => $course->id,
            'name' => 'automaticenddate'
        ));
        $DB->update_record('course_format_options', (object)array(
            'id' => $cfo->id,
            'value' => $course->automaticenddate
        ));

        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($backup['basepath']);
        }
        if ($removebackupfile) {
            $backupfile->delete();
        }
        return $newcourseid;
    }

    /**
     * Create new groups, copied from existing $groupidlist, in the course identified
     * by $courseid.
     *
     * @param $courseid
     * @param $groupidlist
     * @return array
     * @throws dml_exception
     * @throws coding_exception
     */
    public function copy_groups($courseid, $groupidlist) {
        global $DB;
        $errors = array();

        $groups = $DB->get_records_list('groups', 'id', $groupidlist);

        foreach ($groups as $group) {
            $groupdata = new stdClass();
            $groupdata->courseid = $courseid;

            $groupdata->name = $group->name;
            $groupdata->description = $group->description;
            $groupdata->descriptionformat = $group->descriptionformat;
            // Also available in $group:
            // enrolmentkey, picture, hidepicture.
            // Unwanted or not taken upon group creation:
            // id, courseid, idnumber, timecreated, timemodified.

            try {
                groups_create_group($groupdata);
            } catch (Exception $e) {
                $string = new lang_string('warningcopygroupfailed', 'local_courseduplication', array(
                    'groupname' => $group->name,
                    'reason' => $e->getMessage(),
                ));
                $errors[] = $string->out();
            }
        }
        return $errors;
    }

    public function enrol_from_roles($basecourseid, $newcourseid, $roleidlist) {
        // Get users enrolled in the basecourse with a role included in $roleidlist.
        $users = array();
        foreach ($roleidlist as $roleid) {
            if ($roleusers = get_role_users($roleid, context_course::instance($basecourseid))) {
                $users[$roleid] = $roleusers;
            }
        }

        // Enrol these users in the new course.
        $errors = array();
        foreach ($users as $roleid => $roleusers) {
            foreach ($roleusers as $user) {
                if (!enrol_try_internal_enrol($newcourseid, $user->id, $roleid)) {
                    $string = new lang_string('warningenrollingfailed', 'local_courseduplication', array(
                        "userid" => $user->id,
                        "roleid" => $roleid,
                    ));
                    $errors[] = $string->out();
                }
            }
        }
        return $errors;
    }
}

class local_course_duplication_queue {

    const STATUS_QUEUED  = 0;
    const STATUS_RUNNING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAILED  = 3;

    protected $runid;

    /**
     * Retrieve the queued task data, some are course attributes but not necessarily.
     * @param $userid int The user who submitted the duplication form.
     * @param $data stdClass The duplication form data
     * @throws dml_exception
     */
    public static function queue($userid, $data) {
        global $DB;
        $duplication = new stdClass();
        $duplication->courseid = $data->id;
        $duplication->categoryid = $data->categoryid;
        $duplication->status = self::STATUS_QUEUED;
        $duplication->userid = $userid;
        $duplication->fullname = $data->targetfullname;
        $duplication->shortname = $data->targetshortname;
        $duplication->startdate = $data->targetstartdate;
        $duplication->enddate = $data->targetenddate;
        $duplication->automaticenddate = $data->targetautomaticenddate;
        $duplication->coursegroups = serialize($data->coursegroups);
        $duplication->enrolfromroles = serialize($data->enrolfromroles);

        $DB->insert_record('courseduplication_queue', $duplication);
    }

    public function __construct() {
        $this->runid = uniqid(getmypid(), true);
    }

    /**
     * Process the queue and launch job processes sequentially
     */
    public function process_queue() {
        global $DB;
        $info = array(
            'succeeded' => array(),
            'failed' => array()
        );

        $queued = $this->get_queued_jobs();
        if (!count($queued)) {
            return;
        }

        foreach ($queued as $job) {

            if (!$userlang = $DB->get_field('user', 'lang', array('id' => $job->userid))) {
                $userlang = 'en';
            }

            list($status, $errors, $warnings, $newcourseid) = $this->process_job($job, $userlang);

            if ($status == self::STATUS_FAILED) {
                $info['failed'][] = "course id $job->courseid to category $job->categoryid";
                $info['failed'] = array_merge($info['failed'], $errors);

                $event = \local_courseduplication\event\duplication_failed::create(
                    array(
                        'objectid' => $job->courseid,
                        'context' => context_course::instance($job->courseid),
                        'other' => array(
                            'newcategoryid' => $job->categoryid,
                        )
                    ));
                $event->trigger();
                // Database connection may have been reset if there was a failure.  So pull in $DB
                // from the globals again.
                global $DB;
            } else {
                $info['succeeded'][] = "course id $job->courseid to category $job->categoryid (new course id: $newcourseid)";

                $event = \local_courseduplication\event\duplication_succeeded::create(
                    array(
                        'objectid' => $job->courseid,
                        'context' => context_course::instance($job->courseid),
                        'other' => array(
                            'newcourseid' => $newcourseid,
                            'newcategoryid' => $job->categoryid,
                        )
                    ));
                $event->trigger();
            }
            $this->send_mail($job, $status, $errors, $warnings, $newcourseid, $userlang);
            $DB->delete_records('courseduplication_queue', array('id' => $job->id));
        }
        return $info;
    }

    protected function send_mail($job, $status, $errors, $warnings, $newcourseid, $userlang) {
        global $DB;

        $a = new stdClass();
        $oldcourse = $DB->get_record('course', array('id' => $job->courseid), 'id, fullname, shortname');
        $a->oldfullname = $oldcourse->fullname;
        $category = $DB->get_record('course_categories', array('id' => $job->categoryid), 'id, name');
        $a->categoryname = $category->name;

        if (count($errors)) {
            $errorstr = new lang_string('error', 'error', null, $userlang);
            $a->errors = $errorstr . ":\n * " . implode("\n * ", $errors);

            $event = \local_courseduplication\event\duplication_errors::create(
                array(
                    'objectid' => $job->courseid,
                    'context' => context_course::instance($job->courseid),
                    'other' => array(
                        'newcategoryid' => $job->categoryid,
                        'errors' => $a->errors,
                    )
                ));
            $event->trigger();
        } else {
            $a->errors = '';
        }
        if (count($warnings)) {
            $warningstr = new lang_string('warning', 'moodle', null, $userlang);
            $a->warnings = $warningstr . ":\n * " . implode("\n * ", $warnings);
            $event = \local_courseduplication\event\duplication_warnings::create(
                array(
                    'objectid' => $job->courseid,
                    'context' => context_course::instance($job->courseid),
                    'other' => array(
                        'newcategoryid' => $job->categoryid,
                        'warnings' => $a->warnings,
                    )
                ));
            $event->trigger();
        } else {
            $a->warnings = '';
        }

        $subjectstringkey = 'mailduplicationfailsubject';
        $bodystringkey = 'mailduplicationfailbody';

        if ($status == self::STATUS_SUCCESS) {
            $subjectstringkey = 'mailduplicationsuccesssubject';
            $bodystringkey = 'mailduplicationsuccessbody';

            $newcourse = $DB->get_record('course', array('id' => $newcourseid), 'id, fullname, shortname, category');
            $a->newfullname = $newcourse->fullname;
            $url = new moodle_url('/course/view.php', array('id' => $newcourse->id));
            $a->newcourseurl = $url->out();
        }

        $subject = new lang_string($subjectstringkey, 'local_courseduplication', $a, $userlang);
        $body = new lang_string($bodystringkey, 'local_courseduplication', $a, $userlang);

        $user = $DB->get_record('user', array('id' => $job->userid));

        email_to_user($user, \core_user::get_noreply_user(), $subject, $body);
    }

    /**
     * Gets queued jobs
     *
     * This marks the jobs in the table with a run id, so that no other process
     * tries to simultaneously process the same job.
     */
    protected function get_queued_jobs() {
        global $DB;
        $queued = $DB->get_records('courseduplication_queue', array('status' => self::STATUS_QUEUED));
        if (!count($queued)) {
            return array();
        }
        foreach ($queued as $job) {
            $job->status = self::STATUS_RUNNING;
            $job->runid  = $this->runid;
            $DB->update_record('courseduplication_queue', $job);
        }
        return $DB->get_records('courseduplication_queue',
            array('status' => self::STATUS_RUNNING, 'runid' => $this->runid));
    }

    /**
     * Process a job from the queue
     * @param $job
     * @param string $stringlang
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function process_job($job, $stringlang='en') {
        global $DB;

        $status = 0;
        $errors = 1;
        $warnings = 2;
        $courseid = 3;

        $return = array(
            $status => self::STATUS_FAILED,
            $errors => array(),
            $warnings => array(),
            $courseid => 0
        );

        // Course to duplicate.
        if (!$course = $DB->get_record('course', array('id' => $job->courseid))) {
            $return[$errors][] = \
                get_string('duplicatefailedbackup', 'local_courseduplication') . ': ' . get_string('invalidcourseid');
        }

        // Target category.
        if (!$category = $DB->get_record('course_categories', array('id' => $job->categoryid))) {
            $string1 = new lang_string('duplicatefailedbackup', 'local_courseduplication', null, $stringlang);
            $string2 = new lang_string('errornosuchcategory', 'local_courseduplication', null, $stringlang);
            $return[$errors][] = $string1 . ': ' . $string2;
        }

        if (count($return[$errors])) {
            return $return;
        }

        if (!has_capability('local/courseduplication:restore_course', context_coursecat::instance($category->id))) {
            $return[$errors][] = get_string('errornopermsintarget', 'local_courseduplication');
            return $return;
        }

        $admin = get_admin();
        $dup = new local_courseduplication_controller();

        try {
            $backup = $dup->backup_course($course->id, $admin->id);
        } catch (Exception $e) {
            $return[$errors][] = get_string('duplicatefailedbackup', 'local_courseduplication');
            $return[$errors][] = $e->getMessage();
            debugging($e->getMessage());
            $this->database_reconnect();
            return $return;
        }

        $newcourse = new stdClass();
        $newcourse->fullname = $job->fullname;
        $newcourse->shortname = $job->shortname;
        $newcourse->category = $category->id;

        $newcourse->startdate = $job->startdate;
        $newcourse->enddate = $job->enddate;
        $newcourse->automaticenddate = $job->automaticenddate;

        try {
            $return[$courseid] = $dup->restore_course($newcourse, $backup, $admin->id);
        } catch (Exception $e) {
            $return[$errors][] = get_string('duplicatefailedrestore', 'local_courseduplication');
            $return[$errors][] = $e->getMessage();
            debugging($e->getMessage());
            $this->database_reconnect();
            return $return;
        }

        $return[$warnings] = $dup->copy_groups($return[$courseid], unserialize($job->coursegroups));

        array_merge(
            $return[$warnings],
            $dup->enrol_from_roles($job->courseid, $return[$courseid], unserialize($job->enrolfromroles))
        );

        if ($job->automaticenddate === '1') {
            format_weeks::update_end_date($return[$courseid]);
        }

        $return[$status] = self::STATUS_SUCCESS;

        return $return;
    }

    /**
     * Reconnect to the Moodle database.  This drops any temp tables
     * which were created during a backup / restore process.
     *
     * Note that it will be necessary to re-initialize any references to the global $DB
     * in call-stack ancestor functions after this is called.
     */
    protected function database_reconnect() {
        global $DB;
        $DB->dispose();
        // @codingStandardsIgnoreLine .
        $GLOBALS['DB'] = null;
        setup_DB();
    }

}
