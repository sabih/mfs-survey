<?php
/**
 * To insert and update the survey details
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	survey-action.php
 */

// call function insert_data
insert_data();

/**
 * @method : insert_data()
 * @return : void
 * @desc : Populate or update tables
 */
function insert_data() {
	
	if ( isset( $_POST['btn_submit_survey'] )) {
	
		//call function submit_survey
		submit_survey();		
		
	} else if ( isset( $_POST['btn_submit_page'] )) {
	
		//call function submit_page
		submit_page();
		
	} else if ( isset( $_POST['btn_submit_question'] )) {
	
		//call function submit_question
		submit_question();
		
	} else if ( isset( $_POST['btn_manage_survey'] )) {
		
		//call function manage_survey
		manage_survey();
		
	} else if ( isset( $_POST['btn_edit_survey'] )) {
		
		//call function edit_survey
		edit_survey();
		
	} else if ( isset( $_POST['btn_edit_page'] )) {
		
		//call function edit_page
		edit_page();
		
	} else if ( isset( $_POST['btn_edit_question'] )) {
		
		//call function edit_question
		edit_question();
		
	} else if ( $_GET['action'] === 'publish_survey' ) {
	
		//call function publish_survey
		publish_survey();
	
	} else if ( $_GET['action'] === 'delete_survey' ) {
	
		//call function delete_survey
		delete_survey();
	
	}
	
}

/**
 * @method : publish_survey()
 * @return : void
 * @desc : This function updates publish_status for survey in wp_survey table
 */
function publish_survey() {
	
	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	$survey_id = $_GET['survey_id'];
	
	// Convert to int
	$survey_id = intval( $survey_id );
	
	// update wp_survey table to change the survey_status
	$wpdb->update(
		$wp_survey, 
		array( 'publish_status' => 'I' ),
		array( 'survey_id' => $survey_id ),
		array( '%s' ),
		array( '%d' )
	);
	
	// redirect to manage-survey
	wp_redirect( 'admin.php?page=add-survey' );

}

/**
 * @method : submit_survey()
 * @return : void
 * @desc : This function saves survey details in wp_survey table
 */
function submit_survey() {
	
	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	// The function get_current_user_id gives current user_id
	$user_id = get_current_user_id();
	$new_survey = wp_filter_kses($_POST['txt_survey']);
	$survey_status = 'A';
	$publish_status = 'A';
	$date = date( 'Y-m-d H:i:s');
	$insert_id = 0;
	
	// This query returns survey details from wp_survey table
	$query = 
		"
			SELECT survey_name
			FROM $wp_survey
			WHERE survey_name = %s
		";	
	
	// The function get_var() returns the survey_name
	$survey_name = $wpdb->get_var( $wpdb->prepare ( $query, $new_survey ));
	
	if ( !empty( $survey_name ) ) {
	
		// This sets the cookie named "survey_available" with value "1" for "2 sec"
		setcookie( "survey_available", "1", time() + 2 );
		
		// redirect to survey-form
		wp_redirect( 'admin.php?page=forms/survey-form' );
		exit;
		
	}	
		
	// insert survey details in wp_survey table
	$wpdb->query( $wpdb->prepare (
		"
			INSERT INTO $wp_survey (
				fk_user_id, 
				survey_name, 
				survey_status, 
				publish_status,
				date_created 
			)
			VALUES (
				'%d',
				'%s',
				'%s',
				'%s',
				'%s'
				)
		",
			$user_id,
			$new_survey,
			$survey_status,
			$publish_status,
			$date
	));
		
	// If the insert query executed successfully
	// the ID generated for AUTO_INCREMENT column 
	// ie, survey_id can be accessed by $wpdb->insert_id
	$insert_id = $wpdb->insert_id;
	
	if( $insert_id  > 0 ) {
	
		// This sets the cookie named "survey_saved" with value "1" for "2 sec"
		setcookie( "survey_saved", "1", time() + 2 );
	
	}
	
	// redirect to page-form
	wp_redirect( 'admin.php?page=forms/page-form' );

}

/**
 * @method : submit_page()
 * @return : void
 * @desc : This function saves page details in wp_survey_page table
 */
function submit_page() {
	
	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	$survey_id = $_POST['sel_survey'];
	$new_page_title = wp_filter_kses($_POST['txt_page']);
	$date = date( 'Y-m-d H:i:s');
	$status = 'A';
	$insert_id = 0;
	
	// This query returns survey details from wp_survey table
	$query = 
		"
			SELECT page_title
			FROM $wp_survey_page
			WHERE fk_survey_id = %d AND
			page_title = %s
		";	
	
	// The function get_var() returns the page_title
	$page_title = $wpdb->get_var( $wpdb->prepare ( $query, $survey_id, $new_page_title ));
	
	if ( !empty( $page_title ) ) {
	
		// This sets the cookie named "page_available" with value "1" for "2 sec"
		setcookie( "page_available", "1", time() + 2 );
		
		// redirect to page-form
		wp_redirect( 'admin.php?page=forms/page-form' );
		exit;
		
	}	
	
	// insert page details in wp_survey_page table
	$wpdb->query( $wpdb->prepare (
		"
			INSERT INTO $wp_survey_page (
				fk_survey_id, 
				page_title, 
				date_created, 
				date_modified,
				page_status
			)
			VALUES (
				'%d',
				'%s',
				'%s',
				'%s',
				'%s'
				)
		",
			$survey_id,
			$new_page_title,
			$date,
			$date,
			$status
	));
	
	// If the insert query executed successfully
	// the ID generated for AUTO_INCREMENT column 
	// ie, page_id can be accessed by $wpdb->insert_id
	$insert_id = $wpdb->insert_id;
	
	if( $insert_id  > 0 ) {
	
		// This sets the cookie named "page_saved" with value "1" for "2 sec"
		setcookie( "page_saved", "1", time() + 2 );
	
	}
	
	// redirect to question-form
	wp_redirect( 'admin.php?page=forms/question-form' );
	
}

/**
 * @method : submit_question()
 * @return : void
 * @desc : This function saves question details in wp_survey_question table
 */
function submit_question() {

	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	$survey_id = $_POST['sel_survey'];
	$page_id = $_POST['sel_page'];	
	
	// This query returns survey details from wp_survey table
	$query = 
		"
			SELECT fk_start_page_id
			FROM $wp_survey
			WHERE survey_id = %d
		";
		
	$start_page_id = $wpdb->get_var( $wpdb->prepare ( $query, $survey_id ));
	
	// Convert to int
	$start_page_id = intval($start_page_id);
	
	// If this is 1st question for a particular survey then this page_id will be 
	// stored as fk_start_page_id in wp_survey table
	if ( $start_page_id === 0) {
	
		$wpdb->query( $wpdb->prepare (
			"
			UPDATE $wp_survey
			SET fk_start_page_id = %d
			WHERE survey_id = %d
			", $page_id, $survey_id
		));		
		
	}	
	
	// Checking if the page_id is already available in question table
	// If it exists then show error message		
	$count_question = 
		"
			SELECT COUNT( question_id )
			FROM $wp_survey_question 
			WHERE fk_page_id = %d
		";
			
	$count_results = $wpdb->get_var( $wpdb->prepare( $count_question, $page_id ));
	
	// Convert to int
	$count_results = intval($count_results);
	
	if ($count_results > 0) {
	
		// This sets the cookie named "question_available" with value "1" for "2 sec"
		setcookie( "question_available", "1", time() + 2 );
		
		// redirect to survey-form
		wp_redirect( 'admin.php?page=forms/question-form' );
		exit;
	
	}
	
	$next_page = $_POST['sel_next_page'];
	$question = wp_filter_kses( $_POST['txt_question'] );
	
	$question_type = $_POST['sel_question_type'];
	$date = date( 'Y-m-d H:i:s' );
	
	// get the txt_option's value from the hidden field
	$hid_option_ids = $_POST['hid_option_ids'];
	
	// remove the first comma(,) from the string
	$hid_ids_mod = substr($hid_option_ids, 1);
	
	$hid_ids_mod = wp_filter_kses( $hid_ids_mod );
	
	// get the text_option's values in array
	$option = explode("|", $hid_ids_mod);
		
	if ( $question_type === "Textbox") {
	
		$data = array(
			'question' => $question
		);
		
	} else {
	
		$data = array(
			'question' => $question,
			'option' => $option
		);
	
	}
		
	$data2 = array(
		'next_page' => $next_page
	);	
	
	$question_data = serialize( $data );
	$button_data = serialize( $data2 );
	
	// Insert question_data
	$wpdb->query( $wpdb->prepare (
	"
		INSERT INTO $wp_survey_question (
			fk_page_id,
			question_type,
			question_data,
			date_created, 
			date_modified
		)
		VALUES (
			'%d',
			'%s',
			'%s',
			'%s',
			'%s'
		)
	",
		$page_id,
		$question_type,
		$question_data,
		$date,
		$date
	));

	// Insert button_data
	$wpdb->query( $wpdb->prepare (
	"
		INSERT INTO $wp_survey_question (
			fk_page_id,
			question_type,
			question_data,
			date_created, 
			date_modified
		)
		VALUES (
			'%d',
			'%s',
			'%s',
			'%s',
			'%s'
		)
	",
		$page_id,
		'Button',
		$button_data,
		$date,
		$date
	));
	
	// If the insert query executed successfully
	// the ID generated for AUTO_INCREMENT column 
	// ie, page_id can be accessed by $wpdb->insert_id
	$insert_id = $wpdb->insert_id;
	
	if( $insert_id  > 0 ) {
	
		// This sets the cookie named "question_saved" with value "1" for "2 sec"
		setcookie( "question_saved", "1", time() + 2 );
	
	}
	
	// redirect to question-form
	wp_redirect( 'admin.php?page=forms/question-form' );
	
}

/**
 * @method : manage_survey()
 * @return : void
 * @desc : This function updates survey_status in wp_survey table
 */
function manage_survey() {

	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	// get the check box value & status from the hidden field
	$chk_ids = $_POST['hid_survey_checked_ids'];
	$url = $_POST['hid_url'];
	
	// remove the first comma(,) from the string
	$chk_ids_mod = substr($chk_ids, 1);
	
	// get the value||status pair as array
	$chk_ids_ex = explode(",", $chk_ids_mod);
	
	foreach($chk_ids_ex as $chk_id_pipe) { 
		
		// get each value and status separately
		list($chk_id, $chk_status) = explode("||", $chk_id_pipe);
		
		// update wp_survey table to change the survey_status
		$wpdb->update(
			$wp_survey, 
			array( 'survey_status' => $chk_status ),
			array( 'survey_id' => $chk_id ),
			array( '%s' ),
			array( '%d' )
		);

	}
	
	// redirect to manage-survey
	wp_redirect( $url );

}

/**
 * @method : edit_survey()
 * @return : void
 * @desc : This function updates "start_page in wp_survey" and "page_status in wp_survey_page" table
 */
function edit_survey() {

	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	$date = date( 'Y-m-d H:i:s' );
		
	/**
	 *	Updating "page_status" records through checkbox status in wp_survey_page table
	 */
	// get the check box value & status from the hidden field
	$chk_ids = $_POST['hid_page_checked_ids'];
	
	// remove the first comma(,) from the string
	$chk_ids_mod = substr($chk_ids, 1);
	
	// get the value||status pair as array
	$chk_ids_ex = explode(",", $chk_ids_mod);
	
	foreach($chk_ids_ex as $chk_id_pipe) {
		
		// get each value and status separately
		list($chk_id, $chk_status) = explode("||", $chk_id_pipe);
		
		// update wp_survey_page table to change page_status
		$wpdb->update(
			$wp_survey_page, 
			array( 
				'page_status' => $chk_status,
				'date_modified' => $date
				),
			array( 'page_id' => $chk_id ),
			array( 
				'%s',
				'%s'
			),
			array( '%d' )
		);

	}
	
	/**
	 *	Updating "question_data" records through sel_next_page value in wp_survey_question table
	 */
	// get the check box value & status from the hidden field
	$next_page_ids = $_POST['hid_sel_next_page_ids'];
	
	// remove the first comma(,) from the string
	$next_page_ids_mod = substr($next_page_ids, 1);
	
	// get the value||status pair as array
	$next_page_ids_ex = explode(",", $next_page_ids_mod);
	
	foreach($next_page_ids_ex as $next_page_id_pipe) {
		
		// get each value and status separately
		list($question_id, $next_page_id_val) = explode("||", $next_page_id_pipe);
		
		// This query returns question details from wp_survey_question table
		$query = "
					SELECT question_data
					FROM $wp_survey_question 
					WHERE question_id = %d AND
					question_type = 'Button'
				";
		$row = $wpdb->get_row( $wpdb->prepare( $query, $question_id ));
		
		$question_data = $row->question_data;
		$question_data = unserialize(( $question_data ));
		$radio_next_page = $question_data[radio_next_page];
		
		// Update next_page for particular page
		// If radio_next_page is not available in question_data
		// Then update next_page
		if($radio_next_page === NULL) {
			
			$data = array(
				'next_page' => $next_page_id_val
			);
			
		} else {
			
			// If radio_next_page is available in question_data
			// Then update next_page and append radio_next_page values
			$next_page = array(
				'next_page' => $next_page_id_val
			);
			$radio_next_page = array(
				'radio_next_page' => $radio_next_page
			);
			$data = array_merge((array)$next_page, (array)$radio_next_page);
		
		}
		
		$button_data = serialize( $data );
		
		// update wp_survey_question table to change question_status
		$wpdb->update(
			$wp_survey_question, 
			array( 
				'question_data' => $button_data,
				'date_modified' => $date
				),
			array( 'question_id' => $question_id ),
			array(
				'%s',
				'%s'
				),
			array( '%d' )
		);

	}
	
	$survey_id = $_POST['hid_survey_id'];	
	$start_page_id = $_POST['sel_start_page'];
	$end_page_id = $_POST['sel_end_page'];
	
	// update wp_survey table to change start_page_id and end_page_id
	$wpdb->update(
		$wp_survey, 
		array(
			'fk_start_page_id' => $start_page_id,
			'fk_end_page_id' => $end_page_id,
		),
		array( 'survey_id' => $survey_id ),
		array(
			'%d',
			'%d'
		),
		array( '%d' )
	);
	
	$url = $_POST['hid_url'];	
	wp_redirect( $url );

}

/**
 * @method : edit_page()
 * @return : void
 * @desc : This function updates page_title in wp_survey_page,
 * 			question_status and question_data ie, next_page in wp_survey_question table
 */
function edit_page() {

	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	$date = date( 'Y-m-d H:i:s' );	
	$page_id = $_POST['hid_page_id'];
	
	// This query returns question_type from wp_survey_question table
	$query = "
				SELECT question_type
				FROM $wp_survey_question 
				WHERE fk_page_id = %d AND
				question_type != 'Button'
			";
	$row = $wpdb->get_row( $wpdb->prepare( $query, $page_id ));
	$question_type = $row->question_type;
	
	// If question_type = Radiobutton then
	// Add radio_next_page for all options
	if($question_type === 'Radiobutton') {
	
		$option_next_page = $_POST['sel_next_page'];
		$hid_option = $_POST['hid_option'];
		
		$index = 0;
		$previous = array();
		
		// Preparing an array as
		// option => next_page format
		// Added all "option => next_page" pair in previous variable
		foreach($option_next_page as $key => $val) {
		
			$data = array(
				$hid_option[$index] => $val
			);
			$current = array_merge((array)$previous, (array)$data);
			$previous = $current;
			
			$index++;
		
		}
		
		// This query returns question details from wp_survey_question table
		$query = "
					SELECT question_id, question_data
					FROM $wp_survey_question 
					WHERE fk_page_id = %d AND
					question_type = 'Button'
				";
		$row = $wpdb->get_row( $wpdb->prepare( $query, $page_id ));
		
		$question_id = $row->question_id;
		$question_data = $row->question_data;
		$question_data = unserialize( ( $question_data ) );
		$next_page_id = $question_data[next_page];
		
		// Fetch next_page from question_data and then 
		// append updated radio_next_page
		$next_page = array(
			'next_page' => $next_page_id
		);
		
		$radio_next_page = array(
			'radio_next_page' => $previous
		);
		
		$next_page_values = array_merge((array)$next_page, (array)$radio_next_page);
	
		$button_data = serialize( $next_page_values );
	
		// update wp_survey_question table to change question_status
		$wpdb->update(
			$wp_survey_question, 
			array( 
				'question_data' => $button_data,
				'date_modified' => $date
				),
			array( 'question_id' => $question_id ),
			array(
				'%s',
				'%s'
				),
			array( '%d' )
		);
		
	}
	
	// get the check box value & status from the hidden field
	$chk_ids = $_POST['hid_question_checked_ids'];
	
	// remove the first comma(,) from the string
	$chk_ids_mod = substr($chk_ids, 1);
	
	// get the value||status pair as array
	$chk_ids_ex = explode(",", $chk_ids_mod);
	
	foreach($chk_ids_ex as $chk_id_pipe) {
		
		// get each value and status separately
		list($chk_id, $chk_status) = explode("||", $chk_id_pipe);
		
		// update wp_survey_question table to change question_status
		$wpdb->update(
			$wp_survey_question, 
			array( 
				'question_status' => $chk_status,
				'date_modified' => $date
				),
			array( 'question_id' => $chk_id ),
			array(
				'%s',
				'%s'
				),
			array( '%d' )
		);

	}
	
	$page_id = $_POST['hid_page_id'];
	
	$page_title = ( $_POST['txt_page_title'] );
	
	// update wp_survey_page table to change page_title
	$wpdb->update(
		$wp_survey_page, 
		array( 
			'page_title' => $page_title,
			'date_modified' => $date
			),
		array( 'page_id' => $page_id ),
		array( 
			'%s',
			'%s'
			),
		array( '%d' )
	);

	$url = $_POST['hid_url'];	
	wp_redirect( $url );

}

/**
 * @method : edit_question()
 * @return : void
 * @desc : This function updates question details in wp_survey_question table
 */
function edit_question() {

	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	$date = date( 'Y-m-d H:i:s' );
	
	$page_id = $_POST['hid_page_id'];
	$question_id = $_POST['hid_question_id'];
	
	// Check if the question type is Radiobutton then
	// Update its next_page values so that the previous
	// rad_option be removed from wp_survey_question table
	// This query returns question_type from wp_survey_question table
	$query = "
				SELECT question_type
				FROM $wp_survey_question 
				WHERE fk_page_id = %d AND
				question_type != 'Button'
			";
	$row = $wpdb->get_row( $wpdb->prepare( $query, $page_id ));
	$stored_question_type = $row->question_type;
	
	// If question_type = Radiobutton then
	// Remove radio_next_page
	if( $stored_question_type === 'Radiobutton' ) {
	
		// This query returns question details from wp_survey_question table
		$query = "
					SELECT question_id, question_data
					FROM $wp_survey_question 
					WHERE fk_page_id = %d AND
					question_type = 'Button'
				";
		$row = $wpdb->get_row( $wpdb->prepare( $query, $page_id ));
		
		$stored_question_id = $row->question_id;
		$stored_question_data = $row->question_data;
		$stored_question_data = unserialize( ( $stored_question_data ) );
		$next_page_id = $stored_question_data[next_page];
		
		// Fetch next_page from question_data and then 
		// update next_page such that radio_next_page is removed
		$next_page = array(
			'next_page' => $next_page_id
		);
	
		$button_data = serialize( $next_page );
	
		// update wp_survey_question table to change question_data
		$wpdb->update(
			$wp_survey_question, 
			array( 
				'question_data' => $button_data,
				'date_modified' => $date
				),
			array( 'question_id' => $stored_question_id ),
			array(
				'%s',
				'%s'
				),
			array( '%d' )
		);
		
	}
	
	$question = wp_filter_kses( $_POST['txt_question'] );	
	$question_type = $_POST['sel_question_type'];
	
	// get the txt_option's value from the hidden field
	$hid_option_ids = $_POST['hid_option_ids'];
	
	// remove the first comma(,) from the string
	$hid_ids_mod = substr($hid_option_ids, 1);
	
	$hid_ids_mod = wp_filter_kses( $hid_ids_mod );
	
	// get the text_option's values in array
	$option = explode("|", $hid_ids_mod);
		
	if ( $question_type === "Textbox") {
	
		$data = array(
			'question' => $question
		);
		
	} else {
	
		$data = array(
			'question' => $question,
			'option' => $option
		);
	
	}
	
	$question_data = serialize( $data );
	
	// update wp_survey_question table to change question_type, question_data and date_modified
	$wpdb->update(
		$wp_survey_question, 
		array( 
			'question_type' => $question_type,
			'question_data' => $question_data,
			'date_modified' => $date
			),
		array( 
			'question_id' => $question_id
			),
		array( 
			'%s',
			'%s',
			'%s'
			),
		array( 
			'%d'
			)
	);
	
	$url = $_POST['hid_url'];	
	wp_redirect( $url );

}

/**
 * @method : delete_survey()
 * @return : void
 * @desc : This function deletes survey from wp_survey table and
 *			its details from wp_survey_page and wp_survey_question table
 */
function delete_survey() {
	
	// Includes survey-tables.php
	require_once( __DIR__ . '/survey-tables.php' );
	
	$survey_id = $_GET['survey_id'];
	
	// Convert to int
	$survey_id = intval( $survey_id );
	
	// Deletes Survey from wp_survey corresponding to survey_id
	$wpdb->query(
		$wpdb->prepare(
			"
			DELETE FROM ".$wp_survey."
			WHERE survey_id = %d
			",
			$survey_id
		)
	);
	
	// This query returns survey details from wp_survey table
	$query = 
		"
			SELECT survey_id
			FROM $wp_survey
			WHERE survey_id = %d
		";	
	
	// The function get_var() returns NULL if survey_id is deleted
	$deleted_survey_id = $wpdb->get_var( $wpdb->prepare ( $query, $survey_id ));
	
	// Convert to int
	$deleted_survey_id = intval( $deleted_survey_id );
	
	if ( $deleted_survey_id === 0 ) {
		
		// Deletes Question from wp_survey_question corresponding to survey_id
		$wpdb->query(
			$wpdb->prepare(
				"
				DELETE FROM ".$wp_survey_question."
				WHERE fk_page_id IN
					(
						SELECT page_id
						FROM ".$wp_survey_page."
						WHERE fk_survey_id = %d
					)
				",
				$survey_id
			)
		);
		
		// Deletes Page from wp_survey_page corresponding to survey_id
		$wpdb->query(
			$wpdb->prepare(
				"
				DELETE FROM ".$wp_survey_page."
				WHERE fk_survey_id = %d
				",
				$survey_id
			)
		);		
	
	}	
	
	// redirect to manage-survey
	wp_redirect( 'admin.php?page=add-survey' );

}
?>