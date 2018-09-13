<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die('Access Denied');
?>  

<table id="color-themes" width="100%" border="0" cellspacing="0" cellpadding=  
   <tr>
      <td colspan="2">
         <h2>Pick a Color Theme</h2>
      </td>
   </tr>
   <tr>
      <td>
         <form action="../osta/css/themes/choose-theme.php" method="post">
            <?php csrf_token(); ?>          
            <script type="text/javascript">
               $(document).ready(function() {
               	$('#styleOptions').styleSwitcher();
               });
            </script>
            <ul id="styleOptions" title="switch styling" style="list-style: none; margin: 0; padding: 0;">
               <div class="theme-card">
                  <div id="theme-ice" class="theme-card-lid"></div>
                  <div class="theme-card-bottom">
                     <div class="theme-preview">
                        <li>
                           <a href="javascript: void(0)" data-theme="ice" class="ice">PREVIEW</a>
                        </li>
                     </div>
                     <div class="switch"><label class="checkcontainer"><input type="radio" name="theme" value="ice"> SWITCH<span class="radiobtn"></span></label></div>
                  </div>
               </div>
             <ul id="styleOptions" title="switch styling" style="list-style: none; margin: 11px 0 0 0; padding: 0;">
               <div class="theme-card">
                  <div id="theme-soft" class="theme-card-lid"></div>
                  <div class="theme-card-bottom">
                     <div class="theme-preview">
                        <li>
                           <a href="javascript: void(0)" data-theme="soft" class="soft">PREVIEW</a>
                        </li>
                     </div>
                     <div class="switch"><label class="checkcontainer"><input type="radio" name="theme" value="pink"> SWITCH<span class="radiobtn"></span></label></div>
                  </div>
               </div>              
              <ul id="styleOptions" title="switch styling" style="list-style: none; margin: 11px 0 0 0; padding: 0;">
               <div class="theme-card">
                  <div id="theme-pink" class="theme-card-lid"></div>
                  <div class="theme-card-bottom">
                     <div class="theme-preview">
                        <li>
                           <a href="javascript: void(0)" data-theme="pink" class="pink">PREVIEW</a>
                        </li>
                     </div>
                     <div class="switch"><label class="checkcontainer"><input type="radio" name="theme" value="pink"> SWITCH<span class="radiobtn"></span></label></div>
                  </div>
               </div>  
               <ul id="styleOptions" title="switch styling" style="list-style: none; margin: 11px 0 0 0; padding: 0;">
               <div class="theme-card">
                  <div id="theme-mint" class="theme-card-lid"></div>
                  <div class="theme-card-bottom">
                     <div class="theme-preview">
                        <li>
                           <a href="javascript: void(0)" data-theme="mint" class="mint">PREVIEW</a>
                        </li>
                     </div>
                     <div class="switch"><label class="checkcontainer"><input type="radio" name="theme" value="mint"> SWITCH<span class="radiobtn"></span></label></div>
                  </div>
               </div>                           
               <ul id="styleOptions" title="switch styling" style="list-style: none; margin: 11px 0 0 0; padding: 0;">
               <div class="theme-card">
                  <div id="theme-earth" class="theme-card-lid"></div>
                  <div class="theme-card-bottom">
                     <div class="theme-preview">
                        <li>
                           <a href="javascript: void(0)" data-theme="earth" class="earth">PREVIEW</a>
                        </li>
                     </div>
                     <div class="switch"><label class="checkcontainer"><input type="radio" name="theme" value="earth"> SWITCH<span class="radiobtn"></span></label></div>
                  </div>
               </div>   
              <ul id="styleOptions" title="switch styling" style="list-style: none; margin: 11px 0 0 0; padding: 0;">
               <div class="theme-card">
                  <div id="theme-black" class="theme-card-lid"></div>
                  <div class="theme-card-bottom">
                     <div class="theme-preview">
                        <li>
                           <a href="javascript: void(0)" data-theme="black" class="black">PREVIEW</a>
                        </li>
                     </div>
                     <div class="switch"><label class="checkcontainer"><input type="radio" name="theme" value="black"> SWITCH<span class="radiobtn"></span></label></div>
                  </div>
               </div>                                 
            </ul>
		</td>
	</tr>
	<tr>
		<td id="use-custom-theme">
			<div class="use-custom-theme">
				<label class="checkcontainer"> &nbsp;Use Custom Theme
				<input type="radio" name="theme" value="custom">
				<span class="radiobtn"></span>
				</label>			
			</div>			
		</td>		
	</tr>
	<tr>
		<td id="theme-save">
			<button id="theme-save-button" type="submit" value="Save Theme">Save Theme Selection</button><br /> 
			</form>
		</td>
	</tr>		
</tbody>
</table>
<table id="custom-theme" width="100%" border="0" cellspacing="0" cellpadding="0">
<tbody>      
	<tr>
		<td><h2>Create a Custom Theme</h2></td>
	</tr>
	<tr>
		<td id="color-pickers"><br />
			<style>
				i.colorpicker-guide {
					display: unset;
				}	
				.colorpicker-element .input-group-addon i {
					border: .5px solid #b6b6b6;
				}
				.colorpicker-saturation .colorpicker-guide {
    				border: unset !important;
				}
				.colorpicker-hue {
					width: 24px !important;
				}
				.input-group-addon {
					padding: 4px 6px 10px 6px;
					margin: 0px 0 0 -35px;
					font-size: unset;
					font-weight: 400;
					line-height: unset;
					/* background-color: #e9ecef; */
					/* border: .5px solid #ced4da; */
					border-radius: 0 3px 3px 0;
					position: relative;
					top: -3px;
				}
			</style> 	
			<link href="../osta/css/themes/bootstrap-colorpicker-master/dist/css/bootstrap-colorpicker.css" rel="stylesheet">	
			<script src='../osta/css/themes/bootstrap-colorpicker-master/src/js/bootstrap-colorpicker.js'></script>
			<script src="<?php echo ROOT_PATH; ?>osta/js/jquery.columnizer.js"></script>
			<script>
				$(function(){
					$('#custom-column-one').columnize({width: 260});

				});
			</script>			
			<form action="../osta/css/themes/custom/change-colors.php" method="post"> 
			<?php csrf_token(); ?>  

			<div id="custom-column-one">

				Header Background<br />             
				<div id="cp1" class="input-group colorpicker-component" title="Using input value">
					<input type="text" class="form-control input-lg" name="headerbg" value="#<?php $section = file_get_contents('../osta/css/themes/custom.css', NULL, NULL, 26, 6); echo ($section); ?>"> 
					<span class="input-group-addon"><i></i></span>
				</div>

				Header Text<br />						
				<div id="cp2" class="input-group colorpicker-component" title="Using input value">
					<input type="text" class="form-control input-lg" name="headertitlecolor" value="#<?php $section = file_get_contents('../osta/css/themes/custom.css', NULL, NULL, 61, 6); echo ($section); ?>"/>
					<span class="input-group-addon"><i></i></span>
				</div>	
								
				Navigation Bar Background<br />
				<div id="cp3" class="input-group colorpicker-component" title="Using input value">
					<input type="text" class="form-control input-lg" name="navbarbg" value="#<?php $section = file_get_contents('../osta/css/themes/custom.css', NULL, NULL, 88, 6); echo ($section); ?>"/>
					<span class="input-group-addon"><i></i></span>
				</div>

				Navigation Bar Link<br />
				<div id="cp4" class="input-group colorpicker-component" title="Using input value">
					<input type="text" class="form-control input-lg" name="navbarlink" value="#<?php $section = file_get_contents('../osta/css/themes/custom.css', NULL, NULL, 117, 6); echo ($section); ?>"/>
					<span class="input-group-addon"><i></i></span>
				</div>

				Mobile Menu Background<br />						
				<div id="cp5" class="input-group colorpicker-component" title="Using input value">
					<input type="text" class="form-control input-lg" name="mobilemenubg" value="#<?php $section = file_get_contents('../osta/css/themes/custom.css', NULL, NULL, 174, 6); echo ($section); ?>"/>
					<span class="input-group-addon"><i></i></span>
				</div>				

				Mobile Menu Link<br />
				<div id="cp6" class="input-group colorpicker-component" title="Using input value">
					<input type="text" class="form-control input-lg" name="mobilelinkcolor" value="#<?php $section = file_get_contents('../osta/css/themes/custom.css', NULL, NULL, 208, 6); echo ($section); ?>"/>
					<span class="input-group-addon"><i></i></span>
				</div>	
			</div>

			Sticky Bar Background<br />		
			<div id="cp7" class="input-group colorpicker-component" title="Using input value">
				<input type="text" class="form-control input-lg" name="stickybar" value="#<?php $section = file_get_contents('../osta/css/themes/custom.css', NULL, NULL, 143, 6); echo ($section); ?>"/>
				<span class="input-group-addon"><i></i></span>
			</div>				

			<script>
				  $(function () {
				$('#cp1, #cp2, #cp3, #cp4, #cp5, #cp6, #cp7').colorpicker({
				  customClass: 'colorpicker-2x',
				  useAlpha: false	
				});
			  });
			</script> 												
		</td>
	</tr>	
	<tr>
		<td>
			<button id="save-custom-colors" type="submit" value="Save Custom Theme">Save Custom Colors</button>		
			</form><br /><br />	
		</td>
	</tr>						
</tbody>
</table>
<table id="custom-text-and-links" width="100%" border="0" cellspacing="0" cellpadding="0">
<tbody>      
	<tr>
		<td><h2>Custom Text and Links</h2></td>
	</tr>
	<tr>
		<td> 
         	<br />
			<div class="responsive-div-theme">
				<div id="one-theme">
					 <form action="../osta/css/themes/choose-text.php" method="post">
						<?php csrf_token(); ?>  
						<input type=text name="title" placeholder="<?php echo file_get_contents("../osta/css/themes/title.txt"); ?>">
						<button id="theme-title-button" type="submit" value="Save Header Title">Header Title</button>				
					 </form>			
					 <form action="../osta/css/themes/choose-subtext.php" method="post">
						<?php csrf_token(); ?>  
						<input type=text name="subtitle" placeholder="<?php echo file_get_contents("../osta/css/themes/subtitle.txt"); ?>">
						<button id="theme-subtitle-button" type="submit" value="Save Header Subtitle">Header Subtitle</button>	
					 </form>
				</div>
				<div id="two-theme">
					 <form action="../osta/css/themes/choose-mobile-text.php" method="post">
						<?php csrf_token(); ?>  
						<input type=text name="title" placeholder="<?php echo file_get_contents("../osta/css/themes/mobile-text.txt"); ?>">
						<button id="mobile-text-button" type="submit" value="Save Header Title">Mobile Menu Text</button>		
					 </form>			
					 <form action="../osta/css/themes/choose-mobile-link.php" method="post">
						<?php csrf_token(); ?>  
						<input type=text name="link" placeholder="<?php echo file_get_contents("../osta/css/themes/mobile-link.txt"); ?>">
						<button id="mobile-link-button" type="submit" value="Save Header Subtitle">Mobile Menu Link</button><br /><br />	
					 </form>
				</div>
			</div>
		</td>
	</tr>				
</tbody>
</table>
<table id="additional-options" width="100%" border="0" cellspacing="0" cellpadding="0">
<tbody>      
	<tr>
		<td><h2>Additional Options</h2></td>
	</tr>
	<tr>
		<td> 
			<br />
			<div id="client-side-language-bar">Client Side Language Bar</div>
			<span class="small-text">
			If you want clients to be able to switch between languages, this must be ON. If you only use one language, you should turn this OFF.<br /><br />
			
         	<form action="../osta/css/themes/language-bar.php" method="post">
            <?php csrf_token(); ?>     			
				<label class="checkcontainer">On
				  <input type="radio" name="language-bar" value="on">
				  <span class="radiobtn"></span>
				</label>
				<label class="checkcontainer">Off
				  <input type="radio" name="language-bar" value="off">
				  <span class="radiobtn"></span>
				</label>
				<button id="language-bar-save-button" type="submit" value="Save Selection">Save Selection</button>				
			</form>
		</td>
	</tr>				
</tbody>
</table>
<table id="theme3">	
<tbody>    	  
	<tr>
		<td><h2>Theme Information</h2></td>
	</tr>
	<tr>
		<td>
			<br />
			You are using <span class="code-green">
				
			<?php $themev = ROOT_DIR ."osta/version.txt";
				echo file_get_contents($themev);
				?>  
			</span>	on osTicket <span class="code-green">
			<?php echo sprintf("%s", THIS_VERSION); ?></span>.<br /><br />
			
			The current version is 
			<strong>
			<?php $currentv = "https://osticketawesome.com/release/current-version.txt";
				echo file_get_contents($currentv);
				?></strong> <br /><br />
			Thank you for supporting osTicket Awesome!
		</td>
	</tr>	
</tbody>
</table>
<table id="theme4">	
<tbody>    	  	
	<tr>
		<td colspan="2"><h2>Software Environment</h2></td>
	</tr>
	<tr>
		<td colspan="2">
			<br />		
			You are running <span class="code-green">PHP <?php echo phpversion(); ?></span> with <span class="code-green">MySQL <?php echo db_version(); ?></span> on <span class="code-green"><?php echo $_SERVER['SERVER_SOFTWARE']; ?> web server</span>.<br />
		</td>
	</tr>	
</tbody>
</table>
