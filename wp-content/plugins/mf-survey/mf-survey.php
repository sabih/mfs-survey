<?php
/**
 * Mindfire Survey Plugin
 *
 * To add menu "Manage Survey" and submenu in admin section
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	mf-survey.php
 */

/*
Plugin Name:	Mindfire Survey
Plugin URI: 	http://www.mindfiresolutions.com/
Description: 	The mf-survey WordPress plugin lets you add Survey to your website
Version: 		1.0
Author: 		Sabih Ahmad Khan
Author URI: 	http://www.mindfiresolutions.com/
Text Domain: 	mf-survey
Domain Path: 	/lang
*/

/**
 * The function surveys_add_menu_link is hooked to admin_menu
 */
add_action( 'admin_menu', 'surveys_add_menu_link' );

/**
 * The function create_survey_page is hooked when plugin is activated
 */
register_activation_hook( __FILE__, 'create_survey_page' );

/**
 * The function change_post_status is hooked when plugin is deactivated
 */
register_deactivation_hook( __FILE__, 'change_post_status' );

/**
 * Includes install-uninstall-tables.php
 */
require_once( __DIR__ . '/install-uninstall-tables.php' );

/**
 * The surveys table are created when plugin is activated
 */
register_activation_hook( __FILE__, 'install' );

/**
 * The surveys table are dropped when plugin is uninstalled
 * Uncomment this hook for deleting all the surveys table
 */
//register_deactivation_hook( __FILE__ , 'uninstall' );

add_action( 'init', 'mf_survey_plugin_text_domain' );

function mf_survey_plugin_text_domain() {
    load_plugin_textdomain( 'mf-survey', false, 'mf-survey/lang' );
}

/**
 * @method : surveys_add_menu_link()
 * @return : void
 * @desc : Adds a menu and submenu under "Manage Survey"
 */
function surveys_add_menu_link() {
	
	$capability = 2;

	// Adds a menu "Manage Survey" which opens "Manage Survey" page
	add_menu_page( __('Manage Survey', 'mf-survey'), __('Manage Survey', 'mf-survey'), $capability, 'add-survey', 'survey' );
	
	// Adds a submenu "All Survey" which opens "Manage Survey" page
	add_submenu_page( 'add-survey', __('Manage Survey', 'mf-survey'), __('All Survey', 'mf-survey'), $capability, 'add-survey', 'survey' );
	
	// Adds a submenu "Add Survey" which opens "Add Survey" page
	add_submenu_page( 'add-survey', __('Add Survey', 'mf-survey'), __('Add Survey', 'mf-survey'), $capability, 'forms/survey-form', 'add_new_survey' );
	
	// Adds a submenu "Add Page" which opens "Add Page" page
	add_submenu_page( 'add-survey', __('Add Page', 'mf-survey'), __('Add Page', 'mf-survey'), $capability, 'forms/page-form', 'add_new_page' );
	
	// Adds a submenu "Add Question" which opens "Add Question" page
	add_submenu_page( 'add-survey', __('Add Question', 'mf-survey'), __('Add Question', 'mf-survey'), $capability, 'forms/question-form', 'add_new_question' );
	
	// Adds a submenu "Survey Result" which opens "Survey Result" page
	add_submenu_page( 'add-survey', __('Survey Result', 'mf-survey'), __('Survey Result', 'mf-survey'), $capability, 'forms/survey-result-form', 'add_survey_result' );
	
	// Adds a submenu "Survey Result" which opens "Survey Report" page
	add_submenu_page( '', __('Survey Report', 'mf-survey'), __('Survey Report', 'mf-survey'), $capability, 'forms/survey-report-form', 'add_survey_report' );
	
	// Adds a submenu "Edit Survey" which opens "Edit Survey" page
	add_submenu_page( '', __('Edit Survey', 'mf-survey'), __('Edit Survey', 'mf-survey'), $capability, 'forms/edit-survey-form', 'add_edit_survey' );
	
	// Adds a submenu "Edit Survey" which opens "Edit Survey" page
	add_submenu_page( '', __('Edit Page', 'mf-survey'), __('Edit Page', 'mf-survey'), $capability, 'forms/edit-page-form', 'add_edit_page' );
	
	// Adds a submenu "Edit Survey" which opens "Edit Survey" page
	add_submenu_page( '', __('Edit Question', 'mf-survey'), __('Edit Question', 'mf-survey'), $capability, 'forms/edit-question-form', 'add_edit_question' );
	
	// Calling function code_pages which adds new pages to plugin
	code_pages();	
	
}

/**
 * @method : code_pages()
 * @return : void
 * @desc : Adds new pages to plugin
 */
function code_pages() {

	global $_registered_pages;
	
	// Adds new pages to plugin
	$code_pages = array (
		'survey-action.php'
	);

	foreach( (array) $code_pages as $code_page ) {
	
		$hookname = get_plugin_page_hookname( 'mf-survey' . "/$code_page", '' );
		$_registered_pages[$hookname] = true;
		
	}

}

/**
 * @method : add_new_survey()
 * @return : void
 * @desc : Includes survey-form.php
 */
function add_new_survey() {

	require_once( __DIR__ . '/forms/survey-form.php' );

}

/**
 * @method : add_new_page()
 * @return : void
 * @desc : Includes page-form.php
 */
function add_new_page() {

	require_once( __DIR__ . '/forms/page-form.php' );	

}

/**
 * @method : add_new_question()
 * @return : void
 * @desc : Includes question-form.php
 */
function add_new_question() {

	require_once( __DIR__ . '/forms/question-form.php' );	

}

/**
 * @method : survey()
 * @return : void
 * @desc : Includes manage-survey.php
 */
function survey() {

	require_once( __DIR__ . '/forms/manage-survey.php' );
	
}

/**
 * @method : add_survey_result()
 * @return : void
 * @desc : Includes survey-result-form.php
 */
function add_survey_result() {

	require_once( __DIR__ . '/forms/survey-result-form.php' );

}

/**
 * @method : add_survey_report()
 * @return : void
 * @desc : Includes survey-report-form.php
 */
function add_survey_report() {

	require_once( __DIR__ . '/forms/survey-report-form.php' );

}

/**
 * @method : add_edit_survey()
 * @return : void
 * @desc : Includes edit-survey-form.php
 */
function add_edit_survey() {

	require_once( __DIR__ . '/forms/edit-survey-form.php' );

}

/**
 * @method : add_edit_page()
 * @return : void
 * @desc : Includes edit-page-form.php
 */
function add_edit_page() {

	require_once( __DIR__ . '/forms/edit-page-form.php' );

}

/**
 * @method : add_edit_question()
 * @return : void
 * @desc : Includes edit-question-form.php
 */
function add_edit_question() {

	require_once( __DIR__ . '/forms/edit-question-form.php' );

}

/**
 * @method : create_survey_page()
 * @return : void
 * @desc : This function checks if "Survey" page is added in menu on front-end
 */
function create_survey_page() {
	
	global $user_ID;
	$post_id = search_survey_page_id();
	
	// (If) "Survey" page is not added in front-end then add it
	// (Else If) "Survey" page is added then change post_status of "Survey" page to publish
	if ( empty( $post_id ) ) {
	
		$my_page = array();
		$my_page['post_title'] = 'Survey';
		$my_page['post_content'] = '[surveys]';
		$my_page['post_status'] = 'publish';
		$my_page['post_author'] = $user_ID;
		$my_page['post_type'] = 'page';
		$my_page['post_parent'] = 0;
		$my_page['guid'] = '';
		$my_page['comment_status'] = 'closed';
		$my_page['ping_status'] = 'closed';

		wp_insert_post( $my_page );
		
	} else {
		
		$current_post = get_post( $post_id, 'ARRAY_A' );
		$current_post['post_status'] = 'publish';
		$current_post['post_content'] = '[surveys]';
		wp_update_post( $current_post );
		
	}
	
}

/**
 * @method : get_all_surveys()
 * @return : void
 * @desc : Includes display-survey.php
 */
function get_all_surveys() {

	require_once( __DIR__ . '/display-survey.php' );	
	
}

/**
 * This creates shortcode "[surveys]" which calls function get_all_surveys()
 */
add_shortcode( 'surveys', 'get_all_surveys' );

/**
 * @method : change_post_status()
 * @return : void
 * @desc : This function change the post status for "Survey" page to draft
 */
function change_post_status() {

	$post_id = search_survey_page_id();
    $current_post = get_post( $post_id, 'ARRAY_A' );
    $current_post['post_status'] = 'draft';
    wp_update_post( $current_post );
	
}

/**
 * @method : change_post_status()
 * @return : $post_id integer
 * @desc : This function returns the post_id for Survey page if available
 */
function search_survey_page_id() {

	global $wpdb;
	
	$get_post_query = "SELECT $wpdb->posts.ID 
		FROM $wpdb->posts
		WHERE $wpdb->posts.post_title = 'Survey' 
		AND $wpdb->posts.post_type = 'page'
	";

	$get_post_row = $wpdb -> get_row( $get_post_query );
	$post_id = $get_post_row -> ID;
	
	return $post_id;
	
}
?>