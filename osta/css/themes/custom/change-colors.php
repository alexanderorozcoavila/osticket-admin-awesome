<?php 

$headerbg 			= $_POST['headerbg'];
$headertitlecolor  	= $_POST['headertitlecolor'];
$navbarbg          	= $_POST['navbarbg'];
$navbarlink 		= $_POST['navbarlink'];
$mobilemenubg 		= $_POST['mobilemenubg'];
$mobilelinkcolor	= $_POST['mobilelinkcolor'];
$stickybar			= $_POST['stickybar'];

$contents = file_get_contents('template.style.css');

$css = str_replace('{{headerbg}}',$headerbg,$contents);
$css = str_replace('{{headertitlecolor}}',$headertitlecolor,$css);
$css = str_replace('{{navbarbg}}',$navbarbg,$css);
$css = str_replace('{{navbarlink}}',$navbarlink,$css);
$css = str_replace('{{mobilemenubg}}',$mobilemenubg,$css);
$css = str_replace('{{mobilelinkcolor}}',$mobilelinkcolor,$css);
$css = str_replace('{{stickybar}}',$stickybar,$css);

file_put_contents('../custom.css',$css);

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>