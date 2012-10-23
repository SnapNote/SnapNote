<div class="row-fluid">
	<div class="span9"><?php echo Text::auto_link(nl2br(htmlentities($note['active_version']['note']))); ?></div>
	<br style="clear:both;" class="visible-phone" />
	<div class="span3 well">
		Created: <?php echo $note['created']; ?><br />
		Labels: <?php foreach($note['labels'] as $label): ?><a class="label label-info" href="/notes/label/<?php echo $label['id']; ?>"><?php echo $label['name']; ?></a> <?php endforeach; ?><br /><br />
		<a href="/notes/edit/<?php echo $note['id']; ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i> Edit</a>
		<a href="/notes/delete/<?php echo $note['id']; ?>" class="btn btn-danger" onClick="return confirm('Are you sure you want to delete this note?');"><i class="icon-trash icon-white"></i> Delete</a>
	</div>
</div>

