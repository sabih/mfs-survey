<?php
/**
 * To display the total questions available in a particular page with edit functionality
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	ajax-edit-page.php
 */

/**
 * Includes wp-load.php
 */
require_once("../../../../wp-load.php");

/**
* Includes survey-tables.php
*/
require_once( __DIR__ . '/../survey-tables.php' );

/**
 * Includes list.php to populate page droplist
 */
require_once( __DIR__ . '/../list.php' );

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
?>