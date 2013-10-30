<?php
/**
 * To populate page droplist corresponding to survey_id
 *
 * @package		WordPress
 * @subpackage	mf-survey
 * @filename	ajax-survey.php
 */

/**
 * Includes wp-load.php
 */
require_once("../../../../wp-load.php");

/**
 * Includes list.php to populate page droplist
 */
require_once( __DIR__ . '/../list.php' );

$survey_id = $_POST["data_survey_id"];
$options = "";
$options .= "<option value=''>-- ".__('Please Choose', 'mf-survey')." --</option>";

// Call function populate_page_droplist with survey_id as parameter
$options .= populate_page_droplist( $survey_id, '' );
echo $options;
?>