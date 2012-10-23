<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title><?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/media/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      html, body { height: 100% }
      body {
        padding-top: 41px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>
    <link href="/media/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/media/css/notes.css" rel="stylesheet">
    <script src="/media/js/jquery.js"></script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="http://twitter.github.com/bootstrap/assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="http://twitter.github.com/bootstrap/assets/ico/apple-touch-icon-57-precomposed.png">
  </head>

  <body>

	<?php echo Component_Display::Navbar(); ?>

	<div id="wrapper">
		<?php echo Component_Display::Sidebar(); ?>

		<div id="content" class="clearfix" style="margin-left: 213px;">
			<div class="contentwrapper">
				<div class="heading">
					<h3><?php if(!empty($header)) echo $header; ?></h3>
				</div>
				<div class="row-fluid">
					<div class="span12">
<?php
// output messages
 echo Message::output();
 echo $content ?>
					</div>
				</div>
			</div>
		</div>
	</div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/media/js/bootstrap-transition.js"></script>
    <script src="/media/js/bootstrap-alert.js"></script>
    <script src="/media/js/bootstrap-modal.js"></script>
    <script src="/media/js/bootstrap-dropdown.js"></script>
    <script src="/media/js/bootstrap-scrollspy.js"></script>
    <script src="/media/js/bootstrap-tab.js"></script>
    <script src="/media/js/bootstrap-tooltip.js"></script>
    <script src="/media/js/bootstrap-popover.js"></script>
    <script src="/media/js/bootstrap-button.js"></script>
    <script src="/media/js/bootstrap-collapse.js"></script>
    <script src="/media/js/bootstrap-carousel.js"></script>
    <script src="/media/js/bootstrap-typeahead.js"></script>
    <script src="/media/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="/media/js/jquery.hoverIntent.minified.js"></script>
    <script src="/media/js/jquery.cookie.js"></script>

<script>
$(document).ready(function () {
	$('#nav').dcAccordion({
		eventType: 'click',
		autoClose: false,
		saveState: true,
		disableLink: false,
		showCount: true,
		speed: 'slow'
	});
	$('.menu-dropdown-button .drop').click(function(event){
		event.preventDefault();
		$('#sidebar .sidenav').slideToggle();
	});
	$(window).resize(function(){
		if($(window).width() > 980)
			$('#sidebar .sidenav').show();
	});
	$('.dcjq-count').click(function(event){
		event.preventDefault();
	});
});
</script>

</body></html>