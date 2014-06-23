This plugin works by scheduling a course duplication (does a backup and restore).

A cron job must be configured to run local/courseduplication/cli/cron_course_duplication.php so
that the scheduled duplication actually happens.

This was implemented in this way because it was not wished that the users who can perform a
course duplication can directly backup and restore courses themselves.

Two capabilities are used to control who can duplicate which courses where:
* local/courseduplication:backup_course
* local/courseduplication:restore_course

