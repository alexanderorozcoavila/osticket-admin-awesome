<?php
$title=($cfg && is_object($cfg) && $cfg->getTitle())
    ? $cfg->getTitle() : 'osTicket :: '.__('Support Ticket System');
$signin_url = ROOT_PATH . "login.php"
    . ($thisclient ? "?e=".urlencode($thisclient->getEmail()) : "");
$signout_url = ROOT_PATH . "logout.php?auth=".$ost->getLinkToken();

header("Content-Type: text/html; charset=UTF-8");
if (($lang = Internationalization::getCurrentLanguage())) {
    $langs = array_unique(array($lang, $cfg->getPrimaryLanguage()));
    $langs = Internationalization::rfc1766($langs);
    header("Content-Language: ".implode(', ', $langs));
}
?>
<!DOCTYPE html>
<html<?php
if ($lang
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . $lang . '"';
}
?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo Format::htmlchars($title); ?></title>
    <meta name="description" content="customer support platform">
    <meta name="keywords" content="osTicket, Customer support system, support ticket system">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/osticket.css?9ae093d" media="screen"/>
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/theme.css?9ae093d" media="screen"/>
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>css/print.css?9ae093d" media="print"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>scp/css/typeahead.css?9ae093d"
         media="screen" />
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?9ae093d"
        rel="stylesheet" media="screen" />
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/thread.css?9ae093d" media="screen"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?9ae093d" media="screen"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css?9ae093d"/>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-1.11.2.min.js?9ae093d"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-ui-1.10.3.custom.min.js?9ae093d"></script>
    <script src="<?php echo ROOT_PATH; ?>js/osticket.js?9ae093d"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/filedrop.field.js?9ae093d"></script>
    <script src="<?php echo ROOT_PATH; ?>scp/js/bootstrap-typeahead.js?9ae093d"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor.min.js?9ae093d"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-plugins.js?9ae093d"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/redactor-osticket.js?9ae093d"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/select2.min.js?9ae093d"></script>
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/fabric.min.js?9ae093d"></script>
    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }

    // Offer alternate links for search engines
    // @see https://support.google.com/webmasters/answer/189077?hl=en
    if (($all_langs = Internationalization::getConfiguredSystemLanguages())
        && (count($all_langs) > 1)
    ) {
        $langs = Internationalization::rfc1766(array_keys($all_langs));
        $qs = array();
        parse_str($_SERVER['QUERY_STRING'], $qs);
        foreach ($langs as $L) {
            $qs['lang'] = $L; ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>?<?php
            echo http_build_query($qs); ?>" hreflang="<?php echo $L; ?>" />
<?php
        } ?>
        <link rel="alternate" href="//<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
            hreflang="x-default" />
<?php
    }
    ?>
<?php include ROOT_DIR . 'osta/inc/client-head.html'; ?>    
</head>
<body class="<?php echo basename($_SERVER['PHP_SELF'], '.php');  ?>-page">
    <div id="container">
        <div id="header">
        	<div id="header-inner">

				<div class="pull-right flush-right">
				<p>
				 <?php
					if ($thisclient && is_object($thisclient) && $thisclient->isValid()
						
												
						&& !$thisclient->isGuest()) { ?>
						
					 <?php echo Format::htmlchars($thisclient->getName()).'&nbsp;';
					 ?>
					<a href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a> 
					<a href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets <b>(%d)</b>'), $thisclient->getNumTickets()); ?></a>
					<a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a>
				<?php
				} elseif($nav) {
					if ($cfg->getClientRegistrationMode() == 'public') { ?>
						<?php echo __('Sign In').','; ?>
						<?php echo __('Guest User'); ?> <?php
					}
					if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
						<a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a><?php
					}
					elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
						<a href="<?php echo $signin_url; ?>"><?php echo __('Sign In'); ?></a>
	<?php
					}
				} ?>
				</p>

				</div>
       
       
				<div id="left-logo">

					<div class="header-title">
						<a id="header-logo-title" href="<?php echo ROOT_PATH; ?>">
							<?php				
							$file_name = ROOT_DIR ."osta/css/themes/title.txt";
							echo file_get_contents($file_name);
							?>  
						</a>
					</div>

					<div class="header-subtitle">
						<a id="header-logo-subtitle" href="<?php echo ROOT_PATH; ?>">
							<?php				
							$file_name = ROOT_DIR ."osta/css/themes/subtitle.txt";
							echo file_get_contents($file_name);
							?>      
						</a>
					</div>
				</div>			
				
				
				<div id="right-menu" href="#right-menu">
					<button href="#right-menu" class="c-hamburger c-hamburger--htx" style="">
						<span>toggle menu</span>
					</button>
					<script>
					$(document).ready(function() {

					  "use strict";

						var toggles = document.querySelectorAll(".c-hamburger");

						for (var i = toggles.length - 1; i >= 0; i--) {
						  var toggle = toggles[i];
						  toggleHandler(toggle);
						};

						function toggleHandler(toggle) {
						  toggle.addEventListener( "click", function(e) {
							e.preventDefault();
							(this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
						  });
						  toggle.addEventListener( "touchstart", function(e) {
							e.preventDefault();
							(this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
						  });	  
						}

					   $('.c-hamburger').sidr({
							name: 'sidr-right',
							side: 'right',
							body: '#content',
							displace: false
						});	
					})();
					</script>
				</div>
			</div>		
			<div id="sidr-right" class="sidr right">
				<div class="sidr-inner">

					<ul id="nav-mobile" class="flush-left">
						<li><a href="<?php echo ROOT_PATH; ?>"><?php echo __('Support Center Home'); ?></a></li>
				 <?php
						if($cfg && $cfg->isKnowledgebaseEnabled())  { ?>
						<li><a class="active kb" href="<?php echo ROOT_PATH; ?>kb/index.php"><?php echo __('Knowledgebase') ?></a></li>
				 <?php } ?>
						<li><a href="<?php echo ROOT_PATH; ?>open.php"><?php echo __('Open a New Ticket'); ?></a></li>
						<li><a href="<?php echo ROOT_PATH; ?>view.php"><?php echo __('Check Ticket Status'); ?></a></li>	
				 <?php
						if ($thisclient && is_object($thisclient) && $thisclient->isValid()
							&& !$thisclient->isGuest()) {
						echo '<div id="welcome"><svg style="width:18px;height:18px" viewBox="0 0 24 24"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" /></svg>&nbsp;'.Format::htmlchars($thisclient->getName()).'</div>';
						 ?>
						<li><a href="<?php echo ROOT_PATH; ?>profile.php"><?php echo __('Profile'); ?></a></li>
						<li><a href="<?php echo ROOT_PATH; ?>tickets.php"><?php echo sprintf(__('Tickets (%d)'), $thisclient->getNumTickets()); ?></a></li>
						<li><a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a></li>
				<?php
				} elseif($nav) {
					if ($cfg->getClientRegistrationMode() == 'public') { ?>
						<div id="welcome"><svg style="width:18px;height:18px" viewBox="0 0 24 24"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" /></svg>&nbsp;<?php echo __('Guest User'); ?></div>  <?php
					}
					if ($thisclient && $thisclient->isValid() && $thisclient->isGuest()) { ?>
						<li><a href="<?php echo $signout_url; ?>"><?php echo __('Sign Out'); ?></a></li><?php
					}
					elseif ($cfg->getClientRegistrationMode() != 'disabled') { ?>
						<li><a href="<?php echo $signin_url; ?>"><?php echo __('Sign In'); ?></a></li>
						
						
				<div id="flags-mobile">
					<?php
					if (($all_langs = Internationalization::getConfiguredSystemLanguages())
						&& (count($all_langs) > 1)
					) {
						$qs = array();
						parse_str($_SERVER['QUERY_STRING'], $qs);
						foreach ($all_langs as $code=>$info) {
							list($lang, $locale) = explode('_', $code);
							$qs['lang'] = $code;
					?>
							<a class="flag flag-<?php echo strtolower($locale ?: $info['flag'] ?: $lang); ?>"
								href="?<?php echo http_build_query($qs);
								?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
					<?php }
					} ?>
				</div> 						
							
						
				<?php
					}
				} ?>
						<li id="contact-id">
							<a href="
								<?php				
								$file_name = ROOT_DIR ."osta/css/themes/mobile-link.txt";
									echo file_get_contents($file_name);
								?> 
							">
								<?php				
								$file_name = ROOT_DIR ."osta/css/themes/mobile-text.txt";
									echo file_get_contents($file_name);
								?>  
							</a>
						</li>
					</ul>

				</div>
			</div>				
				

			</div>    
        </div>
        <div class="clear"></div>
        <?php
        if($nav){ ?>
        <div id="nav-wrapper">
			<div id="nav-inner">

				<ul id="nav" class="flush-left">
					<?php
					if($nav && ($navs=$nav->getNavLinks()) && is_array($navs)){
						foreach($navs as $name =>$nav) {
							echo sprintf('<li><a class="%s %s" href="%s">%s</a></li>%s',$nav['active']?'active':'',$name,(ROOT_PATH.$nav['href']),$nav['desc'],"\n");
						}
					} ?>
				</ul>
				<div id="languages">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="24px"
	 height="24px" viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
<g id="Icons">
	<g id="language-big">
		<path id="japanese" d="M18.498,9.4l0.334-0.722l1.277,0.497L19.887,9.62c0.813,0.335,1.352,0.576,1.609,0.724
			c0.41,0.258,0.76,0.63,1.055,1.112c0.261,0.479,0.392,1.036,0.392,1.665c0,0.964-0.372,1.813-1.113,2.557
			c-0.738,0.775-2.036,1.295-3.887,1.554c-0.148-0.443-0.315-0.833-0.499-1.166c1.183-0.186,2.019-0.407,2.501-0.665
			c0.554-0.261,0.96-0.593,1.22-1.002c0.258-0.37,0.39-0.814,0.39-1.332c0-0.592-0.166-1.112-0.502-1.557
			c-0.406-0.407-0.981-0.721-1.721-0.943c-0.407,0.776-0.798,1.369-1.169,1.777c-0.294,0.407-0.849,1.109-1.662,2.11
			c0.111,0.48,0.22,0.87,0.33,1.166l-1.275,0.445l-0.113-0.611c-0.518,0.443-0.981,0.758-1.388,0.944
			c-0.444,0.184-0.814,0.277-1.11,0.277c-0.37,0-0.704-0.165-1.002-0.499c-0.295-0.37-0.443-0.835-0.443-1.39
			c0-0.74,0.165-1.405,0.501-2c0.295-0.554,0.74-1.092,1.334-1.609c0.332-0.297,0.887-0.648,1.666-1.057
			c0-0.333,0.035-0.979,0.112-1.945c-0.633,0.039-1.132,0.058-1.502,0.058c-0.48,0-0.87-0.019-1.167-0.058l-0.056-1.274
			c0.888,0.107,1.833,0.165,2.833,0.165c0-0.185,0.091-0.906,0.278-2.166l1.443,0.221c-0.184,0.668-0.313,1.279-0.391,1.834
			c0.298-0.036,0.669-0.092,1.113-0.166c0.446-0.073,0.702-0.112,0.777-0.112s0.797-0.185,2.166-0.555l0.057,1.277
			c-1.186,0.298-2.629,0.537-4.332,0.723c-0.074,0.813-0.111,1.333-0.111,1.557C17.034,9.491,17.794,9.4,18.498,9.4z M15.22,14.177
			c-0.074-0.593-0.167-1.536-0.277-2.834c-0.703,0.519-1.277,1.057-1.721,1.612c-0.372,0.518-0.556,1.073-0.556,1.664
			c0,0.298,0.053,0.539,0.166,0.723c0.111,0.112,0.239,0.167,0.39,0.167C13.665,15.509,14.332,15.064,15.22,14.177z M16.164,10.789
			c0,0.591,0.036,1.332,0.113,2.22c0.74-1.109,1.295-1.96,1.665-2.554C17.238,10.529,16.646,10.642,16.164,10.789z"/>
		<path id="english" style="fill-rule:evenodd;clip-rule:evenodd;" d="M8.642,16.931h2.269L7.128,6.083h-2.27l-3.78,10.848h2.269
			l0.685-2.169h3.938L8.642,16.931z M4.359,13l1.635-4.75L7.643,13H4.359z"/>
	</g>
</g>
<g id="Guides" style="display:none;">
</g>
</svg>
<script>
$('#languages').click(function(){
  $('.toggle').toggleClass('down up');
});		
</script>
				</div>
			</div>
		</div>
        <?php
        }else{ ?>
         <hr>
        <?php
        } ?>
        <div id="content" class="toggle up">
         
				<div id="flags">
					<?php
					if (($all_langs = Internationalization::getConfiguredSystemLanguages())
						&& (count($all_langs) > 1)
					) {
						$qs = array();
						parse_str($_SERVER['QUERY_STRING'], $qs);
						foreach ($all_langs as $code=>$info) {
							list($lang, $locale) = explode('_', $code);
							$qs['lang'] = $code;
					?>
							<a class="flag flag-<?php echo strtolower($locale ?: $info['flag'] ?: $lang); ?>"
								href="?<?php echo http_build_query($qs);
								?>" title="<?php echo Internationalization::getLanguageDescription($code); ?>">&nbsp;</a>
					<?php }
					} ?>
				</div>  

         <?php if($errors['err']) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
         <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
         <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
         <?php } ?>
