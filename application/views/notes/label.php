<table class="table table-striped table-hover">
	<thead>
		<tr><th width="40">&nbsp;</th><th>Subject</th><th class="hidden-phone" width="150">Modified</th></tr>
	</thead>
	<tbody>
		<?php foreach($notes as $note_id => $note): ?>
		<tr>
			<td width="40" style="white-space: nowrap">
				<input type="checkbox" name="note_ids[]" value="<?php $note_id; ?>" />
				<?php $starred = false; if(!empty($note['labels'])) foreach($note['labels'] as $label) if($label['name'] == 'Starred') $starred = true; ?>
				<?php if($starred): ?>
					<a href="/notes/star/<?php echo $note['id']; ?>?action=unstar&redirect=<?php echo $_SERVER['REQUEST_URI']; ?>"><img src="/media/img/star-orange.png" border="0" /></a>
				<?php else: ?>
					<a href="/notes/star/<?php echo $note['id']; ?>?action=star&redirect=<?php echo $_SERVER['REQUEST_URI']; ?>"><img src="/media/img/star-clear2.png" border="0" /></a>
				<?php endif; ?>
			</td>
			<td>
				<a href="/notes/view/<?php echo $note_id; ?>"><?php echo $note['subject']; ?></a>
				<span style="margin-left: 30px;">
					<?php if(!empty($note['labels'])) foreach($note['labels'] as $label): if($label['name'] != 'Starred'): ?>
						<a class="label" href="/notes/label/<?php echo $label['id']; ?>"><?php echo $label['name']; ?></a>
					<?php endif; endforeach; ?>
				</span>
			</td>
			<td class="hidden-phone" >
				<?php echo Date::fuzzy_span(strtotime($note['modified'])); ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
