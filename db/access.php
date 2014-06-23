<?php
$capabilities = array(
    'local/courseduplication:backup_course' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            // should probably be assigned to "EHL teacher" role
        ),
    ),
    'local/courseduplication:restore_course' => array(
        'riskbitmask' => RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'archetypes' => array(
            // should probably be assigned to "Course Creator (EHL)" role
        ),
    ),
);
