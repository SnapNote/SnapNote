<form action="/notes/edit/<?php if(!empty($note['id'])) echo $note['id']; ?>" class="form-horizontal" method="post">
    <div class="control-group">
	    <label class="control-label" for="inputSubject">Subject</label>
	    <div class="controls">
		    <input type="text" name="subject" id="inputSubject" placeholder="Enter Subject..." class="span12" value="<?php if(!empty($note['active_version']['subject'])) echo $note['active_version']['subject']; ?>">
	    </div>
	</div>
    <div class="control-group">
	    <label class="control-label" for="inputNote">Note</label>
	    <div class="controls">
		    <textarea name="note" placeholder="Enter Note..." id="inputNote" rows="5" class="span12"><?php if(!empty($note['active_version']['note'])) echo $note['active_version']['note']; ?></textarea>
	    </div>
	</div>
	<div class="control-group">
		<div class="controls">
    		<input type="submit" class="btn btn-primary" value="Save" /><?php if(!empty($note)): ?> <a href="/notes/view/<?php echo $note['id']; ?>" class="btn">Cancel</a><?php endif; ?>
    	</div>
	</div>
</form>

<?php if(!empty($note)): ?>
<form action="/notes/edit/<?php if(!empty($note['id'])) echo $note['id']; ?>" class="form-horizontal" method="post">
	<input type="hidden" name="action" value="addLabel" />
    <div class="control-group">
	    <label class="control-label">Labels</label>
	    <div class="controls">
	    	<div class="btn-toolbar">
	    	<?php foreach($note['labels'] as $label): ?>
	    		<div class="btn-group">
				    <a class="btn btn-mini" href="/notes/label/<?php echo $label['id']; ?>">
				    <?php echo $label['name']; ?></a>
					<a class="btn btn-mini dropdown-toggle" href="/notes/edit/<?php echo $note['id']; ?>?remove_label=<?php echo $label['id']; ?>">
						<span class="icon-remove"></span>
					</a>
			    </div>
	    	<?php endforeach; ?>
	    	</div>
	    </div>
	</div>
    <div class="control-group">
	    <label class="control-label" for="inputLabel">Add Label</label>
	    <div class="controls">
		    <select name="label_id" id="inputLabel">
		    	<option value="">Select a Label...</option>
		    	<?php foreach($master_labels as $label): ?>
		    		<option value="<?php echo $label['id']; ?>"><?php echo $label['name']; ?></option>
		    	<?php endforeach; ?>
		    	<?php foreach($user_labels as $label): ?>
			    	<option value="<?php echo $label['id']; ?>"><?php echo $label['name']; ?></option>
			    	<?php if(!empty($label['children'])): foreach($label['children'] as $sublabel): ?>
				    	<option value="<?php echo $sublabel['id']; ?>">&nbsp;&nbsp;&nbsp;/&nbsp;<?php echo $sublabel['name']; ?></option>
				    	<?php if(!empty($sublabel['children'])): foreach($sublabel['children'] as $subsublabel): ?>
					    	<option value="<?php echo $subsublabel['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;<?php echo $subsublabel['name']; ?></option>
					    	<?php if(!empty($subsublabel['children'])): foreach($subsublabel['children'] as $subsubsublabel): ?>
					    		<option value="<?php echo $subsubsublabel['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;<?php echo $subsubsublabel['name']; ?></option>
					    	<?php endforeach; endif; ?>
				    	<?php endforeach; endif; ?>
			    	<?php endforeach; endif; ?>
		    	<?php endforeach; ?>
		    </select>
	    </div>
	</div>
	<div class="control-group">
		<div class="controls">
    		<input type="submit" class="btn" value="Add Label" />
    	</div>
	</div>
</form>
<?php endif; ?>
