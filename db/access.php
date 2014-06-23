<?php
$capabilities = array(
    'local/courseduplication:backup_course' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'manager'          => CAP_ALLOW
            // should probably be assigned to "teacher" role
        ),
    ),
    'local/courseduplication:restore_course' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => array(
            'coursecreator' => CAP_ALLOW,
            'manager'          => CAP_ALLOW
            // should probably be assigned to "Course Creator" role
        ),
    ),
);
