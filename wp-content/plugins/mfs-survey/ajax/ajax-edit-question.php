<?php
/**
 * To edit question
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	ajax-edit-question.php
 */

/**
 * Includes wp-load.php
 */
require_once("../../../../wp-load.php");

/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/../survey-tables.php' );

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
?>