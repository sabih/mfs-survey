<?php
/**
 * To display the total survey details
 *
 * @package	WordPress
 * @subpackage	mfs-survey
 * @filename	manage-survey.php
 */

/**
 * Includes survey-tables.php
 */
require_once( __DIR__ . '/../survey-tables.php' );

/**
 * To include manage-survey-script.js file
 */
wp_register_script( 'manage-survey-script', plugins_url() . '/mfs-survey/scripts/manage-survey-script.js', 'jquery' );
wp_enqueue_script( 'manage-survey-script' );

wp_register_script( 'jquery-min', plugins_url() . '/mfs-survey/scripts/jquery/jquery.min.js', 'jquery' );
wp_enqueue_script( 'jquery-min' );

wp_register_script( 'jquery-ui-min', plugins_url() . '/mfs-survey/scripts/jquery/jquery-ui.min.js', 'jquery');
wp_enqueue_script( 'jquery-ui-min' );

/**
 * To include survey-style.css file
 */
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/jquery-ui.css', '' );
wp_enqueue_style( '', plugins_url() . '/mfs-survey/styles/survey-style.css', '' );

// Passing alert message as an array for translation to be applicable
$translation_array = array (
						'publish_survey' => __( 'Once published you are unable to unpublish!' ),
						'delete_survey' => __( 'Are you sure to delete this Survey?' )
					);
wp_localize_script( 'manage-survey-script', 'manage_survey_object', $translation_array );
?>

<div class="wrap">
	<table width="100%">
		<tbody>
			<tr>
				<td width="50%">
					<h2 style="float:left;"><?php _e('Manage Survey', 'mfs-survey'); ?></h2>
				</td>
				<td width="50%">
					<div style="float:right;">						
						<?php							
							// This query counts total surveys from wp_survey table
							$query = "SELECT count(survey_id) FROM $wp_survey";						
							
							$count = $wpdb->get_var($query);
							
							//pagination script
							$limit = 12;
							$paged = $_GET['paged'];
							$current = max( 1, $paged );
							$total_pages = ceil($count / $limit);
							$start = $current * $limit - $limit;							
								
							// This query returns survey details from wp_survey table
							$query = 
								"
									SELECT survey_id, survey_name, survey_status, publish_status 
									FROM $wp_survey 
									ORDER BY survey_id DESC
									LIMIT $start, $limit
								";
							$results = $wpdb->get_results( $query );
						?>							
							
						<div class="tablenav top">
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo $count?> <?php echo __('items'); ?></span>
								<?php
								echo paginate_links(
									array(
										'current' 	=> $current,
										'prev_text'	=> '&laquo; ' . __('Prev'),
										'next_text'    => __('Next') . ' &raquo;',
										'base' 		=> @add_query_arg('paged','%#%'),
										'format'  	=>  '?page=%#%',
										'total'   	=> $total_pages
									)
								);
								?>
							</div>								
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	
	<!-- This form displays all the available surveys with active and inactive status in checkbox -->
	<form action="edit.php?page=mfs-survey/survey-action.php&amp;noheader=true" method="post" name="form_manage_survey" id="form_manage_survey" autocomplete="off">	
		
		<table class="widefat page fixed" id="tbl_manage_survey" cellspacing="0">
			<thead>
				<tr>
					<th><?php _e('Survey Name', 'mfs-survey'); ?></th>
					<th width="6%"><?php _e('Active', 'mfs-survey'); ?></th>
					<th></th>
					<th width="5%"></th>
					<th width="6%"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$chk_box_id_incr = 0;
				
				if( count( $results ) > 0 ) {
					
					// Displays survey_name and survey_status for all surveys
					// Checkbox checked if survey_status is active and unchecked if inactive
					foreach( $results as $result ) {
						
						$survey_id = $result->survey_id;
						echo "<tr><td>";
						echo stripslashes_deep( $result->survey_name );
						echo "</td><td><input type='checkbox' id='chk_survey$chk_box_id_incr' name='chk_survey[]'";
							if( $result->survey_status === 'A' ) {
								echo "checked='checked'";
							}
						echo " onclick='survey_checkbox_status(" . $survey_id . ", " . $chk_box_id_incr . ");'/></td>";
						$chk_box_id_incr++;
						
						// If survey is not published then display "Publish", "Edit" and "Delete" link
						// Else remove all link and diplay "Published"
						if ( $result->publish_status === 'A' ) {
						
							// This query returns page details from wp_survey_page table
							$query = 
							"
								SELECT count( page_id )
								FROM $wp_survey_page 
								WHERE fk_survey_id = %d
							";
							$count_pages = $wpdb->get_var( $wpdb->prepare( $query, $survey_id ) );
						
							if( $count_pages > 0 ) {
						
								$survey_publish_url = "edit.php?page=mfs-survey/survey-action.php&amp;noheader=true?&action=publish_survey&survey_id=".$result->survey_id;
						
								// Publish link
								echo "
									<td style='text-align:right;'>
										<a onclick='publish_survey(\"$survey_publish_url\");' href='javascript:void(0);'>".__('Publish', 'mfs-survey')."</a>
									</td>";
								
								// Edit link
								echo "
									<td style='text-align:right;'>
										<a href='admin.php?page=forms/edit-survey-form.php&survey_id=".$survey_id."'>".__('Edit', 'mfs-survey')."</a>
									</td>";
									
							} else {
							
								echo '<td style="text-align:right;" colspan="2">'.__('Add page for this survey', 'mfs-survey').'</td>';
							
							}
						
							$survey_delete_url = "edit.php?page=mfs-survey/survey-action.php&amp;noheader=true?&action=delete_survey&survey_id=".$result->survey_id;
						
							// Delete link
							echo "
								<td style='text-align:right;'>
									<a onclick='delete_survey(\"$survey_delete_url\");' href='javascript:void(0);'>".__('Delete', 'mfs-survey')."</a>
								</td></tr>"; 
								
						} else {
							
							// Published
							echo "
								<td></td>
								<td></td>
								<td style='text-align:right;'>
									".__('Published', 'mfs-survey')."
								</td>";
							
						}
					
					}
					
				} else {
					?>
					<tr>
						<th colspan="5">
							<?php _e('Please create new Survey!', 'mfs-survey'); ?>
						</th>
					</tr>
					<?php
				}
				
				$current_page_url = $_SERVER["REQUEST_URI"];
				
				?>
			</tbody>
		</table>
		
		<input type="hidden" name="hid_url" id="hid_url" value="<?php echo $current_page_url; ?>" />
		<input type="hidden" name="hid_survey_checked_ids" id="hid_survey_checked_ids" value="" />
		
		<?php if( $chk_box_id_incr !== 0 ) { ?>
			<p class="submit">
				<input type="submit" name="btn_manage_survey" id="btn_manage_survey" value="<?php _e('Save', 'mfs-survey'); ?>" class="button" />
			</p>
		<?php } ?>
		
	</form>	
</div>

<div>
	<?php if( $chk_box_id_incr === 0 ) { echo "<br />"; } ?>
	<a href="admin.php?page=forms/survey-form"><?php _e('Create New Survey', 'mfs-survey'); ?></a>
</div>