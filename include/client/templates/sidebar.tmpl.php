<?php
$BUTTONS = isset($BUTTONS) ? $BUTTONS : true;
?>
    <div class="sidebar pull-right">
<?php if ($BUTTONS) { ?>
        <div class="front-page-button flush-right">
        	<div id="ticket-open-view-wrapper">
				<?php
					if ($cfg->getClientRegistrationMode() != 'disabled'
						|| !$cfg->isClientLoginRequired()) { ?>    
				<a href="open.php"> 
				<div class="open-wrap-open" >
					<div class="open-inner-open">       
						<span><?php echo __('Open a New Ticket');?></span>
					</div>  	
				</div>
				</a>			
				<?php } ?>        
				<a href="view.php">         
				<div class="open-wrap-check">
					<div class="open-inner-check">

						<span><?php echo __('Check Ticket Status');?></span>

					</div>  
				</div>
			</div>
			</a>

        </div>
<?php } ?>
    </div>

