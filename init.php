<?php

$conflictFiles = [
    'config/db_doctrine.php',
    'container/dbDoctrineConf.php'
];

foreach ($conflictFiles as $conflictFile) {
    if (file_exists($conflictFile)) {
        unlink($conflictFile);
    }
}
