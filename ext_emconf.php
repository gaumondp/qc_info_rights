<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "qc_info_rights"
 *
 * Auto generated by Extension Builder 2021-09-13
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'qc_info_rights',
    'description' => 'Gives editors a view of BE users and group rights like the Access module but in read-only. Also provides BE users and BE User Groups list with CSV export.',
    'category' => 'plugin',
    'author' => 'Quebec.ca/',
    'author_email' => '',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.0',
    'autoload' => [
        'psr-4' => [
            'Qc\QcInfoRights\\' => 'Classes',
        ],
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
