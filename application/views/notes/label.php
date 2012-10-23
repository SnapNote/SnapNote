<table class="table table-striped table-hover">
	<thead>
		<tr><th width="30">&nbsp;</th><th>Subject</th></tr>
	</thead>
	<tbody>
		<?php foreach($notes as $note_id => $note): ?>
		<tr><td width="30"><?php foreach($note['labels'] as $label): if($label['name'] == 'Starred'): ?><a class="label" href="/notes/label/<?php echo $label['id']; ?>">X</a><?php endif; endforeach; ?></td><td><a href="/notes/view/<?php echo $note_id; ?>"><?php echo $note['subject']; ?></a><span style="margin-left: 30px;"><?php foreach($note['labels'] as $label): if($label['name'] != 'Starred'): ?><a class="label" href="/notes/label/<?php echo $label['id']; ?>"><?php echo $label['name']; ?></a> <?php endif; endforeach; ?></span></td></tr>
		<?php endforeach; ?>
	</tbody>
</table>
