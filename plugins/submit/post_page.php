<?php

/* ******* PLUGIN TEMPLATE ************************************************************************** 
 * Plugin name: Submit
 * Template name: plugins/submit/post_page.php
 * Template author: Nick Ramsay
 * Version: 0.1
 * License:
 *
 *   This file is part of Hotaru CMS (http://www.hotarucms.org/).
 *
 *   Hotaru CMS is free software: you can redistribute it and/or modify it under the terms of the 
 *   GNU General Public License as published by the Free Software Foundation, either version 3 of 
 *   the License, or (at your option) any later version.
 *
 *   Hotaru CMS is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 *   even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License along with Hotaru CMS. If not, 
 *   see http://www.gnu.org/licenses/.
 *   
 *   Copyright (C) 2009 Hotaru CMS - http://www.hotarucms.org/
 *
 **************************************************************************************************** */

global $hotaru, $plugin, $post, $userbase;
$userbase = new UserBase();

?>

<!-- ************ POST **************** -->

<div class="show_post">

	<?php $plugin->check_actions('post_page_1'); ?>
	
	<div class="show_post_title"><a href='<?php echo $post->post_orig_url; ?>'><?php echo $post->post_title; ?></a></div>

	<?php if($post->use_author || $post->use_date) { ?>
		<div class="show_post_author_date">	
			Posted
			<?php if($post->use_author) { echo " by " . $userbase->get_username($post->post_author); } ?>
			<?php if($post->use_date) { echo time_difference(unixtimestamp($post->post_date)) . " ago"; } ?>
		</div>
	<?php } ?>
		
	<?php if($post->use_content) { ?>
		<div class="show_post_content"><?php echo $post->post_content; ?></div>
	<?php } ?>
	
	<?php $plugin->check_actions('submit_post_page_extra_fields_1'); ?>
	
	<div class="show_permalink"><a href='<?php echo url(array('page'=>$post->post_id)); ?>'>Permalink</a></div>
	
	<?php $plugin->check_actions('post_page_2'); ?>
	
</div>

<!-- ************ END POST **************** -->
	
 
 