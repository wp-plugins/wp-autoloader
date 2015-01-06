<?php

/**
 * Output admin notice about PHP version
 * @since 2.0.7
 */
function plg_wpautoloader_admin_notice_php_version () {
    $msg[] = '<div class="error"><p>';
    $msg[] = 'Please upgarde PHP at least to version 5.3.0!<br>';
    $msg[] = 'Your current PHP version is <strong>' . PHP_VERSION . '</strong>, which is not suitable for plugin <strong>WP Autoloader</strong>.';
    $msg[] = '</p></div>';
    echo implode( PHP_EOL, $msg );
}
