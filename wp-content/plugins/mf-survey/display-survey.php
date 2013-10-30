<?php
/**
 * To display survey questions in front-end
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	display_survey.php
 */

/**
 * To include question-script.js file
 */
wp_register_script( 'display-survey-script', plugins_url() . '/mf-survey/scripts/display-survey-script.js', 'jquery' );
wp_enqueue_script( 'display-survey-script' );

/**
 * To include jquery-validate.js file 
 * This file helps in front-end validation
 */
wp_register_script( 'jquery-validate', plugins_url() . '/mf-survey/scripts/jquery/jquery-validate.js', array('jquery') );
wp_enqueue_script( 'jquery-validate' );

/**
 * To include survey-style.css file
 */ 
wp_enqueue_style( '', plugins_url() . '/mf-survey/styles/survey-style.css', '' );

// call function display_survey
$display_results = display_survey();

echo $display_results;

/**
 * @method : display_survey()
 * @return : $options string
 * @desc : This function checks if user is logged in
 * 			If logged in then Surveys will be displayed
 * 			Else Login message displayed
 */
function display_survey() {
	
	$user_id = get_current_user_id();
	
	// Convert to int
	$user_id = intval( $user_id );	
	
	$options = "";
	
	if ( $user_id != 0 ) {
	
		$options = get_survey_details();		
	
	} else {
	
		$url = get_site_url();
		$survey_url = $url."/wp-login.php";
		
		$options = "Please <a href='$survey_url'>Log in</a> to take survey";
	
	}
	
	return $options;
	
}

/**
 * @method : get_survey_details()
 * @return : $survey_details string
 * @desc : This function displays all the published surveys with active status
 */
function get_survey_details() {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );

	$user_id = get_current_user_id();
	
	// Convert to int
	$user_id = intval( $user_id );	
	
	$survey_details = "";

	// This query returns survey details from wp_survey table
	// Those surveys which current user have not answered
	$query = 
		"
			SELECT survey_id, survey_name
			FROM $wp_survey
			WHERE survey_status = 'A' AND
			publish_status = 'I' AND
			survey_id NOT IN
				(
					SELECT fk_survey_id FROM $wp_survey_result
					WHERE fk_user_id = %d AND
					result_status = 'I'
				)
			ORDER BY survey_id DESC
		";
	
	// The function get_results() returns the entire query result as an array
	// Each element of this array corresponds to one row of the query result
	$results = $wpdb->get_results( $wpdb->prepare ( $query, $user_id ));	

	// All elements of array are displayed separately as Survey Name	
	$survey_details = '<ol>';
	
	foreach($results as $row) {
	
		$survey_id = $row->survey_id;
		$survey_name = stripslashes_deep( $row->survey_name );
		
		$url = get_permalink();
		
		//Passing survey_id in url as query string
		$params = array	(
			'action' => 'display_question',
			'survey_id' => $survey_id
		);
		
		//Saving current url with survey_id in $url
		$url = add_query_arg( $params, $url );
		
		//Made All surveys as link which redirects to page with start_page_id 
		$survey_details .= '<li><a href="' . $url . '">' . $survey_name. '</a></li><br />';
		
	}
	
	$survey_details .= '</ol>';
	
	if( $_GET['action'] === 'display_question' ) {
		
		$survey_details = display_page();
		
	}
	
	return $survey_details;

}

/**
 * @method : display_page()
 * @return : $display_page string
 * @desc : This function gets page_id from hidden field
 * 			for displaying question for that page_id
 */
function display_page() {
	
	// If this is the first page of survey then
	// Through survey_id we get the start_page_id 
	// and stored it in $page_id
	$survey_id = $_GET['survey_id'];
	
	// Convert to int
	$survey_id = intval( $survey_id );
	
	$survey_exist = survey_exists($survey_id);
	
	if ($survey_exist === 0) {
		
		$display_page = display_survey_error_page();
		return $display_page;
	
	}
	
	// Call function save_user_details()
	$result_id = save_user_details( $survey_id );
	
	// The function get_start_page returns start_page_id for a particular survey
	$page_id = get_start_page( $survey_id );
	
	// The function get_answer_details returns next_page_id for a particular survey
	// with corresponding result_id
	$get_page_id = get_answer_details( $result_id );

	if ( $get_page_id != 0 ) {
	
		$page_id = $get_page_id;
	
	}
	
	// The function get_end_page returns next_page_id of end_page_id for a particular survey
	$end_page_id = get_end_page( $survey_id );
	
	// Call function get_page_and_question_status()
	// @param $page_id
	$status = get_page_and_question_status( $page_id );
	
	$question_status = $status['question_status'];
	$page_status = $status['page_status'];

	// For the first page if page_status or question_status is not active
	// then display error message
	if ( $page_status != 'A' || $question_status != 'A' ) {
	
		// Call function survey_inactive() if user answered all questions
		survey_inactive(  $result_id );	
		$display_page = display_error_page();
		return $display_page;
	
	}
	
	if ( $get_page_id === "" || $page_id === $end_page_id ) {
	
		// Call function survey_inactive() if user answered all questions
		survey_inactive(  $result_id );			
		$display_page = display_last_page();
		return $display_page;
	
	}
	
	// If next_page_id is not blank 
	// then display next page corresponding to next_page_id
	$display_page = display_next_page( $page_id, $result_id );
	return $display_page;

}

/**
 * @method : survey_exists()
 * @param : $survey_id integer
 * @return : $survey_exist integer
 * @desc : This function checks if survey is available
 */
function survey_exists($survey_id) {
	
	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );
	
	$query = 
		"
			SELECT survey_id
			FROM $wp_survey
			WHERE survey_id = %d
		";
			
	$survey_exist = $wpdb->get_var( $wpdb->prepare( $query, $survey_id ));
	
	// Convert to int
	$survey_exist = intval($survey_exist);
	
	return $survey_exist;
	
}

/**
 * @method : get_answer_details()
 * @param : $result_id integer
 * @return : $next_page_id integer
 * @desc : This function displays only those question's page_id which user have not answered
 */
function get_answer_details( $result_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );
	
	// This query returns page_id corresponding to the question_id
	// with max answer_id for a particular result_id
	$query = 
	"
		SELECT fk_page_id
		FROM $wp_survey_question
		WHERE question_id = 
		(
			SELECT fk_question_id
			FROM $wp_survey_answer 
			WHERE fk_result_id = %d
			ORDER BY answer_id DESC LIMIT 1
		)
	";

	// If user details is available in wp_survey_answer table
	// Then we return the $page_id
	$page_id = $wpdb->get_var( $wpdb->prepare( $query, $result_id ));
		
	// Convert to int
	$page_id = intval( $page_id );
	
	// This query returns question_data for a particular page_id
	$query = 
	"
		SELECT question_data
		FROM $wp_survey_question
		WHERE fk_page_id = %d AND		
		question_type ='Button'
	";

	$question_data = $wpdb->get_var( $wpdb->prepare( $query, $page_id ));
	$question_data = unserialize( ( $question_data ) );
	$next_page_id = $question_data[next_page];	
	$radio_next_page_id = $question_data[radio_next_page];
	
	// If radio_next_page_id = NULL then return next_page_id
	if ($radio_next_page_id === NULL) {
	
		return $next_page_id;
	
	} else {
	
		// If radio_next_page_id is available the return 
		// page_id corrsponding to a particular option
		// Get option from wp_survey_answer table
		$query_answer = 
			"
				SELECT answer
				FROM $wp_survey_answer
				WHERE fk_question_id = 
				(
					SELECT question_id
					FROM $wp_survey_question
					WHERE fk_page_id = %d AND		
					question_type !='Button'
				)
			";
		
		$answer = $wpdb->get_var( $wpdb->prepare( $query_answer, $page_id ));
		return $radio_next_page_id[$answer];
		
	}

}

/**
 * @method : survey_inactive()
 * @param : $result_id integer
 * @return : void
 * @desc : This function change the result_status to "I" ie, Inactive
 */
function survey_inactive( $result_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );

	// update wp_survey_result table to change the result_status
	$wpdb->update(
		$wp_survey_result, 
		array( 'result_status' => 'I' ),
		array( 'result_id' => $result_id ),
		array( '%s' ),
		array( '%d' )
	);

}

/**
 * @method : save_user_details()
 * @param : $survey_id integer
 * @return : $result_id integer
 * @desc : This function saves user details in wp_survey_result table
 */
function save_user_details( $survey_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );

	// The function get_current_user_id gives current user_id
	$user_id = get_current_user_id();
	
	// Convert to int
	$user_id = intval( $user_id );
	
	$date = date( 'Y-m-d H:i:s');
	
	// This query returns user details if available in wp_survey_result table
	$query = 
	"
		SELECT result_id, fk_survey_id
		FROM $wp_survey_result 
		WHERE fk_user_id = %d AND
		fk_survey_id = %d
	";

	$result = $wpdb->get_row( $wpdb->prepare( $query, $user_id, $survey_id ));
	
	// If user details is available in wp_survey_result table
	// Then we return the $result_id
	$result_id = $result->result_id;	
	$get_survey_id = $result->fk_survey_id;
	
	// Convert to int
	$get_survey_id = intval( $get_survey_id );
	
	// If user details is not available in wp_survey_result table
	// then add it in wp_survey_result table
	if ( $get_survey_id === 0 ) {
	
		$wpdb->query( $wpdb->prepare (
			"
				INSERT INTO $wp_survey_result (
					fk_user_id, 
					fk_survey_id, 
					result_status,
					date_created
				)
				VALUES (
					'%d',
					'%d',
					'%s',
					'%s'
					)
			",
				$user_id,
				$survey_id,
				'A',
				$date
		));
		
	}	
	
	// If the insert query executed successfully
	// the ID generated for AUTO_INCREMENT column 
	// ie, result_id can be accessed by $wpdb->insert_id
	$insert_id = $wpdb->insert_id;

	if( $insert_id  > 0 ) {
	
		$result_id = $insert_id;
		
	}

	// If user details is already available in wp_survey_result table
	// Then we return the $result_id
	// Else the new $insert_id will be returned
	return $result_id;
	
}

/**
 * @method : display_next_page()
 * @param $page_id integer
 * @param $result_id integer
 * @return : $question_data string
 * @desc : This function displays page with questions

 */
function display_next_page( $page_id, $result_id ) {
	
	// Call function get_page_and_question_status()
	// @param $page_id
	$status = get_page_and_question_status( $page_id );
	
	$question_status = $status['question_status'];
	$page_status = $status['page_status'];

	if ( $page_status === 'A' && $question_status === 'A' ) {

		// Hiding the Survey title
		echo "<style>.entry-title {display:none;}</style>";
		
		// Call function get_page_title()
		// @param $page_id
		$page_title = get_page_title( $page_id );	
			
		// Displaying the Page Title as header of page
		echo "<h1 class='entry-title' style='display:block;'>" . $page_title . "</h1><br />";	
	
		// Call get_question_details() to diplay question details
		$question_data = get_question_details( $page_id, $result_id );
		
	} else {
	
		// Call display_last_page() to diplay last page
		$question_data = display_last_page();
	
	}
	
	return $question_data;

}

/**
 * @method : get_page_and_question_status()
 * @param : $page_id integer
 * @return : $status string
 * @desc : This function returns page_status and question_status
 * 			for a particular page with question
 */
function get_page_and_question_status( $page_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );

	$query = 
	"
		SELECT q.question_status, p.page_status
		FROM $wp_survey_question as q 
		INNER JOIN $wp_survey_page as p 
		ON q.fk_page_id = p.page_id
		WHERE q.fk_page_id = %d
	";

	$result = $wpdb->get_row( $wpdb->prepare( $query, $page_id ));
	$question_status = $result->question_status;
	$page_status = $result->page_status;
	
	$status = array();
	$status['question_status'] = $question_status;
	$status['page_status'] = $page_status;
	
	return $status;

}

/**
 * @method : get_page_title()
 * @param $page_id integer
 * @return : $page_title string
 * @desc : This function returns "Page Title" from wp_survey_page table
 */
function get_page_title( $page_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );

	$query_page = 
	"
		SELECT page_title
		FROM $wp_survey_page
		WHERE page_id = %d AND
		page_status = 'A'
	";
	
	$page_title = $wpdb->get_var( $wpdb->prepare ( $query_page, $page_id ));	
	$page_title = stripslashes_deep( $page_title );
	
	return $page_title;

}

/**
 * @method : get_start_page()
 * @param $survey_id integer
 * @return : $page_id integer
 * @desc : This function returns "Start Page Id" from wp_survey table 
 */
function get_start_page( $survey_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );

	$query = 
	"
		SELECT fk_start_page_id
		FROM $wp_survey
		WHERE survey_id = %d
	";	
	
	$page_id = $wpdb->get_var( $wpdb->prepare ( $query, $survey_id ));
		
	return $page_id;

}

/**
 * @method : get_end_page()
 * @param $survey_id integer
 * @return : $next_page_id integer
 * @desc : This function returns "End Page Id" from wp_survey table 
 */
function get_end_page( $survey_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );

	// This query returns fk_end_page_id from wp_survey table
	$query = 
	"
		SELECT fk_end_page_id
		FROM $wp_survey
		WHERE survey_id = %d
	";	
	
	$page_id = $wpdb->get_var( $wpdb->prepare ( $query, $survey_id ));

	// This query returns next page from wp_survey_question table
	// for end_page_id
	$query = "
				SELECT question_data
				FROM $wp_survey_question 
				WHERE fk_page_id = %d AND
				question_type = 'Button'
			";
	$question_data = $wpdb->get_var( $wpdb->prepare( $query, $page_id ));
	$question_data = unserialize(( $question_data ));
	$next_page_id = $question_data[next_page];
	
	return $next_page_id;

}

/**
 * @method : get_question_details()
 * @param $page_id integer
 * @param $result_id integer
 * @return : $options string
 * @desc : This function returns question details from wp_survey_question table
 * 			corresponding to page_id
 */
function get_question_details( $page_id, $result_id ) {

	// Includes survey-tables.php
	require( __DIR__ . '/survey-tables.php' );
	
	// Includes ajax-save-answers.php
	$ajax_save_answers_url = plugins_url() . '/mf-survey/ajax/ajax-save-answers.php';

	$query_question = 
		"
			SELECT question_id, fk_page_id, question_type, question_data
			FROM $wp_survey_question 
			WHERE fk_page_id = %d
			ORDER BY question_id
		";
		
	$results = $wpdb->get_results( $wpdb->prepare( $query_question, $page_id ));
	
	$options = "";
	
	$options .= '<form action="" method="post" name="form_diplay_page" id="form_diplay_page" onsubmit="return validate_diplay_page();" autocomplete="off">';
	
	$options .=  '<input type="hidden" id="hid_result_id" name="hid_result_id" value="' . $result_id . '" />';
	
	foreach( $results as $result ) {
	
		$question_type = $result->question_type;
		
		if ( $question_type != "Button" ) {
		
			$question_id = $result->question_id;
			
			$options .= '<input type="hidden" id="hid_question_id" name="hid_question_id" value="' . $question_id . '" />';
		
		}
		
		$question_data = $result->question_data;
		$question_data = unserialize( ( $question_data ) );
		
		foreach ( $question_data as $key => $value) {
		
			if ( $key === 'next_page' ) {
			
				$next_page = esc_html( stripslashes_deep( $value ));
				$options .= '<input type="hidden" id="hid_next_page" name="hid_next_page" value="' . $next_page . '" />';
			
			} else if ( $key === 'question' ) {
			
				$question = esc_html( stripslashes_deep( $value ));
				$options .= 'Q: ' . $question . '<br /><br />';
				$options .= '<input type="hidden" id="hid_question" name="hid_question" value="' . $question . '" />';
			
			} else if ( $key === 'option' && $question_type === 'Radiobutton') {
			
				$rad_id = 0;
				$options .= '<table id="tbl_radio"><tbody>';
				
				foreach ($value as $key => $value) {
				
					$radio_value = esc_html( stripslashes_deep( $value ));
					$options .= '<tr><td><input onclick="validate_rad_option();" type="radio" class="rad_option" id="rad_option' . $rad_id . '" name="rad_option" value="' . $radio_value . '" /> ' . $radio_value . '</td></tr>';
					$rad_id++;
					
				}
				
				$options .= '</tbody></table>';
				
			} else if ( $key === 'option' && $question_type === 'Checkbox') {
			
				$chk_id = 0;
				$options .= '<table id="tbl_checkbox"><tbody>';
				
				foreach ($value as $key => $value) {
				
					$checkbox_value = esc_html( stripslashes_deep( $value ));
					$options .= '<tr><td><input onclick="validate_checkbox_option();" type="checkbox" class="chk_option" id="chk_option' . $chk_id . '" name="chk_option[]" value="' . $checkbox_value . '" /> ' . $checkbox_value . '</td></tr>';
					$chk_id++;
				
				}
				
				$options .= '</tbody></table>';
				
			}
			
		}
		
		if ( $question_type === 'Textbox') {
		
			$options .= '<input type="text" id="txt_answer" name="txt_answer" onkeyup="validate_txt_answer();" /><br /><br />';
		
		}
		
		if ( $question_type === 'Button') {			
			
			$options .= '<input type="hidden" id="hid_url" name="hid_url" value="' . $ajax_save_answers_url . '" />';
			
			$options .= 
			'<p class="submit">
				<input type="submit" name="btn_save_answers" id="btn_save_answers" value="'. __('Save', 'mf-survey') .'" class="button" />
			</p>';
		
		}
	
	}
	
	$options .= '</form>';
	
	return $options;

}

/**
 * @method : display_last_page()
 * @return : void
 * @desc : This function displays last page message
 *			with link to Survey page
 */
function display_last_page() {

	_e('This survey ends here', 'mf-survey');
	$url = get_site_url();
	$survey_url = $url."/survey";
	?>
	<br /><br /><?php _e('Please take another', 'mf-survey'); ?> <a href="<?php echo $survey_url; ?>"><?php _e('Survey', 'mf-survey'); ?></a>
	<?php

}

/**
 * @method : display_error_page()
 * @return : void
 * @desc : This function displays error message
 *			with link to Survey page
 */
function display_error_page() {

	_e('This survey needs some modification', 'mf-survey');
	$url = get_site_url();
	$survey_url = $url."/survey";
	?>
	<br /><br /><?php _e('Please take another', 'mf-survey'); ?> <a href="<?php echo $survey_url; ?>"><?php _e('Survey', 'mf-survey'); ?></a>
	<?php

}

function display_survey_error_page() {

	_e('This survey is not available', 'mf-survey');

}

?>