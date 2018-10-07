<?php 
/* HANDLES AJAX REQUEST FOR PRIORITY */
//include('main.inc.php');
//include_once(INCLUDE_DIR.'class.config.php');
require('staff.inc.php');
require_once(INCLUDE_DIR.'class.ticket.php');
require_once(INCLUDE_DIR.'class.dept.php');
require_once(INCLUDE_DIR.'class.filter.php');
require_once(INCLUDE_DIR.'class.canned.php');
require_once(INCLUDE_DIR.'class.json.php');
require_once(INCLUDE_DIR.'class.dynamic_forms.php');
require_once(INCLUDE_DIR.'class.export.php');       // For paper sizes


if ( isset( $_REQUEST['ticketid']) != '' ) {
 
    $sql = "UPDATE os_ticket__cdata SET priority = '".$_REQUEST['priority']."' WHERE ticket_id = '".$_REQUEST['ticketid']."'";    
    echo $sql;
    if ( db_query($sql)) 
        echo $sql;

}


?>