<?php
$capabilities = array(
    'local/courseduplication:backup_course' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'coursecreator' => CAP_ALLOW,
            'manager'          => CAP_ALLOW
        ),
    ),
    'local/courseduplication:restore_course' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => array(
            'coursecreator' => CAP_ALLOW,
            'manager'          => CAP_ALLOW
        ),
    ),
);
