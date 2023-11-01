<?php

/**
 * Object table
 *
 * @package           ObjectTablePlugin
 * @author            Conduction
 * @copyright         2023 Conduction
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       ObjectTable
 * Description:       De object table plugin
 * Version:           1.0.8
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Conduction
 * Author URI:        https://conduction.nl
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

use CommonGateway\ObjectTable\Autoloader;
use CommonGateway\ObjectTable\Foundation\Plugin;

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

/**
 * Manual loaded file: the autoloader.
 */
require_once __DIR__ . '/autoloader.php';
$autoloader = new Autoloader();

/**
 * Begin execution of the plugin.
 */
$plugin = (new Plugin(__DIR__))->boot();

/**
 * Start session on init when there is none.
 */
add_action('init', function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
});
