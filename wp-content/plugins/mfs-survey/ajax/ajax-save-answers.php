<?php
/**
 * To display the total questions available in a particular page with edit functionality
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	ajax-save-answers.php
 */

/**
 * Includes wp-load.php
 */
require_once("../../../../wp-load.php");

/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/../survey-tables.php' );

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
?>