<?php
 $path = 'custom-test.css';
 if (isset($_POST['header-bg'])) {
    $fh = fopen($path,"a+");
    $string = $_POST['header-bg'];
    fwrite($fh,$string,7); // Write information to the file
    fclose($fh); // Close the file
	header('Location: ' . $_SERVER['HTTP_REFERER']);
 }
?>