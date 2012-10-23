		<div id="sidebar-background" style="margin-left: 0px;" class="visible-desktop"></div>
		<div id="sidebar" style="margin-left: 0px; left: 0px;">
			<div class="searchbox" style="display:none;">
				<input type="text" style="width: 100px;" />
			</div>
			<div class="sidenav" style="">
				<div class="mainnav">
					<ul id="nav">
						<?php foreach($master_labels as $label): ?>
						<li>
							<a href="/notes/label/<?php echo $label['id']; ?>"><span><?php echo $label['name']; ?><?php if($label['count']>0): ?> <span class="badge"><?php echo $label['count']; ?></span><?php endif; ?></span></a>
						</li>
						<?php endforeach; ?>
						<li>
							<a href="/notes/archive/"><span>Archive</span></a>
						</li>
						<?php foreach($user_labels as $label): ?>
						<li>
							<a href="/notes/label/<?php echo $label['id']; ?>"><span><?php echo $label['name']; ?><?php if($label['count']>0): ?> <span class="badge"><?php echo $label['count']; ?></span><?php endif; ?></span></a>
							<?php if(!empty($label['children'])): ?>
							<ul class="sub">
								<?php foreach($label['children'] as $sublabel): ?>
								<li>
									<a href="/notes/label/<?php echo $sublabel['id']; ?>"><span><?php echo $sublabel['name']; ?><?php if($sublabel['count']>0): ?> <span class="badge" style="padding-left: 10px;"><?php echo $sublabel['count']; ?></span><?php endif; ?></span></a>
									<?php if(!empty($sublabel['children'])): ?>
									<ul class="sub">
										<?php foreach($sublabel['children'] as $subsublabel): ?>
										<li>
											<a href="/notes/label/<?php echo $subsublabel['id']; ?>"><span class="sublevel2"><?php echo $subsublabel['name']; ?><?php if($subsublabel['count']>0): ?> <span class="badge" style="padding-left: 10px;"><?php echo $subsublabel['count']; ?></span><?php endif; ?></span></a>
											<?php if(!empty($subsublabel['children'])): ?>
											<ul class="sub">
												<?php foreach($subsublabel['children'] as $subsubsublabel): ?>
												<li>
													<a href="/notes/label/<?php echo $subsubsublabel['id']; ?>"><span class="sublevel3"><?php echo $subsubsublabel['name']; ?><?php if($subsubsublabel['count']>0): ?> <span class="badge" style="padding-left: 10px;"><?php echo $subsubsublabel['count']; ?></span><?php endif; ?></span></a>
												</li>
												<?php endforeach; ?>
											</ul>
											<?php endif; ?>
										</li>
										<?php endforeach; ?>
									</ul>
									<?php endif; ?>
								</li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<div class="sidebar-widget">
				<h5 class="title">Preferences</h5>
				<div class="content">
					<a href="/notes/labels">Manage Labels</a>
				</div>
			</div>
		</div>
