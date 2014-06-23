<?php

$string['pluginname'] = 'Course duplication';
$string['courseduplication'] = 'Course duplication';
$string['courseduplication:backup_course'] = 'Ability to select a course for duplication in the course administration menu';
$string['courseduplication:restore_course'] = 'Ability to duplicate a course into a course category';

$string['duplicate'] = 'Duplicate';
$string['targetcategory'] = 'Target category';


// Strings seen by the end user teachers who perform course duplication:
$string['duplicatecourse'] = 'Duplicate course';
$string['duplicatecourseheader'] = 'Duplicate course: {$a->fullname} ({$a->shortname})';
$string['duplicatedesc'] = 'This tool allows you to duplicate your Moodle course to a different category. This is useful when you wish to use the same courses activities (documents, assignments, etc.) in a new course session for the next academic period. Please note:
<ul>
    <li>All your activities will be copied,</li>
    <li>No student information will be copied, no students will be enrolled in the new Moodle course,</li>
    <li>You will have to access the new course settings to set its enrolment key.</li>
</ul>';
$string['duplicationwillbescheduled'] = 'Your course duplication request will be scheduled for execution.  You will receive an email when this is complete.  Normally, this takes at least 5 minutes.';
$string['duplicatefailedcoursenames'] = 'The duplicate did not complete successfully, an error occured upon new course names creation';
$string['nobackupsite'] = 'Sorry, but it\'s not permitted to duplicate the site course';
$string['errornopermsintarget'] = 'You do not have permissions to create courses in the selected target category';
$string['errormissingcategory'] = 'A category must be specified';
$string['errorrestoreprecheck'] = 'Unable to continue because the restore pre-check revealed problems: {$a}';

$string['duplicationscheduled'] = '<p>This course has been scheduled for duplication.  As soon as the course has been duplicated, you will receive an email notification.</p><p>This generally happens within 5 minutes, but can take longer if several courses are being duplicated at the same time, or if the system is unusually busy.</p>';

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
