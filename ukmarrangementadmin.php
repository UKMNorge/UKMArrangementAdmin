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
    public static function hook(){
    }

    /**
     * Rendre meny
     *
     */
    public static function meny(){
    }

    /**
     * Scripts and styles for non-network admin
     *
     */
    public static function scripts_and_styles(){
    }
}

UKMArrangementAdmin::init(__DIR__);