# local/courseduplication - A Moodle asynchronous course duplication plugin

This plugin works by allowing some users to schedule course duplications.  These are then processed asynchronously by a Moodle scheduled task which will perform a backup and then a restore of that course in the requested category.

## Capabilities

The advantage of that approach is that users which do not necessarily have standard backup and/or restore capabilites can be allowed to perform course duplication on their own.

Two capabilities are used to control who can duplicate courses:
* `local/courseduplication:backup_course`
* `local/courseduplication:restore_course`

## Debugging

You can force the tasks to be executed from the commandline via:
> sudo -u www-data php admin/tool/task/cli/schedule_task.php  --execute='\local_courseduplication\task\process_duplicate_queue'

# Authors

This plugin was developed by Liip AG <https://www.liip.ch/>, the swiss Moodle Partner.
