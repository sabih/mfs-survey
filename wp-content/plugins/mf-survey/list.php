<?php
/**
 * To populate survey, page and question_type droplist
 *
 * @package	WordPress
 * @subpackage	mf-survey
 * @filename	list.php
 */

/**
 * @method : populate_survey_droplist()
 * @return : $options string
 * @desc : This function returns all surveys with active status
 *		which are not published
 */
function populate_survey_droplist() {

	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	// This query returns survey details from wp_survey table
	$query = 
		"
			SELECT survey_id, survey_name
			FROM $wp_survey
			WHERE survey_status = 'A' AND
			publish_status = 'A'
			ORDER BY survey_id
		";
		
	$results = $wpdb->get_results( $query );
	$options = "";
	
	// This creates key => value for survey droplist
	foreach($results as $row) {
		
		$survey_id = $row->survey_id;
		$survey_name = stripslashes_deep( $row->survey_name );
		
		$options .= '<option value="' . $survey_id . '">' . $survey_name . '</option>';
		
	}
	return $options;
	
}

/**
 * @method : populate_page_droplist()
 * @return : $options string
 * @desc : This function returns all the pages with active status
 */
function populate_page_droplist( $survey_id, $start_or_end_page_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );
	
	// This query returns page details from wp_survey_page table
	$query = 
		"
			SELECT page_id, page_title
			FROM $wp_survey_page
			WHERE fk_survey_id = %d AND
			page_status = 'A' 
			ORDER BY page_id
		";
		
	$results = $wpdb->get_results( $wpdb->prepare ( $query, $survey_id ));
	$options = "";
	
	// This creates key => value for page droplist
	foreach( $results as $row ) {
		
		$page_id = $row->page_id;
		$page_title = stripslashes_deep( $row->page_title );
		
		$options .= '<option value="'.$page_id.'"';
			
			if ( $page_id === $start_or_end_page_id ) {
				
				$options .= 'selected="selected"';
				
			}
			
		$options .= '>'.$page_title.'</option>';
		
	}
	return $options;
	
}

/**
 * @method : populate_next_page_droplist()
 * @param : $survey_id integer
 * @param : $page_id integer
 * @param : $next_page_id integer
 * @return : $options string
 * @desc : This function returns all pages with active status (excluding $page_id)
 */
function populate_next_page_droplist( $survey_id, $page_id, $next_page_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );
	
	// This query returns page details from wp_survey_page table
	$query = 
		"
			SELECT page_id, page_title
			FROM $wp_survey_page
			WHERE fk_survey_id = %d AND
			page_id != %d AND
			page_status = 'A'
			ORDER BY page_id
		";
		
	$results = $wpdb->get_results( $wpdb->prepare ( $query, $survey_id, $page_id ));
	
	$options = "";
	
	// This creates key => value for page droplist
	foreach($results as $row) {
		
		$page_id = $row->page_id;
		$page_title = stripslashes_deep( $row->page_title );
		
		$options .= '<option value="'.$page_id.'"';
			
			if ( $page_id === $next_page_id ) {
				
				$options .= 'selected="selected"';
				
			}
			
		$options .= '>'.$page_title.'</option>';
		
	}
	return $options;
	
}

/**
 * @method : populate_question_type_droplist()
 * @return : $question_type array
 * @desc : This function returns question type as an associative array
 */
function populate_question_type_droplist() {

	$question_type = array(
		'Textbox'=> 'Textbox',
		'Radiobutton'=> 'Radiobutton',
		'Checkbox'=> 'Checkbox'
	);

	return $question_type;
	
}
?>