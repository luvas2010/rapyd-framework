function create_menu(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

	document.write(
		'<table cellpadding="0" cellspaceing="0" border="0" style="width:98%"><tr>' +
		'<td class="td" valign="top">' +

		'<h3>Basic Info</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/requirements.html">Server Requirements</a></li>' +
			'<li><a href="'+base+'changelog.html">Change Log</a></li>' +
			'<li><a href="'+base+'general/credits.html">Credits</a></li>' +
		'</ul>' +	
		
		'<h3>Installation</h3>' +
		'<ul>' +
			'<li><a href="'+base+'installation/downloads.html">Downloading Rapyd Library</a></li>' +
			'<li><a href="'+base+'installation/index.html">Installation Instructions</a></li>' +
		'</ul>' +
		
		'</td><td class="td_sep" valign="top">' +
		
		'<h3>Introduction</h3>' +
		'<ul>' +
			'<li><a href="'+base+'overview/at_a_glance.html">Rapyd at a Glance</a></li>' +
			'<li><a href="'+base+'overview/features.html">Supported Features</a></li>' +
		'</ul>' +	
				
		'<h3>General Topics</h3>' +
		'<ul>' +
			'<li><a href="'+base+'general/index.html">Getting Started</a></li>' +
			'<li><a href="'+base+'general/concepts.html" class="importante">General Concepts</a></li>' +
			'<li><a href="'+base+'general/views.html">Rapyd Views/Themes</a></li>' +
		'</ul>' +	
		
		'</td><td class="td_sep" valign="top">' +
		
		'<h3>Main Classes</h3>' +
		'<ul>' +
		'<li><a href="'+base+'classes/session.html">Session Class</a></li>' +
		'<li><a href="'+base+'classes/uri.html">URI Class</a></li>' +
		'<li><a href="'+base+'classes/language.html">Language Class</a></li>' +
		'<li><a href="'+base+'classes/authorization.html">Authorization Class</a></li>' +    
		'</ul>' +	

		'<h3>Presentation Components</h3>' +
		'<ul>' +
		'<li><a href="'+base+'classes/dataset.html">DataSet Class</a></li>' +
		'<li><a href="'+base+'classes/datatable.html">DataTable Class</a></li>' +
		'<li><a href="'+base+'classes/datagrid.html">DataGrid Class</a></li>' +		
		'</ul>' +	
    
		'</td><td class="td_sep" valign="top">' +
		'<h3>Editing Components</h3>' +
		'<ul>' +
		'<li><a href="'+base+'classes/fields.html">Field Classes</a></li>' +
		'<li><a href="'+base+'classes/dataobject.html">DataObject Class</a></li>' +
		'<li><a href="'+base+'classes/dataform.html">DataForm Class</a></li>' +		
		'<li><a href="'+base+'classes/dataedit.html">DataEdit Class</a></li>' +
		'<li><a href="'+base+'classes/datafilter.html">DataFilter Class</a></li>' +
		'</ul>' +
		
		'</td></tr></table>');
}



function create_header(basepath)
{
	var base = (basepath == 'null') ? '' : basepath;

		document.write(
'\
			<div class="clearer">&nbsp;</div>\
			<div id="site-title">\
				<a href="'+base+'index.html">Rapyd Framework User Guide</a> <span> / home</span>\
			</div>\
			<div id="navigation">\
				<div id="main-nav">\
					<ul class="tabbed">\
						<li class="current-tab"><a href="'+base+'index.html">Home</a></li>\
						<li><a href="'+base+'installation/installation.html">Installation</a></li>\
						<li><a href="#">Urls and Flow</a></li>\
						<li><a href="#">Filesystem</a></li>\
						<li><a href="#">MVC</a></li>\
					</ul>\
					<div class="clearer">&nbsp;</div>\
				</div>\
				<div id="sub-nav">\
					<ul class="tabbed">\
						<li><a href="index.html">Frontpage</a></li>\
						<li><a href="style-demo.html">Style Demo</a></li>\
						<li class="current-tab"><a href="two-columns.html">Two Columns</a></li>\
						<li><a href="single-column.html">Single Column</a></li>\
						<li><a href="archives.html">Archives</a></li>\
						<li><a href="empty-page.html">Empty Page</a></li>\
					</ul>\
					<div class="clearer">&nbsp;</div>\
				</div>\
			</div>');
}


function create_footer()
{
		document.write(
		'<div id="footer">\
			<div class="left">&copy; 2006-2011 Felice Ostuni</div> \
			<div class="right">main site: <a href="http://www.rapyd.com">rapyd.com</a> | template by <a href="http://arcsin.se/" rel="nofollow">arcsin.se</a></div>\
			<div class="clearer">&nbsp;</div>\
		</div>');
}


function create_search()
{
		document.write('<form method="get" action="http://www.google.com/search"><input type="hidden" name="as_sitesearch" id="as_sitesearch" value="www.rapyd.com/rapyd_guide/" />Search Rapyd Guide&nbsp; <input type="text" class="input" style="width:200px;" name="q" id="q" size="31" maxlength="255" value="" />&nbsp;<input type="submit" class="submit" name="sa" value="Go" /></form>');
}


function create_sidebar()
{
		document.write('\
			<div class="right sidebar" id="sidebar">\
				<div class="section network-section">\
					<div class="section-title">Network News</div>\
					<div class="section-content">\
						<ul class="nice-list">\
							<li><a href="#">Nullam eros</a></li>\
							<li><a href="#">Eleifend nec tortor</a></li>\
							<li><a href="#">Duis mi lectus</a></li>\
							<li><a href="#">Integer diam elit</a></li>\
							<li><a href="#">Enim dapibus venenatis</a></li>\
							<li><a href="#" class="more">Visit Network Site &#187;</a></li>\
						</ul>\
					</div>\
				</div>\
			</div>');

}

window.onload = function() {
	//myHeight = new fx.Height('nav', {duration: 400});
	//myHeight.hide();
  
  dp.SyntaxHighlighter.addControls = false;
  dp.SyntaxHighlighter.HighlightAll('code'); 

}

