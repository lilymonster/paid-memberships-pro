<?php
	global $wpdb, $msg, $msgt, $pmpro_currency_symbol;

	//some vars
	if(isset($_REQUEST['edit']))
		$edit = $_REQUEST['edit'];	
	else
		$edit = false;
	if(isset($_REQUEST['copy']))
		$copy = $_REQUEST['copy'];
	if(isset($_REQUEST['s']))
		$s = $_REQUEST['s'];
	else
		$s = "";
	
	if(isset($_REQUEST['action']))
		$action = $_REQUEST['action'];
	else
		$action = false;
		
	if(isset($_REQUEST['saveandnext']))
		$saveandnext = $_REQUEST['saveandnext'];

	if(isset($_REQUEST['saveid']))
		$saveid = $_REQUEST['saveid'];
	if(isset($_REQUEST['deleteid']))
		$deleteid = $_REQUEST['deleteid'];

	if($action == "save_membershiplevel")
	{
		$ml_name = addslashes($_REQUEST['name']);
		$ml_description = addslashes($_REQUEST['description']);
		$ml_initial_payment = addslashes($_REQUEST['initial_payment']);
		$ml_recurring = $_REQUEST['recurring'];
		$ml_billing_amount = addslashes($_REQUEST['billing_amount']);
		$ml_cycle_number = addslashes($_REQUEST['cycle_number']);
		$ml_cycle_period = addslashes($_REQUEST['cycle_period']);		
		$ml_billing_limit = addslashes($_REQUEST['billing_limit']);
		$ml_custom_trial = $_REQUEST['custom_trial'];
		$ml_trial_amount = addslashes($_REQUEST['trial_amount']);
		$ml_trial_limit = addslashes($_REQUEST['trial_limit']);  
		$ml_expiration = $_REQUEST['expiration'];
		$ml_expiration_number = addslashes($_REQUEST['expiration_number']);
		$ml_expiration_period = addslashes($_REQUEST['expiration_period']);
		$ml_categories = array();
		$ml_disable_signups = $_REQUEST['disable_signups'];
		if($ml_disable_signups)
			$ml_allow_signups = 0;
		else
			$ml_allow_signups = 1;

		foreach ( $_REQUEST as $key => $value )
		{
			if ( $value == 'yes' && preg_match( '/^membershipcategory_(\d+)$/i', $key, $matches ) )
			{
				$ml_categories[] = $matches[1];
			}
		}

		if ( $ml_recurring != "yes" )
		{
			$ml_billing_amount = $ml_cycle_number = $ml_cycle_period = $ml_billing_limit = $ml_trial_amount = $ml_trial_limit = 0;
		}
		elseif ( $ml_custom_trial != "yes" )
		{
			$ml_trial_amount = $ml_trial_limit = 0;
		}

		if($ml_expiration != "yes")
		{
			$ml_expiration_number = $ml_expiration_period = 0;
		}

		if($saveid > 0)
		{
			$sqlQuery = " UPDATE {$wpdb->pmpro_membership_levels}
						SET name = '$ml_name',
						  description = '$ml_description',
						  initial_payment = '$ml_initial_payment',
						  billing_amount = '$ml_billing_amount',
						  cycle_number = '$ml_cycle_number',
						  cycle_period = '$ml_cycle_period',
						  billing_limit = '$ml_billing_limit',
						  trial_amount = '$ml_trial_amount',
						  trial_limit = '$ml_trial_limit',                    
						  expiration_number = '$ml_expiration_number',
						  expiration_period = '$ml_expiration_period',
						  allow_signups = '$ml_allow_signups'
						WHERE id = '$saveid' LIMIT 1;";	 
			$wpdb->query($sqlQuery);
			
			pmpro_updateMembershipCategories( $saveid, $ml_categories );
			if(!mysql_errno())
			{
				$edit = false;
				$msg = 2;
				$msgt = "Membership level updated successfully.";												
			}
			else
			{     
				$msg = -2;
				$msg = true;
				$msgt = "Error updating membership level.";					
			}
		}
		else
		{
			$sqlQuery = " INSERT INTO {$wpdb->pmpro_membership_levels}
						( name, description, initial_payment, billing_amount, cycle_number, cycle_period, billing_limit, trial_amount, trial_limit, expiration_number, expiration_period, allow_signups)
						VALUES
						( '$ml_name', '$ml_description', '$ml_initial_payment', '$ml_billing_amount', '$ml_cycle_number', '$ml_cycle_period', '$ml_billing_limit', '$ml_trial_amount', '$ml_trial_limit', '$ml_expiration_number', '$ml_expiration_period', '$ml_allow_signups' )";
			$wpdb->query($sqlQuery);
			if(!mysql_errno())
			{
				pmpro_updateMembershipCategories( $wpdb->insert_id, $ml_categories );
				
				$edit = false;
				$msg = 1;
				$msgt = "Membership level added successfully.";								
			}
			else
			{
				$msg = -1;				
				$msgt = "Error adding membership level.";
			}
		}
	}	
	elseif($action == "delete_membership_level")
	{
		global $wpdb;

		$ml_id = $_REQUEST['deleteid'];
	  
		if($ml_id > 0)
		{	  
			//remove any categories from the ml
			$sqlQuery = "DELETE FROM $wpdb->pmpro_memberships_categories WHERE membership_id = '$ml_id'";	  			
			$r1 = $wpdb->query($sqlQuery);
							
			//cancel any subscriptions to the ml
			$r2 = true;
			$user_ids = $wpdb->get_col("SELECT user_id FROM $wpdb->pmpro_memberships_users WHERE membership_id = '$ml_id'");			
			foreach($user_ids as $user_id)
			{
				//change there membership level to none. that will handle the cancel
				if(pmpro_changeMembershipLevel(0, $user_id))
				{
					//okay
				}
				else
				{
					//couldn't delete the subscription
					//we should probably notify the admin	
					$pmproemail = new PMProEmail();			
					$pmproemail->data = array("body"=>"<p>There was an error canceling the subscription for user with ID=" . $user_id . ". You will want to check your payment gateway to see if their subscription is still active.</p>");
					$last_order = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_orders WHERE user_id = '" . $user_id . "' ORDER BY timestamp DESC LIMIT 1");
					if($last_order)
						$pmproemail->data["body"] .= "<p>Last Invoice:<br />" . nl2br(var_export($last_order, true)) . "</p>";
					$pmproemail->sendEmail(get_bloginfo("admin_email"));	

					$r2 = false;
				}	
			}					
			
			//delete the ml
			$sqlQuery = "DELETE FROM $wpdb->pmpro_membership_levels WHERE id = '$ml_id' LIMIT 1";	  			
			$r3 = $wpdb->query($sqlQuery);
					
			if($r1 !== FALSE && $r2 !== FALSE && $r3 !== FALSE)
			{
				$msg = 3;
				$msgt = "Membership level deleted successfully.";
			}
			else
			{
				$msg = -3;
				$msgt = "Error deleting membership level.";	
			}
		}
		else
		{
			$msg = -3;
			$msgt = "Error deleting membership level.";	
		}
	}  
		
	require_once(dirname(__FILE__) . "/admin_header.php");		
?>

<?php	
	if($edit)
	{			
	?>
		
	<h2>
		<?php
			if($edit > 0)
				echo "Edit Membership Level";
			else
				echo "Add New Membership Level";
		?>
	</h2>
		
	<div>
		<?php
			// get the level...
			if($edit > 0)
			{
				$level = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = '$edit' LIMIT 1", OBJECT);
				$temp_id = $level->id;
			}
			elseif($copy > 0)		
			{	
				$level = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = '$copy' LIMIT 1", OBJECT);
				$temp_id = $level->id;
				$level->id = NULL;
			}

			// didn't find a membership level, let's add a new one...
			if(!$level) $edit = -1;

			//defaults for new levels
			if($edit == -1)
			{
				$level = new stdClass();
				$level->cycle_number = 1;
				$level->cycle_period = "Month";
			}
			
			// grab the categories for the given level...
			$level->categories = $wpdb->get_col("SELECT c.category_id
												FROM $wpdb->pmpro_memberships_categories c
												WHERE c.membership_id = '" . $temp_id . "'");       		
			if(!$level->categories)
				$level->categories = array();	
			
		?>
		<form action="" method="post" enctype="multipart/form-data">
			<input name="saveid" type="hidden" value="<?php echo $edit?>" />
			<input type="hidden" name="action" value="save_membershiplevel" />
			<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label>ID:</label></th>
					<td><?php echo $level->id?></td>
				</tr>								                
				
				<tr>
					<th scope="row" valign="top"><label for="name">Name:</label></th>
					<td><input name="name" type="text" size="50" value="<?php echo str_replace("\"", "&quot;", stripslashes($level->name))?>" /></td>
				</tr>
				
				<tr>
					<th scope="row" valign="top"><label for="description">Description:</label></th>
					<td>
						<div id="poststuff" class="pmpro_description">
						<textarea rows="10" cols="80" name="description" id="description"><?php echo str_replace("\"", "&quot;", stripslashes($level->description))?></textarea>							
						</div>    
					</td>
				</tr>
			</tbody>
		</table>
		
		<h3 class="topborder">Billing Details</h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label for="initial_payment">Initial Payment:</label></th>
					<td><?php echo $pmpro_currency_symbol?><input name="initial_payment" type="text" size="20" value="<?php echo str_replace("\"", "&quot;", stripslashes($level->initial_payment))?>" /> <small>The initial amount collected at registration.</small></td>
				</tr>
				
				<tr>
					<th scope="row" valign="top"><label>Recurring Subscription:</label></th>
					<td><input id="recurring" name="recurring" type="checkbox" value="yes" <?php if(pmpro_isLevelRecurring($level)) { echo "checked='checked'"; } ?> onclick="if(jQuery('#recurring').is(':checked')) { jQuery('.recurring_info').show(); if(jQuery('#custom_trial').is(':checked')) {jQuery('.trial_info').show();} else {jQuery('.trial_info').hide();} } else { jQuery('.recurring_info').hide();}" /> <small>Check if this level has a recurring subscription payment.</small></td>
				</tr>
				
				<tr class="recurring_info" <?php if(!pmpro_isLevelRecurring($level)) {?>style="display: none;"<?php } ?>>
					<th scope="row" valign="top"><label for="billing_amount">Billing Amount:</label></th>
					<td>
						<?php echo $pmpro_currency_symbol?><input name="billing_amount" type="text" size="20" value="<?php echo str_replace("\"", "&quot;", stripslashes($level->billing_amount))?>" /> <small>per</small>
						<input id="cycle_number" name="cycle_number" type="text" size="10" value="<?php echo str_replace("\"", "&quot;", stripslashes($level->cycle_number))?>" />
						<select id="cycle_period" name="cycle_period">
						  <?php
							$cycles = array( 'Day(s)' => 'Day', 'Week(s)' => 'Week', 'Month(s)' => 'Month', 'Year(s)' => 'Year' );
							foreach ( $cycles as $name => $value ) {
							  echo "<option value='$value'";
							  if ( $level->cycle_period == $value ) echo " selected='selected'";
							  echo ">$name</option>";
							}
						  ?>
						</select>
						<br /><small>The amount to be billed one cycle after the initial payment.</small>							
					</td>
				</tr>                                        
				
				<tr class="recurring_info" <?php if(!pmpro_isLevelRecurring($level)) {?>style="display: none;"<?php } ?>>
					<th scope="row" valign="top"><label for="billing_limit">Billing Cycle Limit:</label></th>
					<td>
						<input name="billing_limit" type="text" size="20" value="<?php echo $level->billing_limit?>" />
						<br /><small>The <strong>total</strong> number of recurring billing cycles for this level, including the trial period (if applicable) but not including the initial payment. Set to zero if membership is indefinite.</small>
					</td>
				</tr>            								

				<tr class="recurring_info" <?php if (!pmpro_isLevelRecurring($level)) echo "style='display:none;'";?>>
					<th scope="row" valign="top"><label>Custom Trial:</label></th>
					<td><input id="custom_trial" name="custom_trial" type="checkbox" value="yes" <?php if ( pmpro_isLevelTrial($level) ) { echo "checked='checked'"; } ?> onclick="jQuery('.trial_info').toggle();" /> Check to add a custom trial period.</td>
				</tr>

				<tr class="trial_info recurring_info" <?php if (!pmpro_isLevelTrial($level)) echo "style='display:none;'";?>>
					<th scope="row" valign="top"><label for="trial_amount">Trial Billing Amount:</label></th>
					<td>
						<?php echo $pmpro_currency_symbol?><input name="trial_amount" type="text" size="20" value="<?php echo str_replace("\"", "&quot;", stripslashes($level->trial_amount))?>" />
						<small>for the first</small>
						<input name="trial_limit" type="text" size="10" value="<?php echo str_replace("\"", "&quot;", stripslashes($level->trial_limit))?>" />
						<small>subscription payments.</small>																			
					</td>
				</tr>
									 
			</tbody>
		</table>
		<h3 class="topborder">Other Settings</h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label>Disable New Signups:</label></th>
					<td><input name="disable_signups" type="checkbox" value="yes" <?php if($level->id && !$level->allow_signups) { ?>checked="checked"<?php } ?> /> Check to hide this level from the membership levels page and disable registration.</td>
				</tr>
				
				<tr>
					<th scope="row" valign="top"><label>Membership Expiration:</label></th>
					<td><input id="expiration" name="expiration" type="checkbox" value="yes" <?php if(pmpro_isLevelExpiring($level)) { echo "checked='checked'"; } ?> onclick="if(jQuery('#expiration').is(':checked')) { jQuery('.expiration_info').show(); } else { jQuery('.expiration_info').hide();}" /> Check this to set an expiration date for new sign ups.</td>
				</tr>
				
				<tr class="expiration_info" <?php if(!pmpro_isLevelExpiring($level)) {?>style="display: none;"<?php } ?>>
					<th scope="row" valign="top"><label for="billing_amount">Expire In:</label></th>
					<td>							
						<input id="expiration_number" name="expiration_number" type="text" size="10" value="<?php echo str_replace("\"", "&quot;", stripslashes($level->expiration_number))?>" />
						<select id="expiration_period" name="expiration_period">
						  <?php
							$cycles = array( 'Day(s)' => 'Day', 'Week(s)' => 'Week', 'Month(s)' => 'Month', 'Year(s)' => 'Year' );
							foreach ( $cycles as $name => $value ) {
							  echo "<option value='$value'";
							  if ( $level->expiration_period == $value ) echo " selected='selected'";
							  echo ">$name</option>";
							}
						  ?>
						</select>
						<br /><small>How long before the expiration expires. Not that any future payments will be canceled when the membership expires.</small>							
					</td>
				</tr> 
			</tbody>
		</table>
		<h3 class="topborder">Content Settings</h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label>Categories:</label></th>
					<td>
						<?php
						$categories = get_categories( array( 'hide_empty' => 0 ) );
						echo "<ul>";
						foreach ( $categories as $cat )
						{                               								
							$checked = in_array( $cat->term_id, $level->categories ) ? "checked='checked'" : '';
							echo "<li><input name='membershipcategory_{$cat->term_id}' type='checkbox' value='yes' $checked /> {$cat->name}</li>\n";
						}
						echo "</ul>";
						?>
					</td>
				</tr>
			</tbody>
		</table>			
		<p class="submit topborder">
			<input name="save" type="submit" class="button-primary" value="Save Level" /> 					
			<input name="cancel" type="button" value="Cancel" onclick="location.href='<?php echo home_url('/wp-admin/admin.php?page=pmpro-membershiplevels')?>';" /> 					
		</p>
	</form>
	</div>
		
	<?php
	}	
	else
	{
	?>							
				
	<h2>Membership Levels <a href="admin.php?page=pmpro-membershiplevels&edit=-1" class="button add-new-h2">Add New Level</a></h2>
	<form id="posts-filter" method="get" action="">			
		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">Search Levels:</label>
			<input type="hidden" name="page" value="pmpro-membershiplevels" />
			<input id="post-search-input" type="text" value="<?php echo $s?>" name="s" size="30" />
			<input class="button" type="submit" value="Search Levels" id="search-submit "/>
		</p>		
	</form>	
	
	<br class="clear" />
	
	<table class="widefat">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Initial Payment</th>
			<th>Billing Cycle</th>        
			<th>Trial Cycle</th>
			<th>Expiration</th>
			<th>Allow Signups</th>
			<th></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$sqlQuery = "SELECT * FROM $wpdb->pmpro_membership_levels ";
			if($s)
				$sqlQuery .= "WHERE name LIKE '%$s%' ";
			$sqlQuery .= "ORDER BY id ASC";
			
			$levels = $wpdb->get_results($sqlQuery, OBJECT);
			
			foreach($levels as $level)
			{
		?>
		<tr <?php if(!$level->allow_signups) { ?>class="pmpro_gray"<?php } ?>>
			<td><?php echo $level->id?></td>
			<td><?php echo $level->name?></td>
			<td>
				<?php if(pmpro_isLevelFree($level)) { ?>
					FREE
				<?php } else { ?>
					<?php echo $pmpro_currency_symbol?><?php echo $level->initial_payment?>
				<?php } ?>
			</td>
			<td>
				<?php if(!pmpro_isLevelRecurring($level)) { ?>
					--
				<?php } else { ?>						
					<?php echo $pmpro_currency_symbol?><?php echo $level->billing_amount?> every <?php echo $level->cycle_number.' '.sornot($level->cycle_period,$level->cycle_number)?>
					
					<?php if($level->billing_limit) { ?>(for <?php echo $level->billing_limit?> <?php echo sornot($level->cycle_period,$level->billing_limit)?>)<?php } ?>
					
				<?php } ?>
			</td>				
			<td>
				<?php if(!pmpro_isLevelTrial($level)) { ?>
					--
				<?php } else { ?>		
					<?php echo $pmpro_currency_symbol?><?php echo $level->trial_amount?> for <?php echo $level->trial_limit?> <?php echo sornot("payment",$level->trial_limit)?>
				<?php } ?>
			</td>
			<td>
				<?php if(!pmpro_isLevelExpiring($level)) { ?>
					--
				<?php } else { ?>		
					After <?php echo $level->expiration_number?> <?php echo sornot($level->expiration_period,$level->expiration_number)?>
				<?php } ?>
			</td>
			<td><?php if($level->allow_signups) { ?>Yes<?php } else { ?>No<?php } ?></td>
			<td align="center"><a href="admin.php?page=pmpro-membershiplevels&edit=<?php echo $level->id?>" class="edit">edit</a></td>
			<td align="center"><a href="admin.php?page=pmpro-membershiplevels&copy=<?php echo $level->id?>&edit=-1" class="edit">copy</a></td>
			<td align="center"><a href="javascript: askfirst('Are you sure you want to delete membership level <?php echo $level->name?>? All subscriptions will be canceled.','admin.php?page=pmpro-membershiplevels&action=delete_membership_level&deleteid=<?php echo $level->id?>'); void(0);" class="delete">delete</a></td>
		</tr>
		<?php
			}
		?>
	</tbody>
	</table>	
	<?php
	}
	?>		
	
<?php
	require_once(dirname(__FILE__) . "/admin_footer.php");	
?>

