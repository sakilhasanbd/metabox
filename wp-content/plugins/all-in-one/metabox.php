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
* Plugin Name:       All In One
* Plugin URI:        https://example.com/plugin-name
* Description:       Description of the plugin.
* Version:           1.0.0
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Shafique Uddin
* Author URI:        https://example.com
* Text Domain:       all_in_one
* License:           GPL v2 or later
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/



/**
 * DataBase Connection Function
 */
global $owntable_version;
$owntable_version = '1.0';

 

// Admin Bootstrap and javaScript

function load_admin_css_javaScript(){
    wp_enqueue_style( 'css-grid-handler', plugins_url('inc/css/bootstrap-grid.css',__FILE__));
}
add_action( 'admin_enqueue_scripts', 'load_admin_css_javaScript');



 
/**
* Activate the plugin.
*/
function callback_custom_plugin_own() { 
    // Trigger our function that registers the custom post type plugin.
    activation_own_function();
    // Clear the permalinks after the post type has been registered.
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'callback_custom_plugin_own');
 
 
 
/**
* Deactivation hook.
*/
function callback_custom_plugin_own_deactivate() {
    // Unregister the post type, so the rules are no longer in memory.
    unregister_post_type( 'detailsOfUniversity' );
    // Clear the permalinks to remove our post type's rules from the database.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'callback_custom_plugin_own_deactivate' );




    // Custom Post type Name = Post Details
    function activation_own_function() {
        register_post_type('postdetailsId',
            array(
                'labels'      => array(
                    'name'          => __('Post Detail'),
                    'singular_name' => __('Post Details'),
                ),
                'public'      => true,
                'has_archive' => true,
            )
        );
    }
    add_action('init', 'activation_own_function');



    // Custom Meta Box
    function own_meta_box_details() {
        $screens = [ 'postdetailsId' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'post_details_metabox_id',                 // Unique ID
                'Post Details',      // Box title
                'custom_form_html_handler',  // Content callback, must be of type callable
                $screen                            // Post type
            );
        }
    }
    add_action( 'add_meta_boxes', 'own_meta_box_details' );




    // HTML Form Output
    function custom_form_html_handler($post){
        $get_user_name = get_post_meta( $post->ID, 'post_details_user_name', true );
        $get_user_email = get_post_meta($post->ID, 'post_details_user_email', true);
        $get_user_gender = get_post_meta($post->ID, 'post_details_user_gender', true);
        $get_user_eQualification = get_post_meta($post->ID, 'post_details_user_eQualification', false);
        $get_user_sscGpa = get_post_meta( $post->ID, 'post_details_user_sscGpa', true);
        
        // var_dump($get_user_sscGpa);
        wp_nonce_field( 'post_details_location', 'post_details_field' );
        
        ?>
        <p> name please <input type="text" name="name" value="<?php echo $get_user_name; ?>"> </p>
    <p> Email please <input type="text" name="email" value="<?php echo $get_user_email; ?>"> </p>

    <p>Gender 
    <?php 
        $gender = array('male','female');
        foreach ($gender as $key => $value) {
            $is_gender_checked = ("$value" == "$get_user_gender") ?  "checked" : " " ;
            ?>
    <input type="radio" <?php echo $is_gender_checked; ?> name="gender" value="<?php echo $value; ?>" id="<?php echo $value; ?>"><label for="<?php echo $value; ?>"><?php echo ucfirst($value); ?></label>
    <?php }   ?>

    </P>
    <P>
    Education 
    <?php 
        $educational_degree = array('ssc', 'hsc', 'honurs', 'masters');

        $is_eQualification_checked = ' ';
        foreach ($educational_degree as $key => $education_qualification) {
            foreach ($get_user_eQualification as $key => $multiDymeQulificValue) {
                $is_eQualification_checked = (in_array($education_qualification, $multiDymeQulificValue)) ? 'checked' : ' ' ;
            }


            ?>
    <input <?php echo $is_eQualification_checked; ?> type="checkbox" value="<?php echo $education_qualification;?>" name="education[]" id="<?php echo $education_qualification?>"><label for="<?php echo $education_qualification?>"><?php echo $education_qualification?></label>
    <?php }   ?>
    </P>
    <p>
    <label for="ssgpa">Your SSC GPA</label>
    <input type="text" name="sscgpa" id="ssgpa" step="0.01" min="0" max="5" value="<?php echo $get_user_sscGpa;?>">
    </p>

        <?php
    }


    // input data checking
    function form_checking($nonce_field, $action, $post_id){
        $nonce = isset( $_POST[ $nonce_field ] ) ? $_POST[ $nonce_field ] : '';

        if ( $nonce == '' ) {
			return false;
		}
		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if ( wp_is_post_autosave( $post_id ) ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		return true;

    }



    // Save Data
    function custm_metabox_data_save_function($post_id) {

        if(!form_checking('post_details_field', 'post_details_location', $post_id)){
            return $post_id;
        }

        $name_field = (isset($_POST['name'])) ? ($_POST['name']) : '' ;
        if($name_field == ''){
            return $post_id;
        }
        
        if ( array_key_exists( 'name', $_POST ) ) {
            update_post_meta(
                $post_id,
                'post_details_user_name',
                $_POST['name']
            );
        }

        if ( array_key_exists( 'email', $_POST ) ) {
            update_post_meta(
                $post_id,
                'post_details_user_email',
                $_POST['email']
            );
        }

        if ( array_key_exists( 'gender', $_POST ) ) {
            update_post_meta(
                $post_id,
                'post_details_user_gender',
                $_POST['gender']
            );
        }

        if ( array_key_exists( 'education', $_POST ) ) {
            update_post_meta(
                $post_id,
                'post_details_user_eQualification',
                $_POST['education']
            );
        }

        if (array_key_exists('sscgpa', $_POST)){
            update_post_meta(
                $post_id,
                'post_details_user_sscGpa',
                $_POST['sscgpa']
            );
        }
    }
    add_action('save_post', 'custm_metabox_data_save_function', 10, 1);



?>