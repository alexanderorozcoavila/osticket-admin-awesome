<?php
header("Content-Type: text/html; charset=UTF-8");

$title = ($ost && ($title=$ost->getPageTitle()))
    ? $title : ('osTicket :: '.__('Staff Control Panel'));

if (!isset($_SERVER['HTTP_X_PJAX'])) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html<?php
if (($lang = Internationalization::getCurrentLanguage())
        && ($info = Internationalization::getLanguageInfo($lang))
        && (@$info['direction'] == 'rtl'))
    echo ' dir="rtl" class="rtl"';
if ($lang) {
    echo ' lang="' . Internationalization::rfc1766($lang) . '"';
}
?>>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="x-pjax-version" content="<?php echo GIT_VERSION; ?>">
    <title><?php echo Format::htmlchars($title); ?></title>
    <!--[if IE]>
    <style type="text/css">
        .tip_shadow { display:block !important; }
    </style>
    <![endif]-->
    <script type="text/javascript" src="<?php echo ROOT_PATH; ?>js/jquery-1.11.2.min.js?9ae093d"></script>
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>css/thread.css?9ae093d" media="all"/>
    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/scp.css?9ae093d" media="all"/>
    
    
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/redactor.css?9ae093d" media="screen"/>

    <link rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/typeahead.css?9ae093d" media="screen"/>
    <link type="text/css" href="<?php echo ROOT_PATH; ?>css/ui-lightness/jquery-ui-1.10.3.custom.min.css?9ae093d"
         rel="stylesheet" media="screen" />
     <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome.min.css?9ae093d"/>
    <!--[if IE 7]>
    <link rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/font-awesome-ie7.min.css?9ae093d"/>
    <![endif]-->
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/dropdown.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/loadingbar.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/flags.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/select2.min.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH; ?>css/rtl.css?9ae093d"/>
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/translatable.css?9ae093d"/>

    <!-- Modificaciones de CC and CCO-->   
    <script type="text/javascript" src="<?php echo ROOT_PATH ?>scp/selectize.js/dist/js/standalone/selectize.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo ROOT_PATH ?>scp/selectize.js/dist/css/selectize.css" />
    
    <!--
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/redactor.css"/>-->
    <link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/jquery.spellchecker.css"/>
    <!--<link type="text/css" rel="stylesheet" href="<?php echo ROOT_PATH ?>scp/css/bootstrap.min.css"/>-->



    


    <?php
    if($ost && ($headers=$ost->getExtraHeaders())) {
        echo "\n\t".implode("\n\t", $headers)."\n";
    }
    
    ?>
	<?php include ROOT_DIR . '/osta/inc/staff-head.html'; ?>	
</head>
<body class="<?php echo basename($_SERVER['PHP_SELF'], '.php');  ?>-page">
<div id="container">
    <?php if($ost->getError()) echo sprintf('
    <div id="error_bar">%s</div>', $ost->getError()); elseif($ost->getWarning()) echo sprintf('
    <div id="warning_bar">%s</div>', $ost->getWarning()); elseif($ost->getNotice()) echo sprintf('
    <div id="notice_bar">%s</div>', $ost->getNotice()); ?>
    
    
    
    <div id="header">
        <div id="nav" class="pull-right pjax">
            <!--<?php echo sprintf(__('Welcome, %s.'), '<strong>'.$thisstaff->getFirstName().'</strong>'); ?>-->

            <?php include STAFFINC_DIR . "templates/navigation.tmpl.php"; ?>

            <?php if($thisstaff->isAdmin() && !defined('ADMINPAGE')) { ?> |
            <a href="<?php echo ROOT_PATH ?>scp/admin.php" class="no-pjax">
                <?php echo __( 'Admin Panel'); ?>
            </a>
            <?php }else{ ?> |
            <a href="<?php echo ROOT_PATH ?>scp/index.php" class="no-pjax">
                <?php echo __( 'Agent Panel'); ?>
            </a>
            <?php } ?> |
            <a href="<?php echo ROOT_PATH ?>scp/profile.php">
                <?php echo $thisstaff->getFirstName(); ?></a>
            &nbsp;
            <span data-placement="bottom" data-toggle="tooltip" title="" data-original-title="logout">            
            <a href="<?php echo ROOT_PATH ?>scp/logout.php?auth=<?php echo $ost->getLinkToken(); ?>" class="no-pjax">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
				<polygon points="15.5,7.5 14.6,8.4 17.6,11.4 7.4,11.4 7.4,12.6 17.6,12.6 14.6,15.6 15.5,16.5 20,12 "></polygon>
				<path d="M20.6,15.2l-1.3,1.3v2.9H4.6V4.6h14.8v2.9l1.3,1.3V5c0-0.9-0.7-1.6-1.6-1.6H5C4.1,3.4,3.4,4.1,3.4,5v14 c0,0.9,0.7,1.6,1.6,1.6h14c0.9,0,1.6-0.7,1.6-1.6V15.2z"></path>
				</svg>
       		</a>
       	    </span>
        </div>


        <div id="left-logo" style="width: 327px;">

            <div class="header-title" style="float: left;margin-top: -6px;">
                <a id="header-logo-title" href="<?php echo ROOT_PATH; ?>scp/">
					<?php				
					// $file_name = ROOT_DIR ."osta/css/themes/title.txt";
					// echo file_get_contents($file_name);
                    ?>  
                    <img src="images/logo.png">
                </a>
            </div>

            <div id="texto-header" class="header" style="float: left;margin-top: 4px;
    border-left: 1px solid;
    height: 25px;padding-top: 2px;
    padding-left: 7px;">
                <a id="header-logo-subtitle" href="<?php echo ROOT_PATH; ?>scp/">
					<?php				
					// $file_name = ROOT_DIR ."osta/css/themes/subtitle.txt";
					// echo file_get_contents($file_name);
                    ?>  
                    CENTRO DE SOPORTE    
                </a>
            </div>
        </div>
            <div id="right-buttons">
                <a class="mobile-nav" href="<?php echo ROOT_PATH; ?>scp/tickets.php?status=open">
                    <svg style="width:24px;height:24px; padding: 18px;float:right;margin-right:1px;" viewBox="0 0 24 24">
                        <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"></path>
                    </svg>
                </a>
                <a class="mobile-nav" href="<?php echo ROOT_PATH; ?>scp/users.php">
                    <svg style="width:24px;height:24px; padding: 18px;float:right;margin-right:1px;" viewBox="0 0 24 24">
                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"></path>
                    </svg>
                </a>
                <a class="mobile-nav" href="<?php echo ROOT_PATH; ?>scp/tickets.php?a=open">
                    <svg style="width: 30px; height: 30px; padding: 15px 20px 15px 12px; float: right;" viewBox="0 0 24 24">
                        <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"></path>
                    </svg>
                </a>
            </div>
    	</div>
            <div id="right-menu" href="#right-menu">
                <button href="#right-menu" class="c-hamburger c-hamburger--htx" style="">
                    <span>toggle menu</span>
                </button>
                <script>
                    /* osTA */
                    // $(document).ready(function() { /* osTA */
                    //     "use strict"; /* osTA */
                    //     var toggles = document.querySelectorAll(".c-hamburger"); /* osTA */
                    //     for (var i = toggles.length - 1; i >= 0; i--) { /* osTA */
                    //         var toggle = toggles[i]; /* osTA */
                    //         toggleHandler(toggle); /* osTA */
                    //     }; /* osTA */
                    //     function toggleHandler(toggle) { /* osTA */
                    //         toggle.addEventListener("click", function(e) { /* osTA */
                    //             e.preventDefault(); /* osTA */
                    //             (this.classList.contains("is-active") === true) ? this.classList.remove("is-active"): this.classList.add("is-active"); /* osTA */
                    //         }); /* osTA */
                    //         toggle.addEventListener("touchstart", function(e) { /* osTA */
                    //             e.preventDefault(); /* osTA */
                    //             (this.classList.contains("is-active") === true) ? this.classList.remove("is-active"): this.classList.add("is-active"); /* osTA */
                    //         }); /* osTA */
                    //     } /* osTA */
                    //     $('.c-hamburger').sidr({ /* osTA */
                    //         name: 'sidr-right',
                    //         /* osTA */
                    //         side: 'right',
                    //         /* osTA */
                    //         body: '#content',
                    //         /* osTA */
                    //         displace: false /* osTA */
                    //     }); /* osTA */
                    // })(); /* osTA */
                </script>
            </div>

        <div id="sidr-right" class="sidr right" style="transition: right 0.2s ease 0s;">
            <?php include ROOT_DIR . 'osta/inc/staff-mobile-menu.html'; ?>
        </div>    	
    <!-- END Header -->
    


    <div id="pjax-container" class="<?php if ($_POST) echo 'no-pjax'; ?>">
<?php } else {
    header('X-PJAX-Version: ' . GIT_VERSION);
    if ($pjax = $ost->getExtraPjax()) { ?>
    <script type="text/javascript">
    <?php foreach (array_filter($pjax) as $s) echo $s.";"; ?>
    </script>
    <?php }
    foreach ($ost->getExtraHeaders() as $h) {
        if (strpos($h, '<script ') !== false)
            echo $h;
    } ?>
    <title><?php echo ($ost && ($title=$ost->getPageTitle()))?$title:'osTicket :: '.__('Staff Control Panel'); ?></title><?php
} # endif X_PJAX ?>
   
   
<!--    <ul id="nav">
<?php include STAFFINC_DIR . "templates/navigation.tmpl.php"; ?>
    </ul>-->
   
    
      <ul id="sub_nav">
<?php include STAFFINC_DIR . "templates/sub-navigation.tmpl.php"; ?>
    </ul>
    <div id="content" class="<?php echo basename($_SERVER['PHP_SELF'], '.php');  ?>">
        <?php if($errors['err']) { ?>
            <div id="msg_error"><?php echo $errors['err']; ?></div>
        <?php }elseif($msg) { ?>
            <div id="msg_notice"><?php echo $msg; ?></div>
        <?php }elseif($warn) { ?>
            <div id="msg_warning"><?php echo $warn; ?></div>
        <?php }
        foreach (Messages::getMessages() as $M) { ?>
            <div class="<?php echo strtolower($M->getLevel()); ?>-banner"><?php
                echo (string) $M; ?></div>
<?php   } ?>
