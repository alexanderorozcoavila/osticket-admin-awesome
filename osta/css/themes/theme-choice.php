<?php
$action = $_GET["action"];
$myText = $_POST["mytheme"];

if($action = "save") {
  $targetFolder = "";
  file_put_contents($targetFolder."selected.txt", $myText);
}
?>   
