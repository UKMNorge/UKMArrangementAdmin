<?php
/* 
Plugin Name: UKM Arrangement Admin
Plugin URI: http://www.ukm.no
Description: UKM Arrangement Admin side for å administrere arrangementet
Author: UKM Norge / Kushtrim Aliu
Version: 1.0
Author URI: http://www.ukm.no
*/

use UKMNorge\Wordpress\Modul;

require_once('UKM/Autoloader.php');

class UKMArrangementAdmin extends Modul
{
    public static $action = 'placeholder';
    public static $path_plugin = null;

    /**
     * Register hooks
     */
    public static function hook() {
        add_action('admin_menu', ['UKMArrangementAdmin', 'meny']);
        self::scripts_and_styles(); // Attach scripts and styles hook
    }

    /**
     * Render menu
     */
    public static function meny() {
        // Placeholder for menu rendering
    }

    /**
     * Scripts and styles for admin pages
     */
    public static function scripts_and_styles() {
        add_action('admin_enqueue_scripts', function () {
        	wp_enqueue_style('UKMStatistikkVueMIDIcons', 'https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');

            wp_enqueue_style('UKMArrangementAdminVueStyle', plugin_dir_url(__FILE__) . '/client/dist/assets/build.css', []);
            wp_enqueue_script('UKMArrangementAdminVueJs', plugin_dir_url(__FILE__) . '/client/dist/assets/build.js', [], false, true);
        });
    }
}

UKMArrangementAdmin::init(__DIR__);
UKMArrangementAdmin::hook();
