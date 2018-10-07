<?php

/* PRIORITY TEMPLATE FILE */
$staff=$ticket->getStaff();
$lock=$ticket->getLock();
$role=$thisstaff->getRole($ticket->getDeptId());
$error=$msg=$warn=null;

?>

<script type = "text/javascript">
function updatePriority ( ticketid, value ) {
 $.ajax({
    type:"post",
    url: "priority.php",
    data: ({ticketid: ticketid, priority: value}),
    success:function(response){
        self.location.reload();
        //alert(response);
    }
});
}
</script>

<?php

$sql = "SELECT * FROM ost_ticket__cdata WHERE ticket_id = '".$ticket->getId()."'";
$result = db_query($sql);
while($row = db_fetch_array($result)){
    $priority = $row['priority'];
}

echo sprintf(
        '<div id="t%s" class="priority-popup">
         <h3>'.__('Ticket %s').': Choose Priority </h3>',
         $ticket->getNumber(),
         $ticket->getNumber(),
         Format::htmlchars($ticket->getSubject()));

?>
<div style="float:left;margin:0 0 6px 20px">
	<div id="priority-4"><div class="color">&nbsp;</div><input type = "radio" name = "priority" value "4" onclick="updatePriority(<?php echo $ticket->getId()?>, '4');" <?php if ( $priority == "4" ) echo "checked"; ?>>Emergency</div>
	<div id="priority-3"><div class="color">&nbsp;</div><input type = "radio" name = "priority" value "3" onclick="updatePriority(<?php echo $ticket->getId()?>, '3');" <?php if ( $priority == "3" ) echo "checked"; ?>>High</div>
</div>
<div style="float:left;margin:0 0 6px 24px;">
	<div id="priority-2"><div class="color">&nbsp;</div><input type = "radio" name = "priority" value "2" onclick="updatePriority(<?php echo $ticket->getId()?>, '2');" <?php if ( $priority == "2" ) echo "checked"; ?>>Normal</div>
	<div id="priority-1"><div class="color">&nbsp;</div><input type = "radio" name = "priority" value "1" onclick="updatePriority(<?php echo $ticket->getId()?>, '1');" <?php if ( $priority == "1" ) echo "checked"; ?>>Low</div>
</div>
<?php
echo '</div>';

?>