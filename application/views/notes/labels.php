<ul>
<?php foreach($user_labels as $label): ?>
	<li>
		<?php echo $label['name']; ?>
		<span class="label-actions">
			<a title="New Sub Label" href="#" class="show-add-form" style="display: none;"><i class="icon-plus"></i></a>
			<a title="Move Up" href="/notes/labels?id=<?php echo $label['id']; ?>&sort_order=<?php echo ($label['sort_order']-1); ?>"><i class="icon-arrow-up"></i></a>
			<a title="Move Down" href="/notes/labels?id=<?php echo $label['id']; ?>&sort_order=<?php echo ($label['sort_order']+1); ?>"><i class="icon-arrow-down"></i></a>
			<a title="Delete" href="/notes/labels?delete_id=<?php echo $label['id']; ?>"><i class="icon-remove"></i></a>
		</span>
		<?php if(!empty($label['children'])): ?>
		<ul>
			<?php foreach($label['children'] as $sublabel): ?>
			<li>
				<?php echo $sublabel['name']; ?>
				<span class="label-actions">
					<a title="New Sub Label" href="#" class="show-add-form" style="display: none;"><i class="icon-plus"></i></a>
					<a title="Move Up"  href="/notes/labels?id=<?php echo $sublabel['id']; ?>&sort_order=<?php echo ($sublabel['sort_order']-1); ?>"><i class="icon-arrow-up"></i></a>
					<a title="Move Down" href="/notes/labels?id=<?php echo $sublabel['id']; ?>&sort_order=<?php echo ($sublabel['sort_order']+1); ?>"><i class="icon-arrow-down"></i></a>
					<a title="Delete" href="/notes/labels?delete_id=<?php echo $sublabel['id']; ?>"><i class="icon-remove"></i></a>
				</span>
				<?php if(!empty($sublabel['children'])): ?>
				<ul>
					<?php foreach($sublabel['children'] as $subsublabel): ?>
					<li>
						<?php echo $subsublabel['name']; ?>
						<span class="label-actions">
							<a title="New Sub Label" href="#" class="show-add-form" style="display: none;"><i class="icon-plus"></i></a>
							<a title="Move Up"  href="/notes/labels?id=<?php echo $subsublabel['id']; ?>&sort_order=<?php echo ($subsublabel['sort_order']-1); ?>"><i class="icon-arrow-up"></i></a>
							<a title="Move Down" href="/notes/labels?id=<?php echo $subsublabel['id']; ?>&sort_order=<?php echo ($subsublabel['sort_order']+1); ?>"><i class="icon-arrow-down"></i></a>
							<a title="Delete" href="/notes/labels?delete_id=<?php echo $subsublabel['id']; ?>"><i class="icon-remove"></i></a>
						</span>
						<?php if(!empty($subsublabel['children'])): ?>
						<ul>
							<?php foreach($subsublabel['children'] as $subsubsublabel): ?>
							<li>
								<?php echo $subsubsublabel['name']; ?>
								<span class="label-actions">
									<a title="Move Up"  href="/notes/labels?id=<?php echo $subsubsublabel['id']; ?>&sort_order=<?php echo ($subsubsublabel['sort_order']-1); ?>"><i class="icon-arrow-up"></i></a>
									<a title="Move Down" href="/notes/labels?id=<?php echo $subsubsublabel['id']; ?>&sort_order=<?php echo ($subsubsublabel['sort_order']+1); ?>"><i class="icon-arrow-down"></i></a>
									<a title="Delete" href="/notes/labels?delete_id=<?php echo $subsubsublabel['id']; ?>"><i class="icon-remove"></i></a>
								</span>
							</li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
						<ul class="add-form" id="add-form-<?php echo $subsublabel['id']; ?>">
							<li>
								<form class="new-label-form" action="/notes/labels" method="post"><input type="text" class="new-label-input" name="name" /><input type="hidden" name="parent_id" value="<?php echo $subsublabel['id']; ?>" /><input type="hidden" name="action" value="addLabel" /><input type="submit" class="icon-ok new-label-submit" value=" " /></form>
							</li>
						</ul>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
				<ul class="add-form" id="add-form-<?php echo $sublabel['id']; ?>">
					<li>
						<form class="new-label-form" action="/notes/labels" method="post"><input type="text" class="new-label-input" name="name" /><input type="hidden" name="parent_id" value="<?php echo $sublabel['id']; ?>" /><input type="hidden" name="action" value="addLabel" /><input type="submit" class="icon-ok new-label-submit" value=" " /></form>
					</li>
				</ul>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<ul class="add-form" id="add-form-<?php echo $label['id']; ?>">
			<li>
				<form class="new-label-form" action="/notes/labels" method="post"><input type="text" class="new-label-input" name="name" /><input type="hidden" name="parent_id" value="<?php echo $label['id']; ?>" /><input type="hidden" name="action" value="addLabel" /><input type="submit" class="icon-ok new-label-submit" value=" " /></form>
			</li>
		</ul>
	</li>
<?php endforeach; ?>
</ul>

Add Label:
<form action="/notes/labels" method="post"><input type="text" class="new-label-input" name="name" /><input type="hidden" name="parent_id" value="0" /><input type="hidden" name="action" value="addLabel" /><input type="submit" class="icon-plus new-label-submit" value="" /></form>
<script type="text/javascript">
$(document).ready(function(){
	$('.add-form').hide();
	$('.show-add-form').show();
	$('.show-add-form').click(function(){
		$(this).parent().parent().find("> .add-form").toggle();
		return false;
	});
});
</script>
<style type="text/css">
.new-label-input {
	font-size:8pt;
	width: 80px;
	height: 10px !important;
}
.new-label-submit {
	width: 20px;
	height: 20px;
	background-color: transparent;
	border: 0;
	margin-left: 4px;
	margin-top: -4px;
}
.new-label-form {
	margin-bottom: 2px;
}
.label-actions {
	opacity: .3;
}
</style>
