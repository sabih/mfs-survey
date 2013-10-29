<?php
/**
 * To create survey tables when plugin activates and drop tables when plugin deactivates
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	install-uninstall-tables.php
 */

/**
 * @method : install()
 * @return : void
 * @desc : This function creates survey tables
 */
function install() {

	global $wpdb;
	
	$wp_survey = $wpdb->prefix . "mfs_survey";
	$wp_survey_page = $wpdb->prefix . "mfs_survey_page";
	$wp_survey_question = $wpdb->prefix . "mfs_survey_question";
	$wp_survey_answer = $wpdb->prefix . "mfs_survey_answer";
	$wp_survey_result = $wpdb->prefix . "mfs_survey_result";
	
	$survey_table = "CREATE TABLE IF NOT EXISTS $wp_survey (
		survey_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		fk_user_id bigint(20) unsigned NOT NULL,
		fk_start_page_id bigint(20) unsigned NOT NULL,
		fk_end_page_id bigint(20) unsigned NOT NULL,
		survey_name varchar(100) NOT NULL,
		survey_status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A:Active, I:Inactive',
		publish_status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A:Active, I:Inactive',
		date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (survey_id),
		KEY fk_user_id (fk_user_id),
		KEY fk_start_page_id (fk_start_page_id),
		KEY fk_end_page_id (fk_end_page_id)
	);";
	
	$survey_page_table = "CREATE TABLE IF NOT EXISTS $wp_survey_page (
		page_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		fk_survey_id bigint(20) unsigned NOT NULL,
		page_title varchar(100) NOT NULL,
		date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		date_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		page_status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A:Active, I:Inactive',
		PRIMARY KEY (page_id),
		KEY fk_survey_id (fk_survey_id)
	);";
	
	$survey_question_table = "CREATE TABLE IF NOT EXISTS $wp_survey_question (
		question_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		fk_page_id bigint(20) unsigned NOT NULL,
		question_sort bigint(20) NOT NULL DEFAULT '0',
		question_type varchar(100) NOT NULL DEFAULT '0',
		question_data longtext NOT NULL,
		date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		date_modified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		question_status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A:Active, I:Inactive',
		PRIMARY KEY (question_id),
		KEY fk_page_id (fk_page_id)
	);";
	
	$survey_answer_table = "CREATE TABLE IF NOT EXISTS $wp_survey_answer (
		answer_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		fk_user_id bigint(20) unsigned NOT NULL,
		fk_result_id bigint(20) unsigned NOT NULL,
		fk_question_id bigint(20) unsigned NOT NULL,
		question text NOT NULL,
		answer text NOT NULL,
		date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (answer_id),
		KEY fk_user_id (fk_user_id),
		KEY fk_result_id (fk_result_id),
		KEY fk_question_id (fk_question_id)
	);";
	
	$survey_result_table = "CREATE TABLE IF NOT EXISTS $wp_survey_result (
		result_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		fk_user_id bigint(20) unsigned NOT NULL,
		fk_survey_id bigint(20) unsigned NOT NULL,		
		result_status enum('A','I') NOT NULL DEFAULT 'A' COMMENT 'A:Active, I:Inactive',
		date_created datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		PRIMARY KEY (result_id),
		KEY fk_user_id (fk_user_id),
		KEY fk_survey_id (fk_survey_id)
	);";
	
	$wpdb->query( $survey_table );
	$wpdb->query( $survey_page_table );
	$wpdb->query( $survey_question_table );
	$wpdb->query( $survey_answer_table );
	$wpdb->query( $survey_result_table );
	
}

/**
 * @method : uninstall()
 * @return : void
 * @desc : This function drops survey tables
 */
function uninstall() {

	global $wpdb;
	
	$wp_survey = $wpdb->prefix . "mfs_survey";
	$wp_survey_page = $wpdb->prefix . "mfs_survey_page";
	$wp_survey_question = $wpdb->prefix . "mfs_survey_question";
	$wp_survey_answer = $wpdb->prefix . "mfs_survey_answer";
	$wp_survey_result = $wpdb->prefix . "mfs_survey_result";
	
	$survey_table = "DROP TABLE IF EXISTS $wp_survey;";
	$survey_page_table = "DROP TABLE IF EXISTS $wp_survey_page;";
	$survey_question_table = "DROP TABLE IF EXISTS $wp_survey_question;";
	$survey_answer_table = "DROP TABLE IF EXISTS $wp_survey_answer;";
	$survey_result_table = "DROP TABLE IF EXISTS $wp_survey_result;";
	
	$wpdb->query( $survey_table );
	$wpdb->query( $survey_page_table );
	$wpdb->query( $survey_question_table );
	$wpdb->query( $survey_answer_table );
	$wpdb->query( $survey_result_table );
	
}

?>