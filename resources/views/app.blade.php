<!DOCTYPE HTML>
<html>
	<head>
		<title>Help People</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" />
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css" />
		<link rel="stylesheet" href="/vendor/waves/waves.min.css" />
		<link rel="stylesheet" href="/vendor/wow/animate.css" />
		<link rel="stylesheet" href="/css/nativedroid2.css" />
        @yield('styles')
		<meta name="mobile-web-app-capable" content="yes">
	 	<meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

	</head>
	<body>
		<div data-role="page">
			<!-- panel left -->
			<div data-role="panel" id="leftpanel" data-display="overlay" data-position-fixed="true" >

				<div class='nd2-sidepanel-profile wow fadeInDown'>
					<img class='profile-background' src="//lorempixel.com/400/200/abstract/2/" />
					<div class="row">
						<div class='col-xs-4 center-xs'>
							<div class='box'>
								<img class="profile-thumbnail" src="//lorempixel.com/200/200/people/9/" />
							</div>
						</div>
						<div class='col-xs-8'>
							<div class='box profile-text'>
								<strong>Jane Doe</strong>
								<span class='subline'>Super Power User</span>
							</div>
						</div>
					</div>
				</div>


				<ul data-role="listview" data-inset="false">
					<li data-role="list-divider">Elements</li>
				</ul>
				<div data-role="collapsible" data-inset="false"  data-collapsed-icon="carat-d" data-expanded-icon="carat-d" data-iconpos="right">
					<h3>Basic Elements</h3>
					<ul data-role="listview" data-inset="false" data-icon="false">
						<li><a href="/examples/elements/text.html" data-ajax='false' data-icon="false">Text Elements</a></li>
						<li><a href="/examples/elements/grid.html" data-ajax='false' data-icon="false">FlexboxGrid</a></li>
						<li><a href="/examples/elements/buttons.html" data-ajax='false' data-icon="false">Buttons</a></li>
						<li><a href="/examples/elements/header_footer.html" data-ajax='false'>Header &amp; Footer</a></li>
						<li><a href="/examples/elements/listviews.html" data-ajax='false'>Listviews</a></li>
						<li><a href="/examples/elements/forms.html" data-ajax='false'>Forms</a></li>
						<li><a href="/examples/elements/tables.html" data-ajax='false'>Tables</a></li>
						<li><a href="/examples/elements/dialog_popups.html" data-ajax='false'>Dialog &amp; Popup</a></li>
						<li><a href="/examples/elements/panels.html" data-ajax='false'>Panels</a></li>
						<li><a href="/examples/elements/autocomplete.html" data-ajax='false'>Autocomplete</a></li>
						<li><a href="/examples/elements/collapsible_accordions.html" data-ajax='false'>Collapsible &amp; Accordions</a></li>
					</ul>
				</div>
				<div data-role="collapsible" data-inset="false" data-collapsed-icon="carat-d" data-expanded-icon="carat-d" data-iconpos="right">
					<h3>Extended Elements</h3>
					<ul data-role="listview" data-icon="false">
						<li><a href="/examples/extended/tabs.html" data-ajax='false'>Tabs</a></li>
						<li><a href="/examples/extended/cards.html" data-ajax='false'>Cards</a></li>
						<li><a href="/examples/extended/search.html" data-ajax='false'>Search</a></li>
						<li><a href="/examples/extended/icons.html" data-ajax='false'>Icons</a></li>
						<li><a href="/examples/extended/charts.html" data-ajax='false'>Charts</a></li>
						<li><a href="/examples/extended/toasts.html" data-ajax='false'>Toasts</a></li>
						<li><a href="/examples/extended/bottomsheet.html" data-ajax='false'>Bottom Sheets</a></li>
					</ul>
				</div>
				<ul data-role="listview" data-inset="false">
					<li data-role="list-divider">Layouts</li>
				</ul>
				<div data-role="collapsible" data-inset="false" data-collapsed-icon="carat-d" data-expanded-icon="carat-d" data-iconpos="right">
					<h3>Real-World-Examples</h3>
					<ul data-role="listview" data-icon="false">
						<li><a href="/examples/pages/profile.html" class="ui-disabled" data-ajax='false'>Profile</a></li>
						<li><a href="/examples/pages/dashboard.html" class="ui-disabled" data-ajax='false'>Dashboard</a></li>
						<li><a href="/examples/pages/gallery.html" class="ui-disabled" data-ajax='false'>Gallery</a></li>
						<li><a href="/examples/pages/chat.html" class="ui-disabled" data-ajax='false'>Chat</a></li>
						<li><a href="/examples/pages/mail_inbox.html" class="ui-disabled" data-ajax='false'>E-Mail Inbox</a></li>
						<li><a href="/examples/pages/team.html" class="ui-disabled" data-ajax='false'>Team</a></li>
						<li><a href="/examples/pages/products_services.html" class="ui-disabled" data-ajax='false'>Products &amp; Services</a></li>
						<li><a href="/examples/pages/blog.html" class="ui-disabled" data-ajax='false'>Blog</a></li>
						<li><a href="/examples/pages/blogpost.html" class="ui-disabled" data-ajax='false'>Blogpost</a></li>
					</ul>
				</div>
				<hr class="inset">
				<ul data-role="listview" data-inset="false">
					<li data-role="list-divider">Information</li>
				</ul>
				<div data-role="collapsible" data-inset="false" data-collapsed-icon="carat-d" data-expanded-icon="carat-d" data-iconpos="right">
					<h3>Nice to Know</h3>
					<ul data-role="listview" data-icon="false">
						<li><a href="/info/colors_and_styles.html" data-ajax='false'>Colors &amp; Styles</a></li>
						<li><a href="/info/credits.html" data-ajax='false'>Credits &amp; License</a></li>
					</ul>
				</div>
			</div>
			<!-- /panel left -->

			<div data-role="header" data-position="fixed" class="wow fadeInDown" data-wow-delay="0.2s">
				<a href="#bottomsheet" class="ui-btn ui-btn-right wow fadeIn" data-wow-delay='1.2s'><i class="zmdi zmdi-more-vert"></i></a>
				<a href="#leftpanel" class="ui-btn ui-btn-left wow fadeIn" data-wow-delay='0.8s'><i class="zmdi zmdi-menu"></i></a>
				<h1 class="wow fadeIn" data-wow-delay='0.4s'>Page Title</h1>
			</div>

			<div role="main" class="ui-content" data-inset="false">

			</div>
		</div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>
		<script src="/vendor/waves/waves.min.js"></script>
		<script src="/vendor/wow/wow.min.js"></script>
		<script src="/js/nativedroid2.js"></script>
		<script src="/nd2settings.js"></script>
        <script src="/js/ie10-viewport-bug-workaround.js"></script>
        <script src="/js/core.js"></script>
        <script>
            if(window.location.pathname == '/login' || window.location.pathname == '/register'){

            }else{
                if(!localStorage.getItem('token'))
                    window.location = "/login";
                else{
                    if(Auth.check()){
                        App.prepare();
                        App.start();
                    }
                }
            }
        </script>
        <script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
        <script src="/js/ws.js"></script>
        @yield('scripts')
	</body>
</html>
