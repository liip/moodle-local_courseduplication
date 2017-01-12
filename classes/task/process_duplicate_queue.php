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
 * @copyright 2017 Liip <https://www.liip.ch/>
 * @author Didier Raboud <didier.raboud@liip.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_courseduplication\task;

class process_duplicate_queue extends \core\task\scheduled_task {
    public function get_name() {
        // Shown in admin screens
        return get_string('process_duplicate_queue', 'local_courseduplication');
    }

    public function execute() {
        require_once(dirname(dirname(dirname(__FILE__))) . '/locallib.php');

        $queue = new \local_course_duplication_queue();
        echo "\n\n";
        local_courseduplication_dated_message('Processing course duplication queue');
        $info = $queue->process_queue();
        local_courseduplication_dated_message('Queue processed.  Results:');

        $processedcount = count($info['succeeded']) + count($info['failed']);
        if ($processedcount == 0) {
            echo "No jobs processed\n";
        } else {
            echo "$processedcount jobs processed\n";
            if (count($info['succeeded'])) {
                echo "\nSucceeded: \n";
                foreach ($info['succeeded'] as $detail) {
                    echo '  *  ' . $detail . "\n";
                }
            }
            if (count($info['failed'])) {
                echo "\nFailed: \n";
                foreach ($info['failed'] as $detail) {
                    echo '  *  ' . $detail . "\n";
                }
            }
        }
    }
}
