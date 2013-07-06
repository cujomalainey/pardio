<html>
<head>
	<title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://code.jquery.com/mobile/1.3.1/jquery.mobile-1.3.1.min.js"></script>
	<script src="/media/js/voter.js"></script>
    <style type="text/css">
        .ui-grid-a .ui-block-a.pic { width: 210px; }
        .ui-grid-a .ui-block-b.main { auto !important; }
    </style>
</head>
<body>
<div data-role="page" id="main">
	<div data-role="header">
    <h1 id="name"></h1>
</div>
<input type="search" name="search" id="request" value="" />
<div id="list">
</div>
<div data-role="footer" class="ui-bar">
    <a href="#exit" data-icon="home">Home</a>
    <a href="javascript:get_update();" data-icon="refresh">Refresh</a>
    <span id="total"></span>
</div>
</div>
<div data-role="dialog" data-rel="dialog" data-transition="pop" data-url="exit" id="exit">
    <div data-role="header" data-theme="e" role="banner">
        <h1 class="ui-title" role="heading" aria-level="1">Warning!</h1>
    </div>
    <div data-role="content" data-theme="d" class="ui-content ui-body-d" role="main">   
        <p>If you leave all your votes will be deleted. Are you sure you want to leave?</p>      
        <p><a href="#" data-rel="back" data-role="button" data-inline="true" data-icon="back" data-corners="true" data-shadow="true" data-theme="d">Cancel</a></p>
        <p><a href="http://wizuma.com/index.php/voter_json/exit_site" data-rel="external" data-ajax="false" data-role="button" data-inline="true" data-icon="back" data-corners="true" data-shadow="true" data-theme="b">Exit</a></p>   
    </div>
</div>
<div data-role="dialog" data-rel="dialog" data-transition="slideup" data-url="search" id="search">
    <div data-role="header" data-theme="e" role="banner">
        <h1 class="ui-title" role="heading" aria-level="1">Search Results</h1>
    </div>
    <div data-role="content" data-theme="d" class="ui-content ui-body-d" role="main" id="search-list">   
         
    </div>
</div>
<!--HTML FOR JS ONLY BELOW-->
<a id='lnkDialog' href="#search" data-rel="dialog" data-transition="slideup" style='display:none;'></a>
<a id='lnkHome' href="#" data-rel="page" data-transition="pop" style='display:none;'></a>
</body>
</html>