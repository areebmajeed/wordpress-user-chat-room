<?php

/**
 * @package WordPress_User_Chat_Room
 * @version 0.1
 */
/*
Plugin Name: WordPress User Chatroom
Plugin URI: https://github.com/areebmajeed/wordpress-user-chat-room/
Description: Enable a beautiful chat room for your registered blog users.
Author: Areeb
Version: 0.1
Author URI: http://areebmajeed.me/
*/

if (!defined('ABSPATH')) {

    exit("Not cool, okay?");

}

define('WUCR_PATH', plugin_dir_path(__FILE__));
require_once (WUCR_PATH . 'plugin_loader.php');

function register_wucr() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'wucr_chat';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  user text NOT NULL,
  date text NOT NULL,
  message varchar(301) NOT NULL,
  UNIQUE KEY id (id)
) $charset_collate";

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $wpdb->insert($table_name, array(

        'user' => 'Overfeat',
        'date' => date("Y-m-d H:i:s") ,
        'message' => 'If you are seeing this message, then it means that your plugin installation has been done successfully.'

    ));

    update_option("wucr_chat_colour", "2780E3");
    update_option("wucr_refresh_interval", 3);

}

function deregister_wucr() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'wucr_chat';
    $sql = "DROP TABLE $table_name";
    $wpdb->query($sql);

    delete_option("wucr_chat_colour");
    delete_option("wucr_refresh_interval");

}

register_activation_hook(__FILE__, 'register_wucr');
register_deactivation_hook(__FILE__, 'deregister_wucr');
