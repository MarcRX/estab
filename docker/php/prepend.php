<?php

/**
 * PHP-FPM does not use the requested script's directory as the working
 * directory. This application includes files with paths such as ./4fcfg/.
 */
if (PHP_SAPI !== 'cli' && !empty($_SERVER['SCRIPT_FILENAME'])) {
    chdir(dirname($_SERVER['SCRIPT_FILENAME']));
}
