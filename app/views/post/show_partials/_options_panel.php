<div class="clearfix" >
  <ul>
	<li class="wiki"><?php if ($this->post->image() && $this->post->can_be_seen_by(current_user())) : ?>
			  <?php $file_jpeg = $this->post->get_file_jpeg() ?>
			  <li><?php if (array_key_exists("dakimakura", $this->post->tags()) && !current_user()->is_contributor_or_higher()) :
							  $file_sample = $this->post->get_file_sample(current_user());
							  echo $this->linkTo(($this->post->has_sample() ? $this->t('.download.larger') : $this->t('.download.normal')) . ' ' . $this->numberToHumanSize($file_sample['size']), $file_sample['url'], array(
							  'class' => $this->post->has_sample() ? "original-file-changed":"original-file-unchanged",
							  'id' => 'highres'));
					  else:
							  echo $this->linkTo(($this->post->has_sample() ? $this->t('.download.larger') : $this->t('.download.image')) . ' (' . $this->numberToHumanSize($file_jpeg['size']) . ')', $file_jpeg['url'], array(
							  'class' => ($this->post->has_sample() ? "original-file-changed":"original-file-unchanged"),
							  'id' => 'highres'));
					  endif
				  ?>
			  </li>
			  <?php if ($this->post->has_jpeg()) : ?>
				<?php $file_image = $this->post->get_file_image() ?>
				<?php # If we have a JPEG, the above link was the JPEG.  Link to the PNG here. ?>
				<li><?= $this->linkTo($this->t('.download.normal').' '.strtoupper($file_image['ext']).' ('.$this->numberToHumanSize($file_image['size']).')', $file_image['url'], array(
								'class' => 'original-file-unchanged',
								'id' => 'png'));
						?>
				</li>
			  <?php endif ?>
			<?php endif ?>
		<?= $this->linkTo('Options ■', '#', ['class' => 'submenu-button']) ?>
		<ul class="submenu" style="display: none; margin-left: 0em; margin-top: .2em; width: 115px; padding: 0.3em; border: 1px solid #bf5e65;">
			<li><?= $this->linkToFunction($this->t('buttons.edit'), "$('comments').hide(); $('edit').show().scrollTo(); $('post_tags').focus(); Cookie.put('show_defaults_to_edit', 1);") ?></li>
			<!-- <?php //if (!$this->post->is_deleted() && $this->post->image() && $this->post->width && $this->post->width > 700) : ?>
			  <li><?php //echo $this->linkToFunction($this->t('.resize'), "post->resize_image()") ?></li>
			<?php //endif ?> -->
			<?php if ($this->post->image() && $this->post->can_be_seen_by(current_user())) : ?>
			  <?php if ($this->post->has_jpeg()) : ?>
				<?php $file_image = $this->post->get_file_image() ?>
				<?php # If we have a JPEG, the above link was the JPEG.  Link to the PNG here. ?>
				<li><?= $this->linkTo($this->t('.download.normal').' '.strtoupper($file_image['ext']).' ('.$this->numberToHumanSize($file_image['size']).')', $file_image['url'], array(
								'class' => 'original-file-unchanged',
								'id' => 'png'));
						?>
				</li>
			  <?php endif ?>
			<?php endif ?>
			<?php if ($this->post->can_user_delete(current_user())) : ?>
			<li><?= $this->linkTo($this->t('.delete'), array('#delete', 'id' => $this->post->id)) ?></li>
			<?php endif ?>
			<?php if ($this->post->is_deleted() && current_user()->is_janitor_or_higher()) : ?>
			  <li><?= $this->linkTo($this->t('.undelete'), array('#undelete', 'id' => $this->post->id)) ?></li>
			<?php endif ?>
			<?php if (!$this->post->is_flagged() && !$this->post->is_deleted()) : ?>
			  <li><?= $this->linkToFunction($this->t('.flag'), "Post.flag(".$this->post->id.", function() { window.location.reload(); })", array('level' => 'member')) ?></li>
			<?php endif ?>
			<?php if (!$this->post->is_deleted() && $this->post->image() && !$this->post->is_note_locked()) : ?>
			  <?php if (CONFIG()->disable_old_note_creation) : ?>
			  <li style="position:relative">
				<div id="note_create_notice"><?= $this->t(['.notes_create_notice', 'notes_help' => $this->linkTo('Notes help', 'help#notes'), 'close' => $this->linkToFunction('close', 'Note.toggleCreateNotice()')]) ?></div>
				<?= $this->linkToFunction($this->t('.add_notes'), "Note.toggleCreateNotice()", array('level' => 'member')) ?>
			  </li>
			  <?php else: ?>
			  <li><?= $this->linkToFunction($this->t('.add_notes'), "Note.create(".$this->post->id.")", array('level' => 'member')) ?></li>
			  <?php endif ?>
			<?php endif ?>
			<li id="add-to-favs"><?= $this->linkToFunction($this->t('.favorites.add'), "Post.vote(".$this->post->id.", 3); return false") ?></li>
			<li id="remove-from-favs"><?= $this->linkToFunction($this->t('.favorites.remove'), "Post.vote(".$this->post->id.", 0); return false") ?></li>
			<?php if ($this->post->is_pending() && current_user()->is_janitor_or_higher()) : ?>
			  <li><?= $this->linkToFunction($this->t('.approve._'), "if (confirm('".$this->t('.approve.confirm')."')) {Post.approve(".$this->post->id.")}") ?></li>
			<?php endif ?>
			<?php if (!$this->post->is_deleted()) : ?>
			  <li id="add-to-pool" class="advanced-editing"><a href="#" onclick="new Ajax.Updater('add-to-pool', '/pool/select?post_id=<?= $this->post->id ?>', {asynchronous:true, evalScripts:true, method:'get'}); return false;"><?= $this->t('.add_to_pool') ?></a></li>
			<?php endif ?>
			<?php if (!$this->post->is_deleted()) : ?>
			  <li id="set-avatar"><?= $this->linkTo($this->t('.set_avatar'), array('user#set_avatar', 'id' => $this->post->id)) ?></li>
			<?php endif ?>
			<li><?= $this->linkTo($this->t('.history'), array('history#index', 'search' => 'post:'.$this->post->id)) ?></li>
		</ul>
	</li>
  </ul>
</div>
