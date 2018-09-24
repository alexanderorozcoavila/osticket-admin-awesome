        </div>
    </div>
    <div class="clear"></div>
    
<?php
	if ($cfg && $cfg->isKnowledgebaseEnabled()) { ?>
    <div id="pre-footer">	
		<div id="pre-footer-inner">
			<form method="get" action="<?php echo ROOT_PATH; ?>kb/faq.php" class="form-wrapper cf">
				<input type="hidden" name="a" value="search"/>
				<input type="text" name="q" class="search" placeholder="<?php echo __('Search our knowledge base'); ?>"/>
				<button type="submit"><?php echo __('Search'); ?></button>
			</form>
		<div class="clear"></div>
		</div>
    </div>    		
<?php
	}
		else
			echo  '';
		?>

	<div class="clear"></div>
    <div id="footer">
		<?php include ROOT_DIR . 'osta/inc/client-foot.html'; ?>   
    </div>
	<div id="overlay"></div>
	<div id="loading">
		<h4><?php echo __('Please Wait!');?></h4>
		<p><?php echo __('Please wait... it will take a second!');?></p>
	</div>
	<?php
	if (($lang = Internationalization::getCurrentLanguage()) && $lang != 'en_US') { ?>
		<script type="text/javascript" src="ajax.php/i18n/<?php
			echo $lang; ?>/js"></script>
	<?php } ?>
	<script type="text/javascript">
		getConfig().resolve(<?php
			include INCLUDE_DIR . 'ajax.config.php';
			$api = new ConfigAjaxAPI();
			print $api->client(false);
		?>);
	</script>
<?php include ROOT_DIR . 'osta/inc/back-button.html'; ?>  
</body>
</html>
