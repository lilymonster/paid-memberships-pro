<?php
	global $besecure;
	$besecure = true;	
	
	global $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_confirm; 
	
	//if they don't have a membership, send them back to the subscription page
	if(!$current_user->membership_level->ID)
	{
		wp_redirect(pmpro_url("levels"));
	}		
	
	$pmpro_confirm = $_REQUEST['confirm'];
	if($pmpro_confirm)
	{		
		$worked = pmpro_changeMembershipLevel(false, $current_user->ID);		
		if($worked === true)
		{			
			$pmpro_msg = "Your membership has been cancelled.";
			$pmpro_msgt = "pmpro_success";
			
			//send an email
			$myemail = new PMProEmail();
			$myemail->sendCancelEmail();
		}
		else
		{
			global $pmpro_error;
			$pmpro_msg = $pmpro_error;
			$pmpro_msgt = "pmpro_error";			
		}		
	}		
?>