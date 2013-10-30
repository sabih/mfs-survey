<?php
/**
 * To include all survey tables
 *
 * @package	WordPress
 * @subpackage	mf-survey
 * @filename	survey-tables.php
 */

// Include all survey tables

global $wpdb;
$wp_survey = $wpdb->prefix . "survey";
$wp_survey_page = $wpdb->prefix . "survey_page";
$wp_survey_question = $wpdb->prefix . "survey_question";
$wp_survey_answer = $wpdb->prefix . "survey_answer";
$wp_survey_result = $wpdb->prefix . "survey_result";
?>