/**
 * This validates display-survey.php
 *
 * @package		WordPress
 * @subpackage	mfs-survey
 * @filename	display-survey-script.js
 */

/**
 * @method : validate_txt_answer()
 * @return : void
 * @desc : This function checks if txt_answer is empty on keyup
 */
function validate_txt_answer() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if txt_answer is empty
	// (If) empty, change textbox border color to red
	// (Else) default textbox border color
	if(jQuery("#txt_answer").val() === "") {
	
		jQuery("#txt_answer").css("border","1px solid red");
	
	} else {
	
		jQuery("#txt_answer").css("border","1px solid #CCCCCC");
	
	}
	
}

/**
 * @method : validate_rad_option()
 * @return : void
 * @desc : This function checks if radio option is empty on click
 */
function validate_rad_option() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if rad_option is empty
	// (If) empty, change tbl_radio border color to red
	// (Else) tbl_radio border color to none
	if(jQuery(".rad_option:checked").attr("checked") != "checked") {
	
		jQuery("#tbl_radio").css("border","1px solid red");
		
	} else {
	
		jQuery("#tbl_radio").css("border","none");
	
	}
	
}

/**
 * @method : validate_checkbox_option()
 * @return : void
 * @desc : This function checks if checkbox option is empty on click
 */
function validate_checkbox_option() {

	// To remove conflict
	jQuery.noConflict();
	
	// checks if chk_option is empty
	// (If) empty, change tbl_checkbox border color to red
	// (Else) tbl_checkbox border color to none
	if(jQuery(".chk_option:checked").attr("checked") != "checked") {
	
		jQuery("#tbl_checkbox").css("border","1px solid red");
		
	} else {
	
		jQuery("#tbl_checkbox").css("border","none");
	
	}
	
}

// To remove conflict
jQuery.noConflict();

/**
 * This validates form(form_display_page) from inserting empty values
 */ 
jQuery(document).ready(function($) {

	jQuery("#form_diplay_page").submit(function(e){
		
		e.preventDefault();		
		var empty_flag = 1;
		var answer;
		
		if(jQuery(".rad_option").val() && jQuery(".rad_option:checked").attr("checked") === "checked") {
		
			answer = jQuery(".rad_option:checked").val();
		
		} else if(jQuery(".rad_option").val() && jQuery(".rad_option:checked").attr("checked") != "checked") {
		
			jQuery("#tbl_radio").css("border","1px solid red");
			jQuery("#tbl_radio").focus();
			empty_flag = 0;
		
		}
		
		if(jQuery(".chk_option").val() && jQuery(".chk_option:checked").attr("checked") === "checked") {
			
			var count_checkbox =  jQuery('.chk_option').length;
			var checkkbox_id;
			var answers = "";
			var chk_flag;
			
			for( var i = 0; i < count_checkbox; i++ ) {
			
				chk_flag = jQuery('#chk_option' + i).is(":checked"); 
				
				if (chk_flag === true) {
				
					checkkbox_id = jQuery("#chk_option"+i+":checked").val();					
					answers += checkkbox_id + ',';
					
				}
				
			}
			
			answer = answers.slice(0,-1);
		
		} else if(jQuery(".chk_option").val() && jQuery(".chk_option:checked").attr("checked") != "checked") {
		
			jQuery("#tbl_checkbox").css("border","1px solid red");
			jQuery("#tbl_checkbox").focus();
			empty_flag = 0;
		
		}
		
		if(jQuery("#txt_answer").val() && jQuery("#txt_answer").val() != "") {
		
			answer = jQuery("#txt_answer").val();
		
		} else if (jQuery("#txt_answer").val() === "") {
		
			jQuery("#txt_answer").css("border","1px solid red");
			jQuery("#txt_answer").focus();
			empty_flag = 0;
		
		}
		
		if(empty_flag === 0) {
		
			return false;			
		
		}
		
		var url = jQuery("#hid_url").val();
		var result_id = jQuery("#hid_result_id").val();
		var question_id = jQuery("#hid_question_id").val();
		var question = jQuery("#hid_question").val();
		var data = {
			action:'save_answer',
			data_result_id: result_id,
			data_question_id: question_id,
			data_question: question,
			data_answer: answer
			
		};
		
		/*$.ajax({
			type: "POST",
			url: url,
			data: data,
			success: function() {window.location.reload(true);}
			//dataType: "HTML"
		});*/
		jQuery.post(ajaxcallpara.ajax_url, data, function(response) {
			window.location.reload();
		});
	});

});