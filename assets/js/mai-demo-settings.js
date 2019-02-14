jQuery(document).ready(function($) {

	var $body               = $( 'body' );
	var $icon               = $( '.maids-icon' );
	var $siteContainer      = $( '.site-container' );
	var $contentSidebarWrap = $( '.content-sidebar-wrap' );
	var $content            = $( '.content' );
	var $singleEntries      = $( '.singular .content > .entry' );
	var $archiveEntries     = $( '.archive .content .entry' );
	var $blogEntries        = $( '.blog .content .entry' );
	var $searchEntries      = $( '.search .content .entry' );
	var $sidebar            = $( '.sidebar' );
	var $sidebarWidgets     = $( '.sidebar > .widget' );
	var $menuItem           = $icon.parents( '.menu-item' );
	var $menuLink           = $menuItem.find( 'a' );

	// Add settings HTML.
	$menuItem.addClass( 'maids-toggle' ).append( maidsVars.html );

	// Settings.
	var $settings = $menuLink.next( '.maids-settings' );

	// Open/Close.
	$menuLink.on( 'click', function(e) {
		e.preventDefault();
		$menuItem.toggleClass( 'open' );
		$settings.fadeToggle( 'fast' );
	});

	// Site Container.
	$body.on( 'click', 'input[name="maids-site_container"]', function() {
		$body.toggleClass( 'has-boxed-site-container' );
		$siteContainer.toggleClass( 'boxed' );
	});

	// Content Sidebar Wrap.
	$body.on( 'click', 'input[name="maids-content_sidebar_wrap"]', function() {
		$contentSidebarWrap.toggleClass( 'boxed' );
	});

	// Main Content.
	$body.on( 'click', 'input[name="maids-content"]', function() {
		$content.toggleClass( 'boxed' );
	});

	// Single Entry.
	$body.on( 'click', 'input[name="maids-entry_singular"]', function() {
		$singleEntries.toggleClass( 'boxed' );
	});

	// Archive Entries.
	$body.on( 'click', 'input[name="maids-entry_archive"]', function() {
		$archiveEntries.toggleClass( 'boxed' );
		$blogEntries.toggleClass( 'boxed' );
		$searchEntries.toggleClass( 'boxed' );
	});

	// Sidebar.
	$body.on( 'click', 'input[name="maids-sidebar"]', function() {
		$sidebar.toggleClass( 'boxed' );
	});

	// Sidebar Widgets.
	$body.on( 'click', 'input[name="maids-sidebar_widgets"]', function() {
		$sidebarWidgets.toggleClass( 'boxed' );
	});

});
