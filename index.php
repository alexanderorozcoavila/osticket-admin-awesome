<?php
/*********************************************************************
    index.php

    Helpdesk landing page. Please customize it to fit your needs.

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('client.inc.php');

require_once INCLUDE_DIR . 'class.page.php';

$section = 'home';
require(CLIENTINC_DIR.'header.inc.php');
?>
<div id="landing_page">
    <div class="clear"></div>


<?php

    if($cfg && ($page = $cfg->getLandingPage()))
        echo $page->getBodyWithImages();
    else
        echo  '<h1>'.__('Welcome to the Support Center').'</h1>';
    ?>

</div>
<div class="clear"></div>
	
	
<div id="open-view-boxes-desktop">
   <div id="open-view-boxes-wrapper">
      <div id="open-view-boxes-inner">
         <div id="title-row">
            <div class="open-view-open title">
               
               <h1><?php echo __('Open a New Ticket');?></h1>
               
            </div>
            <div class="open-view-view title">
               <h1><?php echo __('Check Ticket Status');?></h1>
            </div>
         </div>
         <div id="desc-row">
            <div class="open-view-open desc">
               <img align="left" src="osta/icons/ticket-open.svg">
					<?php echo __('Please provide as much detail as possible so we can best assist you. To update a previously submitted ticket, please login.');?>
            </div>
            <div class="open-view-view desc">
               <img align="left" src="osta/icons/ticket-check.svg">
					<?php echo __('We provide archives and history of all your current and past support requests complete with responses.');?>
            </div>
         </div>
         <div id="button-row">
            <div class="open-view-open buttons">
               <a class="button" href="open.php"><?php echo __('Open a New Ticket');?></a>
            </div>
            <div class="open-view-view buttons">
               <a class="button" href="view.php"><?php echo __('Check Ticket Status');?></a>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="open-view-boxes-mobile">
   <div id="open-view-boxes-wrapper">
      <div id="open-view-boxes-inner">
         <div id="title-row">
            <div class="open-view-open title">
               <h1><?php echo __('Open a New Ticket');?></h1>
            </div>
         </div>
         <div id="desc-row">
            <div class="open-view-open desc">
            	<img align="left" src="osta/icons/ticket-open.svg">
				<?php echo __('Please provide as much detail as possible so we can best assist you. To update a previously submitted ticket, please login.');?>
            </div>
         </div>
         <div id="button-row">
            <div class="open-view-open buttons">
               <a class="button" href="open.php"><?php echo __('Open a New Ticket');?></a>
            </div>
         </div>

         <div id="title-row">
            <div class="open-view-view title">
               <h1><?php echo __('Check Ticket Status');?></h1>
            </div>
         </div>
         <div id="desc-row">
            <div class="open-view-view desc">
            	<img align="left" src="osta/icons/ticket-check.svg">
				<?php echo __('We provide archives and history of all your current and past support requests complete with responses.');?>
            </div>
         </div>
         <div id="button-row">
            <div class="open-view-view buttons">
               <a class="button" href="view.php"><?php echo __('Check Ticket Status');?></a>
            </div>
         </div>
        
      </div>
   </div>
</div>	

	<div id="more-options">
		<div>
			<?php
				if($cfg && $cfg->isKnowledgebaseEnabled()){
					//FIXME: provide ability to feature or select random FAQs ??
				?>
			<br/>
			<?php
				$cats = Category::getFeatured();
				if ($cats->all()) { ?>
			<h1><?php echo __('Featured Knowledge Base Articles'); ?></h1>
			<?php
				}
					
					foreach ($cats as $C) { ?>
			<div class="featured-category front-page">
				<i class="icon-folder-open icon-2x"></i>
				<div class="category-name">
					<?php echo $C->getName(); ?>
				</div>
				<div class="featured-articles">
				<?php foreach ($C->getTopArticles() as $F) { ?>

						<div class="article-title"><a href="<?php echo ROOT_PATH;
							?>kb/faq.php?id=<?php echo $F->getId(); ?>"><?php
							echo $F->getQuestion(); ?></a>
						<div class="article-teaser"><?php echo $F->getTeaser(); ?></div>
					</div>
				<?php } ?>
				</div>	
			</div>
			<?php
				}
				}
				?>
		</div>
		<div id="information">
			<?php
				$faqs = FAQ::getFeatured()->select_related('category')->limit(5);
				if ($faqs->all()) { ?>
			<section>
				<div class="featured-questions"><?php echo __('Featured Questions'); ?></div>
				<?php   foreach ($faqs as $F) { ?>
				<div><a href="<?php echo ROOT_PATH; ?>kb/faq.php?id=<?php
					echo urlencode($F->getId());
					?>"><?php echo $F->getLocalQuestion(); ?></a></div>
				<?php   } ?>
			</section>
			<?php
				}
				$resources = Page::getActivePages()->filter(array('type'=>'other'));
				if ($resources->all()) { ?>
			<section>
				<div class="other-resources"><?php echo __('Other Resources'); ?></div>
				<?php   foreach ($resources as $page) { ?>
				<div class="resource">
					<a href="<?php echo ROOT_PATH; ?>pages/<?php echo $page->getNameAsSlug();
						?>"><?php echo $page->getLocalName(); ?></a>
				</div>
				<?php   } ?>
			</section>
			<?php
				}
					?>
		</div>
		<script>
			$('.resource').columnize();

		</script>  
	</div>
	<div class="clear"></div>
</div>
    

<?php require(CLIENTINC_DIR.'footer.inc.php'); ?>
