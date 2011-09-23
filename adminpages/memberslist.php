<?php
	//vars
	global $wpdb;
	$s = $_REQUEST['s'];
	$l = $_REQUEST['l'];
?>
<div class="wrap pmpro_admin">	
	<div class="pmpro_banner">		
		<a class="pmpro_logo" title="Paid Memberships Pro - Membership Plugin for WordPress" target="_blank" href="<?=pmpro_https_filter("http://www.paidmembershipspro.com")?>"><img src="<?=PMPRO_URL?>/images/PaidMembershipsPro.gif" width="350" height="45" border="0" alt="Paid Memberships Pro(c) - All Rights Reserved" /></a>
		<div class="pmpro_tagline">Membership Plugin for WordPress</div>
		
		<div class="pmpro_meta"><a href="<?=pmpro_https_filter("http://www.paidmembershipspro.com")?>">Plugin Support</a> | <a href="http://www.paidmembershipspro.com/forums/">User Forum</a> | <strong>Version <?=PMPRO_VERSION?></strong></div>
	</div>
	<br style="clear:both;" />
	
	<?php
		//include(pmpro_https_filter("http://www.paidmembershipspro.com/notifications/?v=" . PMPRO_VERSION));
	?>
	<div id="pmpro_notifications">
	</div>
	<script>
		jQuery.get('<?=pmpro_https_filter("http://www.paidmembershipspro.com/notifications/?v=" . PMPRO_VERSION)?>', function(data) {
		  jQuery('#pmpro_notifications').html(data);		 
		});
	</script>

	<form id="posts-filter" method="get" action="">	
	<h2>
		Members Report
		<small>(<a target="_blank" href="<?=PMPRO_URL?>/adminpages/memberslist-csv.php?s=<?=$s?>&l=<?=$l?>">Export to CSV</a>)</small>
	</h2>		
	<ul class="subsubsub">
		<li>			
			Show <select name="l" onchange="jQuery('#posts-filter').submit();">
				<option value="" <?php if(!$l) { ?>selected="selected"<?php } ?>>All Levels</option>
				<?php
					$levels = $wpdb->get_results("SELECT id, name FROM $wpdb->pmpro_membership_levels ORDER BY name");
					foreach($levels as $level)
					{
				?>
					<option value="<?=$level->id?>" <?php if($l == $level->id) { ?>selected="selected"<?php } ?>><?=$level->name?></option>
				<?php
					}
				?>
			</select>			
		</li>
	</ul>
	<p class="search-box">
		<label class="hidden" for="post-search-input">Search Members:</label>
		<input type="hidden" name="page" value="pmpro-memberslist" />		
		<input id="post-search-input" type="text" value="<?=$s?>" name="s"/>
		<input class="button" type="submit" value="Search Members"/>
	</p>
	<?php 
		//some vars for the search
		$pn = $_REQUEST['pn'];
			if(!$pn) $pn = 1;
		$limit = $_REQUEST['limit'];
			if(!$limit) $limit = 15;
		$end = $pn * $limit;
		$start = $end - $limit;				
					
		if($s)
		{
			$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id WHERE mu.membership_id > 0 AND (u.user_login LIKE '%$s%' OR u.user_email LIKE '%$s%' OR um.meta_value LIKE '%$s%') ";
		
			if($l)
				$sqlQuery .= " AND mu.membership_id = '" . $l . "' ";					
				
			$sqlQuery .= "GROUP BY u.ID ORDER BY user_registered DESC LIMIT $start, $limit";
		}
		else
		{
			$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS u.ID, u.user_login, u.user_email, UNIX_TIMESTAMP(u.user_registered) as joindate, mu.membership_id, mu.initial_payment, mu.billing_amount, mu.cycle_period, mu.cycle_number, mu.billing_limit, mu.trial_amount, mu.trial_limit, UNIX_TIMESTAMP(mu.startdate) as startdate, m.name as membership FROM $wpdb->users u LEFT JOIN $wpdb->pmpro_memberships_users mu ON u.ID = mu.user_id LEFT JOIN $wpdb->pmpro_membership_levels m ON mu.membership_id = m.id ";
			$sqlQuery .= "WHERE mu.membership_id > 0 ";
			if($l)
				$sqlQuery .= " AND mu.membership_id = '" . $l . "' ";										
			$sqlQuery .= "ORDER BY user_registered DESC LIMIT $start, $limit";
		}
						
		$theusers = $wpdb->get_results($sqlQuery);
		$totalrows = $wpdb->get_var("SELECT FOUND_ROWS() as found_rows");
		
		if($theusers)
		{
			$initial_payments = pmpro_calculateInitialPaymentRevenue($s, $l);
			$recurring_payments = pmpro_calculateRecurringRevenue($s, $l);
		?>
		<p class="clear">Members shown below have paid <strong>$<?=number_format($initial_payments)?> in initial payments</strong> and will generate an estimated <strong>$<?=number_format($recurring_payments)?> in revenue over the next year</strong>, or <strong>$<?=number_format($recurring_payments/12)?>/month</strong>. <span class="pmpro_lite">(This estimate does not take into account trial periods or billing limits.)</span></p>
		<?php
		}		
	?>
	<table class="widefat">
		<thead>
			<tr class="thead">
				<th>ID</th>
				<th>Username</th>
				<th>First&nbsp;Name</th>
				<th>Last&nbsp;Name</th>
				<th>Email</th>
				<th>Membership</th>	
				<th>Fee</th>
				<th>Joined</th>				
			</tr>
		</thead>
		<tbody id="users" class="list:user user-list">	
			<?php								
				foreach($theusers as $theuser)
				{
					//get meta
					$sqlQuery = "SELECT meta_key as `key`, meta_value as `value` FROM $wpdb->usermeta WHERE $wpdb->usermeta.user_id = '" . $theuser->ID . "'";					
					$metavalues = pmpro_getMetavalues($sqlQuery);																		
					?>
						<tr <?php if($count++ % 2 == 0) { ?>class="alternate"<?php } ?>>
							<td><?=$theuser->ID?></td>
							<td>
								<?=get_avatar($theuser->ID, 32)?>
								<strong><a href="user-edit.php?user_id=<?=$theuser->ID?>"><?=$theuser->user_login?></a></strong>
							</td>
							<td><?=$metavalues->first_name?></td>
							<td><?=$metavalues->last_name?></td>
							<td><a href="mailto:<?=$theuser->user_email?>"><?=$theuser->user_email?></a></td>
							<td><?=$theuser->membership?></td>	
							<td>
								<?php if($theuser->billing_amount > 0) { ?>
									$<?=$theuser->billing_amount?>/<?=$theuser->cycle_period?>
								<?php } else { ?>
									-
								<?php } ?>
							</td>						
							<td><?=date("m/d/Y", $theuser->joindate)?></td>
						</tr>
					<?php
				}
				
				if(!$theusers)
				{
				?>
				<tr>
					<td colspan="9"><p>No members found. <?php if($l) { ?><a href="?page=pmpro-memberslist&s=<?=$s?>">Search all levels</a>.<?php } ?></p></td>
				</tr>
				<?php
				}
			?>		
		</tbody>
	</table>
	</form>
	
	<?php
	echo pmpro_getPaginationString($pn, $totalrows, $limit, 1, "/wp-admin/admin.php?page=pmpro-memberslist&s=" . urlencode($s), "&l=$l&limit=$limit&pn=");
	?>
	
</div>
<?php
?>
