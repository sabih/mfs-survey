<?php
/**
 * Mindfire Survey Plugin
 *
 * To add menu "Manage Survey" and submenu in admin section
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	mfs-survey.php
 */

/*
Plugin Name:	MFS Survey
Plugin URI: 	http://www.mindfiresolutions.com/
Description: 	The mfs-survey WordPress plugin lets you add Survey to your website
Version: 		1.0
Author: 		Mindfire-Solutions
Author URI: 	http://www.mindfiresolutions.com/
Text Domain: 	mfs-survey
Domain Path: 	/lang
*/

/**
 * The function surveys_add_menu_link is hooked to admin_menu
 */
add_action( 'admin_menu', 'surveys_add_menu_link' );

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
    load_plugin_textdomain( 'mfs-survey', false, 'mfs-survey/lang' );
}

/**
 * @method : surveys_add_menu_link()
 * @return : void
 * @desc : Adds a menu and submenu under "Manage Survey"
 */
function surveys_add_menu_link() {
	
	$capability = 2;

	// Adds a menu "Manage Survey" which opens "Manage Survey" page
	add_menu_page( __('Manage Survey', 'mfs-survey'), __('Manage Survey', 'mfs-survey'), $capability, 'add-survey', 'survey' );
	
	// Adds a submenu "All Survey" which opens "Manage Survey" page
	add_submenu_page( 'add-survey', __('Manage Survey', 'mfs-survey'), __('All Survey', 'mfs-survey'), $capability, 'add-survey', 'survey' );
	
	// Adds a submenu "Add Survey" which opens "Add Survey" page
	add_submenu_page( 'add-survey', __('Add Survey', 'mfs-survey'), __('Add Survey', 'mfs-survey'), $capability, 'forms/survey-form', 'add_new_survey' );
	
	// Adds a submenu "Add Page" which opens "Add Page" page
	add_submenu_page( 'add-survey', __('Add Page', 'mfs-survey'), __('Add Page', 'mfs-survey'), $capability, 'forms/page-form', 'add_new_page' );
	
	// Adds a submenu "Add Question" which opens "Add Question" page
	add_submenu_page( 'add-survey', __('Add Question', 'mfs-survey'), __('Add Question', 'mfs-survey'), $capability, 'forms/question-form', 'add_new_question' );
	
	// Adds a submenu "Survey Result" which opens "Survey Result" page
	add_submenu_page( 'add-survey', __('Survey Result', 'mfs-survey'), __('Survey Result', 'mfs-survey'), $capability, 'forms/survey-result-form', 'add_survey_result' );
	
	// Adds a submenu "Survey Result" which opens "Survey Report" page
	add_submenu_page( '', __('Survey Report', 'mfs-survey'), __('Survey Report', 'mfs-survey'), $capability, 'forms/survey-report-form', 'add_survey_report' );
	
	// Adds a submenu "Edit Survey" which opens "Edit Survey" page
	add_submenu_page( '', __('Edit Survey', 'mfs-survey'), __('Edit Survey', 'mfs-survey'), $capability, 'forms/edit-survey-form', 'add_edit_survey' );
	
	// Adds a submenu "Edit Survey" which opens "Edit Survey" page
	add_submenu_page( '', __('Edit Page', 'mfs-survey'), __('Edit Page', 'mfs-survey'), $capability, 'forms/edit-page-form', 'add_edit_page' );
	
	// Adds a submenu "Edit Survey" which opens "Edit Survey" page
	add_submenu_page( '', __('Edit Question', 'mfs-survey'), __('Edit Question', 'mfs-survey'), $capability, 'forms/edit-question-form', 'add_edit_question' );
	
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
	
		$hookname = get_plugin_page_hookname( 'mfs-survey' . "/$code_page", '' );
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

add_action( 'wp_ajax_mfs_survey_view_result', 'mfs_survey_view_result' );

/**
 * @method : mfs_survey_view_result()
 * @return : void
 * @desc : This function loads the questions and answers for a given user and survey on thickbox
 */
function mfs_survey_view_result() {
	global $wpdb;

	/**
	* Includes survey-tables.php
	*/
	require_once( __DIR__ . '/survey-tables.php' );

	$result_id = (int)$_GET["result_id"];

	$survey_result = $wpdb->get_results( $wpdb->prepare( "SELECT question, answer FROM $wp_survey_answer WHERE fk_result_id = %d", $result_id ) );
	?>
	<div class="dv_survey_result wrap">
		<table class="widefat page report" cellspacing="5" cellpadding="2" width="90%" align="center">
			<thead>
				<tr>
					<th width="50%" style="text-align:left;">
						<?php _e('Question', 'mfs-survey'); ?>
					</th>
					<th style="text-align:left;">
						<?php _e('Answer', 'mfs-survey'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( count( $survey_result ) > 0 ) {
					// To show the output loop through the user_result
					foreach($survey_result as $result_data) {
						
						echo '<tr>';
						echo '<td valign="top">' . esc_html( stripslashes_deep($result_data->question)) . '</td>';
						echo '<td valign="top">' . esc_html( stripslashes_deep($result_data->answer)) . '</td>';
						echo '</tr>';
						
					}
				} else {
					?>
					<tr>
						<td colspan="2">
							<?php _e('No questions found', 'mfs-survey'); ?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
	wp_die();
}

//To populate page droplist corresponding to survey_id
add_action('wp_ajax_form_survey','mfs_ajax_survey');
function mfs_ajax_survey() {
/**
 * Includes list.php to populate page droplist
 */
require_once( __DIR__ . '/list.php' );
$survey_id = $_POST["data_survey_id"];
$options = "";
$options .= "<option value=''>-- ".__('Please Choose', 'mfs-survey')." --</option>";

// Call function populate_page_droplist with survey_id as parameter
$options .= populate_page_droplist( $survey_id, '' );
echo $options;
die();
}

//To display the total questions available in a particular page with edit functionality
add_action('wp_ajax_save_answer','mfs_save_answer');
function mfs_save_answer() {
/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/survey-tables.php' );
if($_POST) {
$result_id = $_POST["data_result_id"];
$question_id = $_POST["data_question_id"];
$question = $_POST["data_question"];
$answer = $_POST["data_answer"];
	
// The function get_current_user_id gives current user_id
$user_id = get_current_user_id();

// This query returns result_id which are available in wp_survey_result table
// corresponding to the current user_id
// ie, We are searching for user_id and result_id combination
// If it exists then only answer will be saved
$query = 
	"
		SELECT result_id FROM $wp_survey_result
		WHERE result_id = %d
		AND fk_user_id = %d
	";
	
$result = $wpdb->get_var( $wpdb->prepare( $query, $result_id, $user_id ));

// Convert to int
$result = intval( $result );

$date = date('Y-m-d H:i:s');

// insert answer details in wp_survey_answer table
if ( $answer != "" && $result != 0) {

	$wpdb->query( $wpdb->prepare (
	"
		INSERT INTO $wp_survey_answer (
			fk_user_id, 
			fk_result_id, 
			fk_question_id, 
			question, 
			answer,
			date_created
		)
		VALUES (
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s'
		)
	",
		$user_id,
		$result_id,
		$question_id,
		$question,
		$answer,
		$date

	));
	
}
}
wp_die();
}
//To edit question
add_action('wp_ajax_edit_page_form', 'mfs_ajax_edit_page_form');
function mfs_ajax_edit_page_form() {
/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/survey-tables.php' );

$page_id = $_POST["data_page_id"];

// Deletes Question from wp_survey_question corresponding to page_id
$wpdb->query(
	$wpdb->prepare(
		"
		DELETE FROM ".$wp_survey_question."
		WHERE fk_page_id = %d
		",
		$page_id
	)
);
}

//To display the total questions available in a particular page with edit functionality
add_action('wp_ajax_edit_survey', 'mfs_ajax_edit');
function mfs_ajax_edit() {
	/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/survey-tables.php' );

/**
 * Includes list.php to populate page droplist
 */
require_once( __DIR__ . '/list.php' );

$survey_id = $_POST["data_survey_id"];
$page_id = $_POST["data_page_id"];

// Deletes Page from wp_survey_page corresponding to page_id
$wpdb->query(
	$wpdb->prepare(
		"
		DELETE FROM ".$wp_survey_page."
		WHERE page_id = %d
		",
		$page_id
	)
);

// This query returns survey details from wp_survey_page table
$query = 
	"
		SELECT page_id
		FROM $wp_survey_page
		WHERE page_id = %d
	";	

// The function get_var() returns NULL if page_id id deleted
$deleted_page_id = $wpdb->get_var( $wpdb->prepare ( $query, $page_id ));

// Convert to int
$deleted_page_id = intval( $deleted_page_id );

if( $deleted_page_id === 0 ) {

	// Deletes Question from wp_survey_question corresponding to page_id
	$wpdb->query(
		$wpdb->prepare(
			"
			DELETE FROM ".$wp_survey_question."
			WHERE fk_page_id = %d
			",
			$page_id
		)
	);

}

// This query returns page details from wp_survey_page table
$query = 
	"
		SELECT count( page_id )
		FROM $wp_survey_page
		WHERE fk_survey_id = %d
	";
	
$count_page_id = $wpdb->get_var( $wpdb->prepare ( $query, $survey_id ));

// Convert to int
$count_page_id = intval($count_page_id);

if ( $count_page_id === 0) {
	
	$wpdb->query( $wpdb->prepare (
		"
		UPDATE $wp_survey
		SET fk_start_page_id = 0
		WHERE survey_id = %d
		", 
		$survey_id
	));		
	
}

$result = $wpdb->get_row( $wpdb->prepare( "SELECT fk_start_page_id, survey_name FROM $wp_survey WHERE survey_id = %d", $survey_id ));
$start_page_id = $result->fk_start_page_id;

$options = "";
$options .= "<option value=''>-- ".__('Please Choose', 'mfs-survey')." --</option>";

// Call function populate_page_droplist with $survey_id and $start_page_id as parameter
$options .= populate_page_droplist( $survey_id, $start_page_id );
echo $options;

}

?>