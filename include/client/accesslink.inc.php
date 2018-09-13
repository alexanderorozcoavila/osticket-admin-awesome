<?php
if(!defined('OSTCLIENTINC')) die('Access Denied');

$email=Format::input($_POST['lemail']?$_POST['lemail']:$_GET['e']);
$ticketid=Format::input($_POST['lticket']?$_POST['lticket']:$_GET['t']);

if ($cfg->isClientEmailVerificationRequired())
    $button = __("Email Access Link");
else
    $button = __("View Ticket");
?>
<h1><?php echo __('Check Ticket Status'); ?></h1>
<div class="subtitle"><?php
echo __('Please provide your email address and a ticket number.');
if ($cfg->isClientEmailVerificationRequired())
    echo ' '.__('An access link will be emailed to you.');
else
    echo ' '.__('This will sign you in to view your ticket.');
?></div>
<form action="login.php" method="post" id="clientLogin">
    <?php csrf_token(); ?>
    
<div class="responsive-div-view-page">
    <div id="one-view-page">
        <div id="login-wrapper">
            <div id="login">
				<label for="email"><?php echo __('Email Address'); ?>:
				<input id="email" placeholder="<?php echo __('e.g. john.doe@osticket.com'); ?>" type="text"
					name="lemail" size="30" value="<?php echo $email; ?>" class="nowarn"></label>
				<label for="ticketno"><?php echo __('Ticket Number'); ?>:
				<input id="ticketno" type="text" name="lticket" placeholder="<?php echo __('e.g. 051243'); ?>"
					size="30" value="<?php echo $ticketid; ?>" class="nowarn"></label>
           		<input class="btn" type="submit" value="<?php echo $button; ?>">
            </div>
        </div>
    </div>
    <div id="middle-view-page">
        <div id="or-wrapper">
            <div id="or">OR</div>
        </div>
    </div>
    <div id="two-view-page">
        <div id="options-wrapper">
        	<div id="options">
				<?php if ($cfg && $cfg->getClientRegistrationMode() !== 'disabled') { ?>
						<strong><?php echo __('Have an account with us?'); ?></strong><br /><br />
						<a href="login.php"><?php echo __('Sign In'); ?></a> <?php
					if ($cfg->isClientRegistrationEnabled()) { ?>
				<?php echo sprintf(__('or %s register for an account %s to access all your tickets.'),
					'<a href="account.php?do=create">','</a>');
					}
				}?>
				<br /><br />
				<?php
				if ($cfg->getClientRegistrationMode() != 'disabled'
					|| !$cfg->isClientLoginRequired()) {
					echo sprintf(
					__("If this is your first time contacting us or you've lost the ticket number, please %s open a new ticket %s"),
						'<a href="open.php">','</a>');
				} ?>
            </div>      
        </div>
    </div>
</div>   
</form>
    
    

