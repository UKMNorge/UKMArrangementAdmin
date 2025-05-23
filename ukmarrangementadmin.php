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
        add_action('wp_ajax_UKMArrangementAdmin_ajax', ['UKMArrangementAdmin', 'ajax']);

        // self::scripts_and_styles(); // Attach scripts and styles hook
    }
    /**
     * Håndterer alle ajax-kall
     *
     * @return void
     */
    public static function ajax()
    {   
        $reques_method = $_SERVER['REQUEST_METHOD'];
        $subAction = $_REQUEST['controller'];

        try {
            require_once('ajax/' . $subAction . '.ajax.php');

        // Noe gikk galt
        } catch (Exception $e) {
            static::addResponseData('success', false);
            static::addResponseData('message', $e->getMessage());
            static::addResponseData('code', $e->getCode());
        }

        $data = json_encode(static::getResponseData());
        echo $data;
        die();
    }

    /**
     * Render menu
     */
    public static function meny() {
        // Placeholder for menu rendering
        $page = add_submenu_page(
            'index.php',
            'Home Landsfestivalen', 
            'Home Landsfestivalen', 
            'editor', 
            'UKMArrangementAdmin', 
            ['UKMArrangementAdmin', 'renderAdmin']
        );

        add_action(
            'admin_print_styles-' . $page,
            ['UKMArrangementAdmin', 'scripts_and_styles']
        );
    }

    /**
     * Scripts and styles for admin pages
     */
    public static function scripts_and_styles() {
        // Remove empty submenu
        wp_enqueue_style('UKMStatistikkVueMIDIcons', 'https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');

        wp_enqueue_style('UKMArrangementAdminVueStyle', plugin_dir_url(__FILE__) . '/client/dist/assets/build.css', []);
        wp_enqueue_script('UKMArrangementAdminVueJs', plugin_dir_url(__FILE__) . '/client/dist/assets/build.js', [], false, true);
    }
}

UKMArrangementAdmin::init(__DIR__);
UKMArrangementAdmin::hook();
