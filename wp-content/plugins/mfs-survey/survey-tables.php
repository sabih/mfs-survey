<?php
/**
 * To include all survey tables
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	survey-tables.php
 */

// Include all survey tables

global $wpdb;
$wp_survey = $wpdb->prefix . "mfs_survey";
$wp_survey_page = $wpdb->prefix . "mfs_survey_page";
$wp_survey_question = $wpdb->prefix . "mfs_survey_question";
$wp_survey_answer = $wpdb->prefix . "mfs_survey_answer";
$wp_survey_result = $wpdb->prefix . "mfs_survey_result";
?>