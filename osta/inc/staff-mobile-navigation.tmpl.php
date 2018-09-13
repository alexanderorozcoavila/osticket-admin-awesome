<ul id="nav-mobile" class="flush-left">
	<li class="inactive "><a href="#" class="submenu-button"><?php echo __('Tickets') ?></a>
		<ul class="submenu">
			<li><a class="newTicket sub" href="tickets.php?a=open" title="Open a New Ticket" id="new-ticket"><?php echo __('New Ticket') ?></a>
			</li>
			<li><a class="Ticket active sub" href="tickets.php?status=open" title="Open Tickets" id="subnav3"><?php echo __('Open') ?></a>
			</li>
			<li><a class="answeredTickets sub" href="tickets.php?status=answered" title="Answered Tickets" id="subnav4"><?php echo __('Answered') ?></a>
			</li>
			<li><a class="assignedTickets sub" href="tickets.php?status=assigned" title="Assigned Tickets" id="subnav5"><?php echo __('My Tickets') ?></a>
			</li>
			<li><a class="overdueTickets sub" href="tickets.php?status=overdue" title="Stale Tickets" id="subnav6"><?php echo __('Overdue') ?></a>
			</li>
			<li><a class="closedTickets sub" href="tickets.php?status=closed" title="Closed Tickets" id="subnav7"><?php echo __('Closed') ?></a>
			</li>
		</ul>
	</li>
	<li class="inactive "><a href="#" class="submenu-button"><?php echo __('Users') ?></a>
		<ul class="submenu">
			<li><a class="teams" href="users.php" title="" id="nav0"><?php echo __('User Directory') ?></a>
			</li>
			<li><a class="departments" href="orgs.php" title="" id="nav1"><?php echo __('Organizations') ?></a>
			</li>
		</ul>
	</li>
	<li class="inactive "><a href="#" class="submenu-button"><?php echo __('Tasks') ?></a>
		<ul class="submenu">
			<li><a class="open" href="tasks.php?status=open" title="" id="nav0"><?php echo __('Open Tasks') ?></a>
			</li>
			<li><a class="completed" href="tasks.php?status=closed" title="" id="nav1"><?php echo __('Completed Tasks') ?></a>
			</li>	
		</ul>
	</li>
	<li class="inactive "><a href="#" class="submenu-button"><?php echo __('Knowledgebase') ?></a>
		<ul class="submenu">
			<li><a class="kb" href="kb.php" title="" id="nav0"><?php echo __('FAQs') ?></a>
			</li>
			<li><a class="faq-categories" href="categories.php" title="" id="nav1"><?php echo __('Categories') ?></a>
			</li>
			<li><a class="canned" href="canned.php" title="" id="nav2"><?php echo __('Canned Responses') ?></a>
			</li>
		</ul>
	</li>
	<li><a href="#" class="submenu-button"><?php echo __('Dashboard') ?></a>
		<ul class="submenu">
			<li><a href="dashboard.php"><?php echo __('Dashboard') ?></a>
			</li>
			<li><a href="directory.php"><?php echo __('Agent Directory') ?></a>
			</li>
			<li><a href="profile.php"><?php echo __('My Profile') ?></a>
			</li>
		</ul>
	</li>
	<li id="welcome"><a href="#" class="submenu-button"><?php echo sprintf(__('Welcome, %s'), '<strong>'.$thisstaff->getFirstName().'</strong>'); ?></a>
		<ul class="submenu">
			<li><a href="profile.php"><?php echo __('Your Profile') ?></a>
			</li>
			<li>
				<a href="logout.php?auth=<?php echo $ost->getLinkToken(); ?>" class="no-pjax">
					<?php echo __( 'Log Out'); ?>
				</a>
			</li>
		</ul>
	</li>
	<li id="contact-id">
		<a href="
			<?php				
			$file_name = ROOT_DIR ."osta/css/themes/mobile-link.txt";
				echo file_get_contents($file_name);
			?> 
		">
			<?php				
			$file_name = ROOT_DIR ."osta/css/themes/mobile-text.txt";
				echo file_get_contents($file_name);
			?>  
		</a>
	</li>	
</ul>
