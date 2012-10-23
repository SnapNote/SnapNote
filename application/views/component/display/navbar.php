    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
		  <div class="menu-dropdown-button hidden-desktop">
			<a class="drop" href="#">
				<span>Labels</span>
			</a>
		  </div>
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="/">SnapNote</a>
          <div class="nav-collapse collapse">
          	<?php if (Auth::instance()->logged_in()): ?>
            <p class="navbar-text pull-right">
              Logged in as <?php echo Html::anchor('user/profile', Auth::instance()->get_user()->username, array('class'=>'navbar-link')); ?>
            </p>
            <?php endif; ?>
            <ul class="nav">
             <?php
             if (Auth::instance()->logged_in()){
                echo '<li class="active">'.Html::anchor('/', 'Home').'</li>';
                echo '<li>'.Html::anchor('notes/edit', 'Create Note').'</li>';
                echo '<li>'.Html::anchor('user/profile', 'My Profile').'</li>';
				echo '<li class="hidden-desktop">'.Html::anchor('notes/labels', 'Manage Labels').'</li>';
				if(Auth::instance()->logged_in('admin'))
                	echo '<li>'.Html::anchor('admin_user', 'User Admin').'</li>';
                echo '<li>'.Html::anchor('user/logout', 'Log Out').'</li>';
             } else {
                echo '<li>'.Html::anchor('user/login', 'Log In').'</li>';
             }
             ?>
            </ul>
          	<?php if (Auth::instance()->logged_in()): ?>
            <form class="navbar-search pull-left">
    		<input type="text" class="search-query" placeholder="Search">
    		</form>
            <?php endif; ?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
