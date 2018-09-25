<?php
include_once(INCLUDE_DIR.'staff/login.header.php');
defined('OSTSCPINC') or die('Invalid path');
$info = ($_POST)?Format::htmlchars($_POST):array();
?>

<div id="brickwall"></div>
<div id="loginBox">
    <div id="blur">
        <div id="background"></div>
    </div>
    <div id="login-title">
		<a id="login-title-link" href="<?php echo ROOT_PATH; ?>scp/">
			<?php				
			$file_name = ROOT_DIR ."osta/css/themes/title.txt";
			echo file_get_contents($file_name);
			?>  
		</a>
	</div>
    <h3><?php echo Format::htmlchars($msg); ?></h3>

    <form action="pwreset.php" method="post">
        <?php csrf_token(); ?>
        <input type="hidden" name="do" value="newpasswd"/>
        <input type="hidden" name="token" value="<?php echo Format::htmlchars($_REQUEST['token']); ?>"/>
        <fieldset>
            <input type="text" name="userid" id="name" value="<?php echo
                $info['userid']; ?>" placeholder="<?php echo __('Email or Username'); ?>"
                autocorrect="off" autocapitalize="off"/>
        </fieldset>
        <input class="submit" type="submit" name="submit" value="Login"/>
    </form>

    <div id="company">
        <div class="content">
            <?php echo __('Copyright'); ?> &copy; <?php echo Format::htmlchars($ost->company) ?: date('Y'); ?>
        </div>
    </div>
</div>
<div id="poweredBy"><?php echo __('Powered by'); ?>
    <a href="http://www.osticket.com" target="_blank">
        <img alt="osTicket" src="images/osticket-grey.png" class="osticket-logo">
    </a>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (undefined === window.getComputedStyle(document.documentElement).backgroundBlendMode) {
            document.getElementById('loginBox').style.backgroundColor = 'white';
        }
    });
    </script>
</body>
</html>