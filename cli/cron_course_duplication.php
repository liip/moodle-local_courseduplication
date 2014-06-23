<?php
define('CLI_SCRIPT', true);
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../locallib.php');

function local_courseduplication_dated_message($message, $dateformat='Y-m-d H:i:s') {
    $now = date($dateformat);
    echo "[$now] $message\n";
}

cron_setup_user();

$queue = new local_course_duplication_queue();
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

