<?php
/*
  Plugin Name: Registers_users
  Plugin URI:http://themer.ir
  Description:این افزون برای ثبت نام کابران می باشه 
  Version: 1.0
  Author:امین ساکی   
  Author URI:www.webscript.blog.com
  License:GPL v2
 */
 
ob_start();
defined( 'ABSPATH' ) || exit;
define( 'VIP_DIR', plugin_dir_path( __FILE__ ) );
define( 'VIP_INC_DIR', trailingslashit( VIP_DIR . 'inc' ) );
define( 'VIP_URL', plugin_dir_url( __FILE__ ) );
define( 'VIP_CSS_URL', trailingslashit( VIP_URL . 'css' ) );
define( 'VIP_JS_URL', trailingslashit( VIP_URL . 'js' ) );
define( 'VIP_IMG_URL', trailingslashit( VIP_URL . 'img' ) );

include_once VIP_INC_DIR.'frontend.php';
include_once VIP_INC_DIR.'shortcodes.php';

if(is_admin()){
   include_once VIP_INC_DIR.'backend.php'; 
   include_once VIP_INC_DIR.'pages.php';
   include_once VIP_INC_DIR.'ajax.php';
   include_once VIP_INC_DIR.'metaboxes.php';
   add_action('admin_menu','vip_add_menus');
}
register_activation_hook(__FILE__,'vip_activate');
register_deactivation_hook(__FILE__,'vip_deactivate');
function vip_activate(){
    global $table_prefix;
    $products_sql='CREATE TABLE IF NOT EXISTS `'.$table_prefix.'vip_products` (
                `product_ID` bigint(10) NOT NULL AUTO_INCREMENT,
                `product_name` varchar(30) COLLATE utf8_persian_ci NOT NULL,
                `product_title` varchar(100) COLLATE utf8_persian_ci NOT NULL,
                `product_price` bigint(10) NOT NULL,
                `product_days` int(5) NOT NULL,
                `product_discount` int(10) DEFAULT 0,
                `product_status` tinyint(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`product_ID`)
              ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci AUTO_INCREMENT=1';
    $transacions_sql='CREATE TABLE IF NOT EXISTS `'.$table_prefix.'vip_transactions` (
                    `trans_ID` bigint(20) NOT NULL AUTO_INCREMENT,
                    `trans_user_ID` bigint(20) NOT NULL,
                    `trans_user_phone` varchar(11) COLLATE utf8_persian_ci DEFAULT NULL,
                    `trans_product_ID` bigint(20) NOT NULL,
                    `trans_bank` varchar(20) COLLATE utf8_persian_ci NOT NULL,
                    `trans_order_ID` varchar(100) COLLATE utf8_persian_ci NOT NULL,
                    `trans_ref_ID` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
                    `trans_amount` bigint(20) NOT NULL,
                    `trans_date` datetime NOT NULL,
                    `trans_status` tinyint(1) NOT NULL,
                    `trans_settle` tinyint(1) DEFAULT NULL,
                    PRIMARY KEY (`trans_ID`)
                  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci AUTO_INCREMENT=1';
    $notices_sql='CREATE TABLE IF NOT EXISTS `'.$table_prefix.'vip_notices` (
                    `notice_id` bigint(20) NOT NULL AUTO_INCREMENT,
                    `notice_title` varchar(300) COLLATE utf8_persian_ci NOT NULL,
                    `notice_content` text COLLATE utf8_persian_ci NOT NULL,
                    `notice_date` datetime NOT NULL,
                    PRIMARY KEY (`notice_id`)
                  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci AUTO_INCREMENT=1';
    require_once ABSPATH.'/wp-admin/includes/upgrade.php';
    dbDelta($products_sql);
    dbDelta($transacions_sql);
    dbDelta($notices_sql);
}

add_action('init','vip_session_start');

add_action('wp_logout','vip_session_end');

add_action('wp_login','vip_session_end');

function vip_session_start() {
    if(!session_id()) {
        session_start();
    }
}
function vip_session_end() {
    if(session_id())
    {
        $_SESSION=array();
        session_destroy ();
    }
}
add_action('init','vip_check_user_expire',20);