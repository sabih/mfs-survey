/**
 * This validates edit-question.php
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	edit-question-script.js
 */

/**
 * @method : show_remove_button()
 * @return : void
 * @desc : This function display "-" Remove button if there is more than 1 option
 *			for a particular question
 */
function show_remove_button() {

	// To remove conflict
	jQuery.noConflict();

	jQuery("#btn_remove_option").show();

}