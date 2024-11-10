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
 * @copyright 2017-2018 Liip AG <https://www.liip.ch/>
 * @author Didier Raboud <didier.raboud@liip.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_courseduplication\task;

defined('MOODLE_INTERNAL') || die();

class process_duplicate_queue extends \core\task\scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('process_duplicate_queue', 'local_courseduplication');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG;

        require_once($CFG->dirroot . '/local/courseduplication/locallib.php');

        $queue = new \local_course_duplication_queue();
        mtrace('Processing course duplication queue …');
        $info = $queue->process_queue();

        if (!is_array($info)) {
            return;
        }

        $processedcount = count($info['succeeded']) + count($info['failed']);
        mtrace("Queue processed: $processedcount jobs processed.");
        if (count($info['succeeded'])) {
            mtrace("Succeeded:");
            foreach ($info['succeeded'] as $detail) {
                mtrace('  *  ' . $detail);
            }
        }
        if (count($info['failed'])) {
            mtrace("Failed:");
            foreach ($info['failed'] as $detail) {
                mtrace('  *  ' . $detail);
            }
        }
    }
}
