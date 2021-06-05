<?php 
/**
* Plugin Name
*
* @package           PluginPackage
* @author            Your Name
* @copyright         2019 Your Name or Company Name
* @license           GPL-2.0-or-later
*
* @wordpress-plugin
* Plugin Name:       NEW DB
* Plugin URI:        https://example.com/plugin-name
* Description:       Description of the plugin.
* Version:           2.0.0
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Shafique Uddin
* Author URI:        https://example.com
* Text Domain:       all_in_one
* License:           GPL v2 or later
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/



global $owntable_version;
$owntable_version = '2.0';


function own_db_function(){
    global $wpdb;
    global $owntable_version;

    $table_name = $wpdb->prefix."shafique";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id INT(250) NOT NULL AUTO_INCREMENT,
        name VARCHAR(250) NOT NULL,
        email VARCHAR(250) NOT NULL,
        gpa INT(250) NOT NULL,
        PRIMARY KEY (id)
    )$charset_collate;";

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta( $sql );
    add_option('shafique table version', $owntable_version);
}
register_activation_hook( __FILE__, 'own_db_function' );
    
function custom_data_insert(){
    global $wpdb;
    $welcome_name = 'Mr. WordPress';
    
    $email = 'Congratulations, you just completed the installation!';
    
    $table_name = $wpdb->prefix."shafique";
    
    $wpdb->insert( 
        $table_name, 
        array( 
            'name' => $welcome_name, 
            'email' => $email, 
            'gpa' => 3.63
        )
    );
}

register_activation_hook( __FILE__, 'custom_data_insert' );



?>