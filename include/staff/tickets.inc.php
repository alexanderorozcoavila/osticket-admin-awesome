<?php


$link = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
                        
if ($link == false) {
    die("ERROR: Could not connect. "
                .mysqli_connect_error());
}

$sql = "SELECT id, thread_id, timestamp as hora, DATE_ADD(timestamp, INTERVAL ".$cfg->getScriptConflictTime()." MINUTE) as limite, NOW() as hora_actual FROM ".TABLE_PREFIX."thread_event WHERE data = 'notedit'";
// print $sql;
// exit;
$res = mysqli_query($link, $sql);
while ($row = mysqli_fetch_array($res)) {
    $hora_thread = strtotime($row['hora']);
    $hora_limite = strtotime($row['limite']);
    $hora_actual = strtotime($row['hora_actual']);
    if($hora_actual >= $hora_limite){
        $sql2 ="DELETE FROM ".TABLE_PREFIX."thread_event WHERE ".TABLE_PREFIX."thread_event.id = ".$row['id'];
        $resultado = mysqli_query($link, $sql2);
        if($resultado){
            // echo $row['thread_id'].": liberado...";
            // echo "\n";
        }else{
            // echo $row['thread_id'].": en espera...";
            // echo "\n";
        }
    }else{
        // echo $row['thread_id'].": en espera...";
        // echo "\n";
    }

}
$search = SavedSearch::create();
$tickets = TicketModel::objects();
$clear_button = false;
$view_all_tickets = $date_header = $date_col = false;

// Make sure the cdata materialized view is available
TicketForm::ensureDynamicDataView();

// Figure out REFRESH url â€”Â which might not be accurate after posting a
// response
list($path,) = explode('?', $_SERVER['REQUEST_URI'], 2);
$args = array();
parse_str($_SERVER['QUERY_STRING'], $args);

// Remove commands from query
unset($args['id']);
if ($args['a'] !== 'search') unset($args['a']);

$refresh_url = $path . '?' . http_build_query($args);

$sort_options = array(
    'priority,updated' =>   __('Priority + Most Recently Updated'),
    'updated' =>            __('Most Recently Updated'),
    'priority,created' =>   __('Priority + Most Recently Created'),
    'due' =>                __('Due Date'),
    'priority,due' =>       __('Priority + Due Date'),
    'number' =>             __('Ticket Number'),
    'answered' =>           __('Most Recently Answered'),
    'closed' =>             __('Most Recently Closed'),
    'hot' =>                __('Longest Thread'),
    'relevance' =>          __('Relevance'),
);

// Queues columns

$queue_columns = array(
        'number' => array(
            'width' => '7.4%',
            'heading' => '#',
            ),
        'date' => array(
            'width' => '14.6%',
            'heading' => __('Created'),
            'sort_col' => 'created',
            ),
        'subject' => array(
            'width' => '29.8%',
            'heading' => __('Subject'),
            'sort_col' => 'cdata__subject',
            ),
        'name' => array(
            'width' => '18.1%',
            'heading' => __('From'),
            'sort_col' =>  'user__name',
            ),
        'status' => array(
            'width' => '8.4%',
            'heading' => __('Status'),
            'sort_col' => 'status_id',
            ),
        'priority' => array(
            'width' => '8.4%',
            'heading' => __('Priority'),
            'sort_col' => 'cdata__:priority__priority_urgency',
            ),
        'assignee' => array(
            'width' => '16%',
            'heading' => __('Agent'),
            ),
        'dept' => array(
            'width' => '16%',
            'heading' => __('Department'),
            'sort_col'  => 'dept__name',
            ),
        );

$use_subquery = true;

// Figure out the queue we're viewing
$queue_key = sprintf('::Q:%s', ObjectModel::OBJECT_TYPE_TICKET);
$queue_name = $_SESSION[$queue_key] ?: '';

switch ($queue_name) {
case 'closed':
    $status='closed';
    $results_type=__('Closed Tickets');
    $showassigned=true; //closed by.
    $queue_sort_options = array('closed', 'priority,due', 'due',
        'priority,updated', 'priority,created', 'answered', 'number', 'hot');
    break;
case 'overdue':
    $status='open';
    $results_type=__('Overdue Tickets');
    $tickets->filter(array('isoverdue'=>1));
    $queue_sort_options = array('priority,due', 'due', 'priority,updated',
        'updated', 'answered', 'priority,created', 'number', 'hot');
    break;
case 'assigned':
    $status='open';
    $staffId=$thisstaff->getId();
    $results_type=__('My Tickets');
    $tickets->filter(Q::any(array(
        'staff_id'=>$thisstaff->getId(),
        Q::all(array('staff_id' => 0, 'team_id__gt' => 0)),
    )));
    $queue_sort_options = array('updated', 'priority,updated',
        'priority,created', 'priority,due', 'due', 'answered', 'number',
        'hot');
    break;
case 'answered':
    $status='open';
    $showanswered=true;
    $results_type=__('Answered Tickets');
    $tickets->filter(array('isanswered'=>1));
    $queue_sort_options = array('answered', 'priority,updated', 'updated',
        'priority,created', 'priority,due', 'due', 'number', 'hot');
    break;
default:
case 'search':
    $queue_sort_options = array('priority,updated', 'priority,created',
        'priority,due', 'due', 'updated', 'answered',
        'closed', 'number', 'hot');
    // Consider basic search
    if ($_REQUEST['query']) {
        $results_type=__('Search Results');
        // Use an index if possible
        if ($_REQUEST['search-type'] == 'typeahead') {
            if (Validator::is_email($_REQUEST['query'])) {
            $tickets = $tickets->filter(array(
                'user__emails__address' => $_REQUEST['query'],
            ));
        }
            elseif ($_REQUEST['query']) {
                $tickets = $tickets->filter(array(
                    'number' => $_REQUEST['query'],
            ));
              }
        }
        elseif (isset($_REQUEST['query'])
            && ($q = trim($_REQUEST['query']))
            && strlen($q) > 2
        ) {
            // [Search] click, consider keywords
            $__tickets = $ost->searcher->find($q, $tickets);
            if (!count($__tickets) && preg_match('`\w$`u', $q)) {
                // Do wildcard search if no hits
                $__tickets = $ost->searcher->find($q.'*', $tickets);
            }
            $tickets = $__tickets;
            $has_relevance = true;
        }
        // Clear sticky search queue
        unset($_SESSION[$queue_key]);
        break;
    }
    // Apply user filter
    elseif (isset($_GET['uid']) && ($user = User::lookup($_GET['uid']))) {
        $tickets->filter(array('user__id'=>$_GET['uid']));
        $results_type = sprintf('%s â€” %s', __('Search Results'),
            $user->getName());
        if (isset($_GET['status']))
            $status = $_GET['status'];
        // Don't apply normal open ticket
        break;
    }
    elseif (isset($_GET['orgid']) && ($org = Organization::lookup($_GET['orgid']))) {
        $tickets->filter(array('user__org_id'=>$_GET['orgid']));
        $results_type = sprintf('%s â€” %s', __('Search Results'),
            $org->getName());
        if (isset($_GET['status']))
            $status = $_GET['status'];
        // Don't apply normal open ticket
        break;
    } elseif (isset($_SESSION['advsearch'])) {
        $form = $search->getFormFromSession('advsearch');
        $tickets = $search->mangleQuerySet($tickets, $form);
        $view_all_tickets = $thisstaff->hasPerm(SearchBackend::PERM_EVERYTHING);
        $results_type=__('Advanced Search')
            . '<a class="action-button" style="font-size: 15px;" href="?clear_filter"><i style="top:0" class="icon-ban-circle"></i> <em>' . __('clear') . '</em></a>';
        foreach ($form->getFields() as $sf) {
            if ($sf->get('name') == 'keywords' && $sf->getClean()) {
                $has_relevance = true;
                break;
            }
        }
        break;
    }
    // Fall-through and show open tickets
case 'open':
    $status='open';
    $queue_name = $queue_name ?: 'open';
    $results_type=__('Open Tickets');
    if (!$cfg->showAnsweredTickets())
        $tickets->filter(array('isanswered'=>0));
    $queue_sort_options = array('priority,updated', 'updated',
        'priority,due', 'due', 'priority,created', 'answered', 'number',
        'hot');
    break;
}

// Open queues _except_ assigned should respect showAssignedTickets()
// settings
if ($status != 'closed' && $queue_name != 'assigned') {
    $hideassigned = ($cfg && !$cfg->showAssignedTickets()) && !$thisstaff->showAssignedTickets();
    $showassigned = !$hideassigned;
    if ($queue_name == 'open' && $hideassigned)
        $tickets->filter(array('staff_id'=>0, 'team_id'=>0));
}

// Apply primary ticket status
if ($status)
    $tickets->filter(array('status__state'=>$status));

// Impose visibility constraints
// ------------------------------------------------------------
if (!$view_all_tickets) {
    // -- Open and assigned to me
    $assigned = Q::any(array(
        'staff_id' => $thisstaff->getId(),
    ));
    // -- Open and assigned to a team of mine
    if ($teams = array_filter($thisstaff->getTeams()))
        $assigned->add(array('team_id__in' => $teams));

    $visibility = Q::any(new Q(array('status__state'=>'open', $assigned)));

    // -- Routed to a department of mine
    if (!$thisstaff->showAssignedOnly() && ($depts=$thisstaff->getDepts()))
        $visibility->add(array('dept_id__in' => $depts));

    $tickets->filter(Q::any($visibility));
}

// TODO :: Apply requested quick filter

// Apply requested pagination
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$count = $tickets->count();
$pageNav = new Pagenate($count, $page, PAGE_LIMIT);
$pageNav->setURL('tickets.php', $args);
$tickets = $pageNav->paginate($tickets);

// Apply requested sorting
$queue_sort_key = sprintf(':Q%s:%s:sort', ObjectModel::OBJECT_TYPE_TICKET, $queue_name);

// If relevance is available, use it as the default
if ($has_relevance) {
    array_unshift($queue_sort_options, 'relevance');
}
elseif ($_SESSION[$queue_sort_key][0] == 'relevance') {
    unset($_SESSION[$queue_sort_key]);
}

if (isset($_GET['sort'])) {
    $_SESSION[$queue_sort_key] = array($_GET['sort'], $_GET['dir']);
}
elseif (!isset($_SESSION[$queue_sort_key])) {
    $_SESSION[$queue_sort_key] = array($queue_sort_options[0], 0);
}

list($sort_cols, $sort_dir) = $_SESSION[$queue_sort_key];
$orm_dir = $sort_dir ? QuerySet::ASC : QuerySet::DESC;
$orm_dir_r = $sort_dir ? QuerySet::DESC : QuerySet::ASC;

switch ($sort_cols) {
case 'number':
    $queue_columns['number']['sort_dir'] = $sort_dir;
    $tickets->extra(array(
        'order_by'=>array(
            array(SqlExpression::times(new SqlField('number'), 1), $orm_dir)
        )
    ));
    break;

case 'priority,created':
    $tickets->order_by(($sort_dir ? '-' : '') . 'cdata__:priority__priority_urgency');
    // Fall through to columns for `created`
case 'created':
    $queue_columns['date']['heading'] = __('Date Created');
    $queue_columns['date']['sort_col'] = $date_col = 'created';
    $tickets->values('created');
    $tickets->order_by($sort_dir ? 'created' : '-created');
    break;

case 'priority,due':
    $tickets->order_by('cdata__:priority__priority_urgency', $orm_dir_r);
    // Fall through to add in due date filter
case 'due':
    $queue_columns['date']['heading'] = __('Due Date');
    $queue_columns['date']['sort'] = 'due';
    $queue_columns['date']['sort_col'] = $date_col = 'est_duedate';
    $tickets->values('est_duedate');
    $tickets->order_by(SqlFunction::COALESCE(new SqlField('est_duedate'), 'zzz'), $orm_dir_r);
    break;

case 'closed':
    $queue_columns['date']['heading'] = __('Date Closed');
    $queue_columns['date']['sort'] = $sort_cols;
    $queue_columns['date']['sort_col'] = $date_col = 'closed';
    $queue_columns['date']['sort_dir'] = $sort_dir;
    $tickets->values('closed');
    $tickets->order_by('closed', $orm_dir);
    break;

case 'answered':
    $queue_columns['date']['heading'] = __('Last Response');
    $queue_columns['date']['sort'] = $sort_cols;
    $queue_columns['date']['sort_col'] = $date_col = 'thread__lastresponse';
    $queue_columns['date']['sort_dir'] = $sort_dir;
    $date_fallback = '<em class="faded">'.__('unanswered').'</em>';
    $tickets->order_by('thread__lastresponse', $orm_dir);
    $tickets->values('thread__lastresponse');
    break;

case 'hot':
    $tickets->order_by('thread_count', $orm_dir);
    $tickets->annotate(array(
        'thread_count' => SqlAggregate::COUNT('thread__entries'),
    ));
    break;

case 'relevance':
    $tickets->order_by(new SqlCode('__relevance__'), $orm_dir);
    break;

case 'assignee':
    $tickets->order_by('staff__lastname', $orm_dir);
    $tickets->order_by('staff__firstname', $orm_dir);
    $tickets->order_by('team__name', $orm_dir);
    $queue_columns['assignee']['sort_dir'] = $sort_dir;
    break;

default:
    if ($sort_cols && isset($queue_columns[$sort_cols])) {
        $queue_columns[$sort_cols]['sort_dir'] = $sort_dir;
        if (isset($queue_columns[$sort_cols]['sort_col']))
            $sort_cols = $queue_columns[$sort_cols]['sort_col'];
        $tickets->order_by($sort_cols, $orm_dir);
        break;
    }

case 'priority,updated':
    $tickets->order_by('cdata__:priority__priority_urgency', $orm_dir_r);
    // Fall through for columns defined for `updated`
case 'updated':
    $queue_columns['date']['heading'] = __('Last Updated');
    $queue_columns['date']['sort'] = $sort_cols;
    $queue_columns['date']['sort_col'] = $date_col = 'lastupdate';
    $tickets->order_by('lastupdate', $orm_dir);
    break;
}

if (in_array($sort_cols, array('created', 'due', 'updated')))
    $queue_columns['date']['sort_dir'] = $sort_dir;

// Rewrite $tickets to use a nested query, which will include the LIMIT part
// in order to speed the result
$orig_tickets = clone $tickets;
$tickets2 = TicketModel::objects();
$tickets2->values = $tickets->values;
$tickets2->filter(array('ticket_id__in' => $tickets->values_flat('ticket_id')));

// Transfer the order_by from the original tickets
$tickets2->order_by($orig_tickets->getSortFields());
$tickets = $tickets2;

// Save the query to the session for exporting
$_SESSION[':Q:tickets'] = $tickets;

TicketForm::ensureDynamicDataView();

// Select pertinent columns
// ------------------------------------------------------------
$tickets->values('lock__staff_id', 'staff_id', 'isoverdue', 'team_id',
'ticket_id', 'number', 'cdata__subject', 'user__default_email__address',
'source', 'cdata__:priority__priority_color', 'cdata__:priority__priority_desc', 'status_id', 'status__name', 'status__state', 'dept_id', 'dept__name', 'user__name', 'lastupdate', 'isanswered', 'staff__firstname', 'staff__lastname', 'team__name');

// Add in annotations
$tickets->annotate(array(
    'collab_count' => TicketThread::objects()
        ->filter(array('ticket__ticket_id' => new SqlField('ticket_id', 1)))
        ->aggregate(array('count' => SqlAggregate::COUNT('collaborators__id'))),
    'attachment_count' => TicketThread::objects()
        ->filter(array('ticket__ticket_id' => new SqlField('ticket_id', 1)))
        ->filter(array('entries__attachments__inline' => 0))
        ->aggregate(array('count' => SqlAggregate::COUNT('entries__attachments__id'))),
    'thread_count' => TicketThread::objects()
        ->filter(array('ticket__ticket_id' => new SqlField('ticket_id', 1)))
        ->exclude(array('entries__flags__hasbit' => ThreadEntry::FLAG_HIDDEN))
        ->aggregate(array('count' => SqlAggregate::COUNT('entries__id'))),
));


// Make sure we're only getting active locks
$tickets->constrain(array('lock' => array(
                'lock__expire__gt' => SqlFunction::NOW())));

?>

<!-- SEARCH FORM START -->
<div id='basic_search'>
<!--  <div class="pull-right" style="height:25px">
    <span class="valign-helper"></span>
    <?php
    require STAFFINC_DIR.'templates/queue-sort.tmpl.php';
    ?>
  </div>-->
<!--    <form action="tickets.php" method="get" onsubmit="javascript:
  $.pjax({
    url:$(this).attr('action') + '?' + $(this).serialize(),
    container:'#pjax-container',
    timeout: 2000
  });
return false;">
    <input type="hidden" name="a" value="search">
    <input type="hidden" name="search-type" value=""/>
    <div class="attached input">
		<input type="text" class="basic-search" data-url="ajax.php/tickets/lookup" name="query" placeholder="Search Here"
			autofocus size="30" value="<?php echo Format::htmlchars($_REQUEST['query'], true); ?>"
			autocomplete="off" autocorrect="off" autocapitalize="off">		
      <button type="submit" class="attached button"><i class="icon-search"></i>
		</button>
    </div>
	<a href="#" onclick="javascript:$.dialog('ajax.php/tickets/search', 201);">
		<div class="action-button advanced-search gray-light2">
			<div class="button-icon">
			</div>
			<div class="button-text advanced-search">
				Advanced Search
			</div>
			<div class="button-spacing">
				&nbsp;
			</div>
		</div>
	</a>
    <i class="help-tip icon-question-sign" href="#advanced"></i>
    </form>-->
</div>
<!-- SEARCH FORM END -->
<div class="clear"></div>
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="sticky bar opaque">
       
		<div class="pull-left flush-left">
			<h2><a href="<?php echo $refresh_url; ?>"
				title="<?php echo __('Refresh'); ?>"><?php echo
				$results_type; ?></a>								
			</h2>
		</div>       
       
        <div class="content">

            <div class="pull-right flush-right page-top">            
            
				<form action="tickets.php" method="get" onsubmit="javascript:
					  $.pjax({
						url:$(this).attr('action') + '?' + $(this).serialize(),
						container:'#pjax-container',
						timeout: 2000
					  });
					return false;">
					<input type="hidden" name="a" value="search">
					<input type="hidden" name="search-type" value=""/>
					<div class="attached input">
						<input type="text" class="basic-search" data-url="ajax.php/tickets/lookup" name="query"  placeholder="<?php echo __('Search Here'); ?>"
							size="30" value="<?php echo Format::htmlchars($_REQUEST['query'], true); ?>"
							autocomplete="off" autocorrect="off" autocapitalize="off">		
					  <button type="submit" class="attached button"><i class="icon-search"></i>
						</button>
					</div>
					<a href="#" onclick="javascript:$.dialog('ajax.php/tickets/search', 201);">
						<div class="action-button advanced-search gray-light2">
							<div class="button-icon">
							</div>
							<div class="button-text advanced-search">
								Advanced	
								<svg style="width:20px;height:20px" viewBox="0 0 20 20">
									<path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
								</svg>							
							</div>
							<div class="button-spacing">
								&nbsp;
							</div>
						</div>
					</a>
				</form>            

			<span class="valign-helper"></span>
			<?php
			require STAFFINC_DIR.'templates/queue-sort.tmpl.php';
			?>

            <?php
            if ($count) {
                Ticket::agentActions($thisstaff, array('status' => $status));
            }?>
            </div>
        </div>
    </div>
</div>
	<div class="clear"></div>
	<form action="tickets.php" method="POST" name='tickets' id="tickets">
	<?php csrf_token(); ?>
	 <input type="hidden" name="a" value="mass_process" >
	 <input type="hidden" name="do" id="action" value="" >
	 <input type="hidden" name="status" value="<?php echo
	 Format::htmlchars($_REQUEST['status'], true); ?>" >


<!-- DESKTOP -->
<div class="desktop-ticket-list">
<!-- DESKTOP -->	
	
	


	 <table class="list" border="0" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr class="head">

		<!-- Head Priority -->	
					<th class="head-priority" <?php echo $pri_sort;?>>
						<a <?php echo $pri_sort; ?> href="tickets.php?sort=pri&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
							>			
							</a>
					</th>

		<!-- Head Checkbox -->          
					<?php 
						if($thisstaff->canManageTickets()) { ?>
				<th class="head-checkbox" width="2px"></th>
					<?php } ?>  

		<!-- Head Date -->            
					<th class="head-date">
						<a  <?php echo $date_sort; ?> href="tickets.php?sort=date&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
							><?php echo __('Date'); ?></a>
					</th>  

		<!-- Head Client -->             
					<th class="head-client">
						<a <?php echo $name_sort; ?> href="tickets.php?sort=name&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
							 ><?php echo __('Client');?></a>
					</th>

		<!-- Head Description -->            
					<th class="head-description">
						 <a <?php echo $subj_sort; ?> href="tickets.php?sort=subj&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
							><?php echo __('Description'); ?></a>
					</th>

		<!-- Head Status -->             
					<?php
					if($search && !$status) { ?>
						<th class="head-status">
							<a <?php echo $status_sort; ?> href="tickets.php?sort=status&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
								><?php echo __('Status');?></a>
					</th>

		<!-- Head Closed By --> <!-- OR -->    
		<!-- Head Assigned To --> <!-- OR -->   
		<!-- Head Department -->  
					<?php
					}

					if($showassigned ) {
						//Closed by
						if(!strcasecmp($status,'closed')) { ?>
							<th class="head-closed-by">
								<a <?php echo $staff_sort; ?> href="tickets.php?sort=staff&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
									><?php echo __('Closed By'); ?></a>
					</th> 
		<!-- OR -->
		<!-- Head Assigned To -->    
						<?php
						} else { //assigned to ?>
							<th class="head-assigned-to">
								<a <?php echo $assignee_sort; ?> href="tickets.php?sort=assignee&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
									><?php echo __('Assigned To'); ?></a>
					</th>         
		<!-- Head Department -->             
						<?php
						}
					} else { ?>
						<th class="head-department">
							<a <?php echo $dept_sort; ?> href="tickets.php?sort=dept&order=<?php echo $negorder;?><?php echo $qstr; ?>"
								><?php echo __('Dept'); ?></a>
					</th>                

					<?php
					} ?>

		<!-- Head ID --> 
					<th class="head-id">
						<a style="padding-left:7px;" <?php echo $id_sort; ?> href="tickets.php?sort=ID&order=<?php echo $negorder; ?><?php echo $qstr; ?>"
							title="<?php echo sprintf(__('Sort by %s %s'), __('Ticket ID'), __($negorder)); ?>"><?php echo __('ID'); ?></a>
					</th>   

				</tr>
			 </thead>
		 <tbody>
		<?php
		// Setup Subject field for display
		$subject_field = TicketForm::getInstance()->getField('subject');
		$class = "row1";
		$total=0;
		$ids=($errors && $_POST['tids'] && is_array($_POST['tids']))?$_POST['tids']:null;
		foreach ($tickets as $T) {
			$total += 1;
				$tag=$T['staff_id']?'assigned':'openticket';
				$flag=null;
				if($T['lock__staff_id'] && $T['lock__staff_id'] != $thisstaff->getId())
					$flag='locked';
				elseif($T['isoverdue'])
					$flag='overdue';

				$lc='';
				if ($showassigned) {
					if ($T['staff_id'])
						$lc = new AgentsName($T['staff__firstname'].' '.$T['staff__lastname']);
					elseif ($T['team_id'])
						$lc = Team::getLocalById($T['team_id'], 'name', $T['team__name']);
				}
				else {
					$lc = Dept::getLocalById($T['dept_id'], 'name', $T['dept__name']);
				}
				$tid=$T['number'];
				$subject = $subject_field->display($subject_field->to_php($T['cdata__subject']));
				$threadcount=$T['thread_count'];
				if(!strcasecmp($T['status__state'],'open') && !$T['isanswered'] && !$T['lock__staff_id']) {
					$tid=sprintf('<b>%s</b>',$tid);
				}
				?>
                <?php
                if(isset($_GET['status'])){
                    $statusLista = '&status='.$_GET['status'];
                }else{
                    $statusLista = '&status=open';
                }

                $ticket=Ticket::lookup($T['ticket_id']);
                if($ticket->getThread()->getLogConflict($T['ticket_id'])){
                    if($ticket->getThread()->getLogConflictUser($T['ticket_id'])){
                        $nombreagente = "";
                    }else{
                        $nombreagentes = $ticket->getThread()->getLogConflictUserAgente($T['ticket_id']);
                        $nombreagente =  $nombreagentes["username"];   
                    }
                }else{
                    $nombreagente = "";
                }

            ?>
        <!-- Table Priority  --inicio-- -->
                <tr id="<?php echo $T['ticket_id']; ?>" class="tr_ticket_es">	
                

					<td class="cursor priority <?php echo $T['cdata__:priority__priority_desc']; ?>" style="cursor:pointer" nowrap >
						<a style="display:block;" class="preview cursor" href="#" onclick='return false;'
							data-preview="#tickets/<?php echo $T['ticket_id']; ?>/priority">
							<?php echo $T['cdata__priority__priority_desc']; ?>&nbsp;
						</a>
					</td>			

		<!-- Table Checkbox -->  
					<?php if($thisstaff->canManageTickets()) {
						$sel=false;
						if($ids && in_array($T['ticket_id'], $ids))
							$sel=true;
						?>
				
					<td align="center" class="checkbox nohover">
						<label for="checkboxG4-<?php echo $T['ticket_id']; ?>" class="css-label checkbox">
							<input id="checkboxG4-<?php echo $T['ticket_id']; ?>" 
								class="ckb css-checkbox checkbox" 
								type="checkbox" name="tids[]"
								value="<?php echo $T['ticket_id']; ?>" 
								<?php echo $sel?'checked="checked"':''; ?>
								onchange="console.log('changed');">
							<span></span>	
						</label>
					</td>
					<?php } ?>
					
					<td class="mobile-only">
						<table class="mobile-ticket-list-outter-table" border="0" cellspacing="0" cellpadding="0">
						  <tbody>
							<tr id="<?php echo $T['ticket_id']; ?>">
							  <td>  
								<table class="table-accordion">
								   <tr class="accordion-row">
									  <td colspan="1" class="accordion-td name">
										 <div class="inner-client-summary">
                                            <!-- Table Client -->	
                                            <?php 
                                                    if($nombreagente == ""){
                                                ?>
                                                <a href="tickets.php?id=<?php echo $T['ticket_id']; ?>"><?php
											   $un = new UsersName($T['user__name']);
												echo Format::htmlchars($un);
											   ?></a>
                                                <?php
                                                    }else{
                                                ?>
                                                   <a class="conflictoTicket" nombreagente="<?php echo $nombreagente; ?>"><?php
											   $un = new UsersName($T['user__name']);
												echo Format::htmlchars($un);
											   ?></a> 
                                                <?php
                                                
                                                    }
                                                ?>
											</span>
										 </div>
										 <div class="details">
											<ul>
											   <li> 
												  <div class="ticket-number-mobile">
												  <span class="table-id" title="<?php echo $T['user__default_email__address']; ?>" nowrap>
												  <a class="Icon <?php echo strtolower($T['source']); ?>Ticket preview"
													 title="Preview Ticket"
													 href="tickets.php?id=<?php echo $T['ticket_id']; ?>"
													 data-preview="#tickets/<?php echo $T['ticket_id']; ?>/preview"
													 >#<?php echo $tid; ?></a>
												  </span>
												  </div>
												  <div class="agent">
												  <?php echo Format::htmlchars($lc); ?></div>
												  <!-- Table Date -->
												  <div class="nowrap">
													 <div class="due-date">
														<?php echo Format::date($T[$date_col ?: 'lastupdate']) ?: $date_fallback; ?>
													 </div>
													 <div class="due-time">
														<?php echo Format::time($T[$date_col ?: 'lastupdate']) ?: $date_fallback; ?>
													 </div>
												  </div>
											   </li>
											</ul>
										 </div>
									  </td>
								   </tr>
								   <tr class="accordion-row">
									  <td class="accordion-td summary">
										 <div class="inner-ticket-summary">
											<!-- Table Description -->
											<div style="max-width: <?php
											   $base = 279;
											   $base = 280;
											   // Make room for the paperclip and some extra
											   if ($T['attachment_count']) $base -= 18;
											   // Assume about 8px per digit character
											   if ($threadcount > 1) $base -= 20 + ((int) log($threadcount, 10) + 1) * 8;
											   // Make room for overdue flag and friends
											   if ($flag) $base -= 20;
											   echo $base; ?>px; max-height: 1.2em"
											   class="<?php if ($flag) { ?>Icon <?php echo $flag; ?>Ticket <?php } ?>link truncate"
											   <?php if ($flag) { ?> title="<?php echo ucfirst($flag); ?> Ticket" <?php } ?>
											   href="tickets.php?id=<?php echo $T['ticket_id']; ?>">
											   
											   
                                               <?php 
                                                    if($nombreagente == ""){
                                                ?>
                                                <a href="tickets.php?id=<?php echo $T['ticket_id']; ?>">
                                                    <?php echo $subject; ?>
                                                </a>
                                                <?php
                                                    }else{
                                                ?>
                                                    <a class="conflictoTicket" nombreagente="<?php echo $nombreagente; ?>">
                                                    <?php echo $subject; ?>
                                                </a>  
                                                <?php
                                                
                                                    }
                                                ?>
											   
											   
											   </div>
											<?php               if ($T['attachment_count'])
											   echo '<i class="small icon-paperclip icon-flip-horizontal" data-toggle="tooltip" title="'
												.$T['attachment_count'].'"></i>';
											   if ($threadcount > 1) { ?>
											<?php } ?>
										 </div>
									  </td>
								   </tr>
								</table>

							  </td>
							</tr>
						  </tbody>
						</table>  
					</td>	
					
		<!-- Table Date -->
					<td class="table-date" nowrap>
						<div class="nowrap">
							<div class="due-date">
								<?php echo Format::date($T[$date_col ?: 'lastupdate']) ?: $date_fallback; ?>
							</div>
							<div class="due-time">
								<?php echo Format::time($T[$date_col ?: 'lastupdate']) ?: $date_fallback; ?>
							</div>		
						</div>
					</td>

		<!-- Table Client -->				
					<td class="table-client" nowrap><?php
						if ($T['collab_count']){
                            $ticket=Ticket::lookup($T['ticket_id']);
                        $thread = $ticket->getThread();
                        $collabs=$thread->getCollaborators();
                        $colaboradores = "";
                        $coma = '';
                        foreach($collabs as $collab) {
                            $colaboradores = $colaboradores.$coma.$collab->getEmail();
                            $coma = ',&nbsp;&#10;';
                        }
                        echo '<span class="pull-right faded-more" data-toggle="tooltip" title="'
								.$colaboradores.'"><i class="icon-group"></i></span>';
                        }
							
                        ?>
                        <?php 
                            if($nombreagente == ""){
                        ?>
						<span class="truncate" style="max-width:<?php
							echo $T['collab_count'] ? '150px' : '170px'; ?>"><a href="tickets.php?id=<?php echo $T['ticket_id']; ?>"><?php
						$un = new UsersName($T['user__name']);
							echo Format::htmlchars($un);
						?></a></span>
                        <?php
                            }else{
                        ?>
                        <span class="truncate conflictoTicket" nombreagente="<?php echo $nombreagente; ?>" style="max-width:<?php
							echo $T['collab_count'] ? '150px' : '170px'; ?>"><a><?php
						$un = new UsersName($T['user__name']);
							echo Format::htmlchars($un);
						?></a></span>
                        <?php
                        
                            }
                        ?>
					</td>

		<!-- Table Description -->
					<td class="table-description"><div style="max-width: <?php
						$base = 279;
						$base = 280;
						// Make room for the paperclip and some extra
						if ($T['attachment_count']) $base -= 18;
						// Assume about 8px per digit character
						if ($threadcount > 1) $base -= 20 + ((int) log($threadcount, 10) + 1) * 8;
						// Make room for overdue flag and friends
						if ($flag) $base -= 20;
						echo $base; ?>px; max-height: 1.2em"
						class="<?php if ($flag) { ?>Icon <?php echo $flag; ?>Ticket <?php } ?>link truncate"
						<?php if ($flag) { ?> title="<?php echo ucfirst($flag); ?> Ticket" <?php } ?>>
                        <?php 
                            if($nombreagente == ""){
                        ?>
						<a href="tickets.php?id=<?php echo $T['ticket_id']; ?>">
							<?php echo $subject; ?>
                        </a>
                        <?php
                            }else{
                        ?>
                              <a class="conflictoTicket" nombreagente="<?php echo $nombreagente; ?>">
							<?php echo $subject; ?>
                        </a>  
                        <?php
                        
                            }
                        ?>
						
						</div>
						<?php 
						if ($T['attachment_count'])
							echo '<i class="small icon-paperclip icon-flip-horizontal" data-toggle="tooltip" title="'
								.$T['attachment_count'].'"></i>';
						if ($threadcount > 1) { ?>
							<div id="thread-icon"><i class="icon-comments-alt"></i></div>
								<div id="thread-count"><small><?php echo $threadcount; ?></small></div>
						<?php } ?>
					</td>				

					<?php
					if($search && !$status){
						$displaystatus=TicketStatus::getLocalById($T['status_id'], 'value', $T['status__name']);
						if(!strcasecmp($T['status__state'],'open'))
							$displaystatus="<b>$displaystatus</b>";
						echo "<td></td>";
					} else { ?>


		<!-- Table Closed By --> <!-- OR -->    
		<!-- Table Assigned To --> <!-- OR -->   
		<!-- Table Department -->                  
		<!-- Table Status -->
					<?php
					}
					?>
					<td class="table-status" nowrap><span class="truncate" style="max-width: 169px"><?php
						echo Format::htmlchars($lc); ?></span></td>


		<!-- Table ID -->
                    <td class="table-id" title="<?php echo $T['user__default_email__address']; ?>" nowrap>
                        <?php 
                            if($nombreagente == ""){
                            ?>
                                <a class="Icon <?php echo strtolower($T['source']); ?>Ticket preview"
                                title="Preview Ticket"
                                href="tickets.php?id=<?php echo $T['ticket_id'].$statusLista; ?>"
                                data-preview="#tickets/<?php echo $T['ticket_id']; ?>/preview"
                                ><?php echo $tid; ?></a>
                            <?php
                            }else{
                            ?>
                                
                                <span class="Icon <?php echo strtolower($T['source']); ?>Ticket preview conflictoTicket"
                                title="Preview Ticket" 
                                style="cursor:pointer;color: #128dbe;"
                                nombreagente="<?php echo $nombreagente; ?>"
                                href="tickets.php?id=<?php echo $T['ticket_id'].$statusLista; ?>"
                                data-preview="#tickets/<?php echo $T['ticket_id']; ?>/preview"
                                ><?php echo $tid; ?></span>
                            <?php
                            }
                        ?>
					</td>					

				</tr>
				<tr class="mobile-only-bottom-spacer">
					<td colspan="3"></td>
                </tr>
                <?php
                $ticketprew=Ticket::lookup($T['ticket_id']);
                $tcount = $ticketprew->getThreadEntries();
                ?>
                <?php 
                $i=0;
                foreach ($tcount as $EN){
                    if($i == 0){
                        $lineas = $EN->getBody();
                        $i = 1;
                    }
                }

                $allowed_tags = array("html", "body", "b", "br", "em", "hr", "i", "li", "ol", "p", "s", "span", "table", "tr", "td", "u", "ul","div");
                $descripcion = "";
                foreach($allowed_tags as $tag ){
                    $descripcion = strip_tags($lineas, $tag);
                }
                $linea1 = substr($descripcion, 0, 300);
                // $linea2 = substr($descripcion, 101, 202);
                    if(($thisstaff->getDefaultPreviewTicket() == 1) || ($thisstaff->getDefaultPreviewTicket() == 2)){
                        if($thisstaff->getDefaultPreviewTicket() == 1){
                        $selected = "preview_1 preview-line-hide";
                        }
                        if($thisstaff->getDefaultPreviewTicket() == 2){
                        $selected = "preview_1 preview-line-show";
                        } 
                    }else{
                        $selected = "preview_1 preview-line-hide";
                    }
                
                ?>
                <tr id="<?php echo $T['ticket_id']; ?>">
                <td colspan="7" class="table-date td_ticket<?php echo $T['ticket_id']; ?>" nowrap >  
                <div class="<?php echo $selected; ?>" style="width: 891px;"><?php echo $linea1; ?></div>
                </td>
                </tr>
				<?php
				} //end of foreach
			if (!$total)
				$ferror=__('There are no tickets matching your criteria.');
			?>
		</tbody>
		<tfoot>
		 <tr>
			<td colspan="8">
				<?php if($total && $thisstaff->canManageTickets()){ ?>
				<?php echo __('Select');?>&nbsp;
				<a id="selectAll" href="#ckb"><?php echo __('All');?></a>&nbsp;&nbsp;
				<a id="selectNone" href="#ckb"><?php echo __('None');?></a>&nbsp;&nbsp;
				<a id="selectToggle" href="#ckb"><?php echo __('Toggle');?></a>&nbsp;&nbsp;
				<?php }else{
					echo '<i>';
					echo $ferror?Format::htmlchars($ferror):__('Query returned 0 results.');
					echo '</i>';
				} ?>
			</td>
		 </tr>
		</tfoot>
		</table>
		<?php
		if ($total>0) { //if we actually had any tickets returned.
	?>      <div id="table-foot-options">
				<span class="faded pull-right"><?php echo $pageNav->showing(); ?></span>
	<?php
			echo __('Page').':'.$pageNav->getPageLinks().'&nbsp;';
			echo sprintf('<a class="export-csv no-pjax" href="?%s">%s</a>',
					Http::build_query(array(
							'a' => 'export', 'h' => $hash,
							'status' => $_REQUEST['status'])),
					__('Export'));
			echo '&nbsp;<i class="help-tip icon-question-sign" href="#export"></i></div>';
		} ?>
		</form>
	</div>

</div>



<div style="display:none;" class="dialog" id="confirm-action">
    <h3><?php echo __('Please Confirm');?></h3>
    <a class="close" href=""><i class="material-icons">highlight_off</i></a>
    <hr/>
    <p class="confirm-action" style="display:none;" id="mark_overdue-confirm">
        <?php echo __('Are you sure you want to flag the selected tickets as <font color="red"><b>overdue</b></font>?');?>
    </p>
    <div><?php echo __('&nbsp;');?></div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons pull-left">
            <input type="button" value="<?php echo __('No, Cancel');?>" class="close">
        </span>
        <span class="buttons pull-right">
            <input type="button" value="<?php echo __('Yes, Do it!');?>" class="confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>


<div class="dialog" id="alert2" style="top: 75.2857px;
    left: calc((100%/2) - 250px) !important;display: none;">
    <h3><span id="title">Conflicto de tramitación de ticket</span></h3>
    <a class="close" href=""><i class="icon-remove-circle"></i></a>
    <hr>
    <div id="body" style="min-height: 20px;">El ticket selecciónado ya está siendo tramitado por el agente <a id="nombreagente"></a><br>
No es posible que dos agentes realicen operaciones sobre un mismo ticket de forma simultánea. Para más información, contacte con dicho agente</div>
    <hr style="margin-top:3em">
    <p class="full-width">
        <span class="buttons pull-right">
            <input type="button" value="ACEPTAR" class="close ok">
        </span>
     </p>
    <div class="clear"></div>
</div>
<script type="text/javascript">

$('.tr_ticket_es').hover(function() {
    id = $(this).attr('id');
    //console.log(id);
    $('.td_ticket'+id).css('background-color', '#fbf0e4 !important');
    }, function() {
    // vuelve a dejar el <div> como estaba al hacer el "mouseout"
    $('.td_ticket'+id).css('background-color', '');
    });
$('.conflictoTicket').click(function(){
    var $input = $( this );
    nombre = $input.attr('nombreagente');
    $('#nombreagente').text(nombre);
    $('.dialog#alert2').css({"top": "75.2857px" , "left": "470px"});
    $('.dialog#alert2').show();


});
</script>