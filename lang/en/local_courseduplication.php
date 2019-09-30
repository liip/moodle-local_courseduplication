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

$string['pluginname'] = 'Course duplication';
$string['courseduplication'] = 'Course duplication';
$string['courseduplication:backup_course'] = 'Ability to select a course for duplication in the course administration menu';
$string['courseduplication:restore_course'] = 'Ability to duplicate a course into a course category';

$string['duplicate'] = 'Duplicate';
$string['targetcategory'] = 'Target category';


// Strings seen by the end user teachers who perform course duplication.
$string['duplicatecourse'] = 'Duplicate course';
$string['duplicatecourseheader'] = 'Duplicate course: {$a->fullname} ({$a->shortname})';
$string['duplicatedesc'] = '<p>This tool allows you to duplicate your Moodle course to a different category. This is useful when you wish to use the same courses activities (documents, assignments, etc.) in a new course session for the next academic period.</p><p>Please note:</p>
<ul>
    <li>All your activities will be copied ;</li>
    <li>No student information will be copied ;</li>
    <li>Only participants of the selected role(s) will be enrolled in the new course ;</li>
    <li>You will have to access the new course settings to set its enrolment key.</li>
</ul>';
$string['duplicationwillbescheduled'] = 'Your course duplication request will be scheduled for execution.  You will receive an email when this is complete.  By default, this is configured to happen every 15 minutes.';
$string['duplicatefailedcoursenames'] = 'The duplicate did not complete successfully, an error occured upon new course names creation';
$string['nobackupsite'] = 'Sorry, but it\'s not permitted to duplicate the site course';
$string['errornopermsintarget'] = 'You do not have permissions to create courses in the selected target category';
$string['errormissingcategory'] = 'A category must be specified';
$string['errorrestoreprecheck'] = 'Unable to continue because the restore pre-check revealed problems: {$a}';

$string['duplicationscheduled'] = '<p>This course has been scheduled for duplication.  As soon as the course has been duplicated, you will receive an email notification.</p><p>By default, this is configured to happen every 15 minutes, but can take longer if several courses are being duplicated at the same time, or if the system is unusually busy.</p>';

$string['mailduplicationsuccesssubject'] = 'Course duplication finished successfully';
$string['mailduplicationsuccessbody'] = 'The course "{$a->oldfullname}" has been duplicated into the category "{$a->categoryname}".  You can access the new course "{$a->newfullname}" with this URL: {$a->newcourseurl}

{$a->warnings}
';
$string['mailduplicationfailsubject'] = 'Course duplication failed';
$string['mailduplicationfailbody'] = 'The attempt to duplicate the course "{$a->oldfullname} into the category "{$a->categoryname}" was unsuccessful.  A record of the errors encountered can be found below.

{$a->warnings}

{$a->errors}
';
$string['warningenrollingfailed'] = 'Unable to enrol user id {$a->userid} with role {$a->roleid}';
$string['duplicatefailedbackup'] = 'The duplicate did not complete successfully, the backup failed';
$string['duplicatefailedrestore'] = 'The duplicate did not complete successfully, the restore failed';
$string['errornosuchcategory'] = 'Target category not found';
$string['warningcopygroupfailed'] = 'Unable to copy the group "{$a->groupname}". Reason: ${a->reason}';
// Cron jobs.
$string['process_duplicate_queue'] = 'Process duplicate queue';
// Events.
$string['event_duplication_succeeded'] = 'Course duplication succeeded';
$string['event_duplication_failed'] = 'Course duplication failed';
$string['event_duplication_errors'] = 'Course duplication had errors';

$string['enrolfromroles'] = 'Enrol users having role(s)';
$string['enrolfromroles_excluded'] = 'Role(s) selected for enrolment must also be selected in "Course roles" field for creation.';
$string['enrolfromroles_help'] = 'Automatically enrol (in the new course) the users that have the following roles (in the base course). If you leave "No selection", no enrolment will be replicated.';
$string['coursegroups'] = 'Course groups';
$string['coursegroups_help'] = 'Create these groups in the new course';
