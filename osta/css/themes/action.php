<?php
 $path = 'data.txt';
 if (isset($_POST['field1'])) {
    $fh = fopen($path,"wa+");
    $string = $_POST['field1'];
    fwrite($fh,$string); // Write information to the file
    fclose($fh); // Close the file
 }
?>