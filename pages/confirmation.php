<?php 
	global $current_user, $pmpro_invoice, $pmpro_msg, $pmpro_msgt, $pmpro_currency_symbol;
	
	if($pmpro_msg)
	{
	?>
		<div class="pmpro_message <?php echo $pmpro_msgt?>"><?php echo $pmpro_msg?></div>
	<?php
	}
?>	

<p>Thank you for your membership to <?php bloginfo('name'); ?>. Your <?php echo $current_user->membership_level->name?> membership is now active.</p>

<?php if($pmpro_invoice) { ?>		
	<?php
		$pmpro_invoice->getUser();
		$pmpro_invoice->getMembershipLevel();			
	?>
	<p>Below are details about your membership account and a receipt for your initial membership invoice. A welcome email with a copy of your initial membership invoice has been sent to <strong><?php echo $pmpro_invoice->user->user_email?></strong>.</p>
	
	<h3>Invoice #<?php echo $pmpro_invoice->code?> on <?php echo date("F j, Y", $pmpro_invoice->timestamp)?></h3>
	<a class="pmpro_a-print" href="javascript:window.print()">Print</a>
	<ul>
		<li><strong>Account:</strong> <?php echo $pmpro_invoice->user->display_name?> (<?php echo $pmpro_invoice->user->user_email?>)</li>
		<li><strong>Membership Level:</strong> <?php echo $current_user->membership_level->name?></li>
		<?php if($current_user->membership_level->enddate) { ?>
			<li><strong>Membership Expires:</strong> <?php echo date("n/j/Y", $current_user->membership_level->enddate)?></li>
		<?php } ?>
		<?php if($pmpro_invoice->getDiscountCode()) { ?>
			<li><strong>Discount Code:</strong> <?php echo $pmpro_invoice->discount_code->code?></li>
		<?php } ?>
	</ul>
	
	<table id="pmpro_confirmation_table" class="pmpro_invoice" width="100%" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<th>Billing Address</th>
				<th>Payment Method</th>
				<th>Membership Level</th>
				<th>Total Billed</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php echo $pmpro_invoice->billing->name?><br />
					<?php echo $pmpro_invoice->billing->street?><br />						
					<?php if($pmpro_invoice->billing->city && $pmpro_invoice->billing->state) { ?>
						<?php echo $pmpro_invoice->billing->city?>, <?php echo $pmpro_invoice->billing->state?> <?php echo $pmpro_invoice->billing->zip?><br />												
					<?php } ?>
					<?php echo formatPhone($pmpro_invoice->billing->phone)?>
				</td>
				<td>
					<?php if($pmpro_invoice->accountnumber) { ?>
						<?php echo $pmpro_invoice->cardtype?> ending in <?php echo last4($pmpro_invoice->accountnumber)?><br />
						<small>Expiration: <?php echo $pmpro_invoice->expirationmonth?>/<?php echo $pmpro_invoice->expirationyear?></small>
					<?php } elseif($pmpro_invoice->payment_type) { ?>
						<?php echo $pmpro_invoice->payment_type?>
					<?php } ?>
				</td>
				<td><?php echo $pmpro_invoice->membership_level->name?></td>					
				<td><?php if($pmpro_invoice->total) echo $pmpro_currency_symbol . number_format($pmpro_invoice->total, 2); else echo "---";?></td>
			</tr>
		</tbody>
	</table>		
<?php } else { ?>
	<p>Below are details about your membership account. A welcome email has been sent to <strong><?php echo $current_user->user_email?></strong>.</p>
	<ul>
		<li><strong>Account:</strong> <?php echo $current_user->display_name?> (<?php echo $current_user->user_email?>)</li>
		<li><strong>Membership Level:</strong> <?php echo $current_user->membership_level->name?></li>
	</ul>	
<?php } ?>  

<p align="center"><a href="<?php echo pmpro_url("account")?>">View Your Membership Account &raquo;</a></p>           