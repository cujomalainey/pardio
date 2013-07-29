<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="/media/js/dj.js"></script>
<script type="text/javascript">
	$(function() {
    $( "#dialog-confirm" ).dialog({
      resizable: false,
      height:140,
      modal: true,
      buttons: {
        "Initialize": function() {
          $( this ).dialog( "close" );
        },
        "Run without Rdio": function() {
          $( this ).dialog( "close" );
        }
      }
    });
  });
</script>
<link rel="stylesheet" href="/media/css/jquery-ui.min.css" />
<title></title>
<style type="text/css">

body {
	margin: 0px;
	background:
		linear-gradient(27deg, #151515 5px, transparent 5px) 0 5px,
		linear-gradient(207deg, #151515 5px, transparent 5px) 10px 0px,
		linear-gradient(27deg, #222 5px, transparent 5px) 0px 10px,
		linear-gradient(207deg, #222 5px, transparent 5px) 10px 5px,
		linear-gradient(90deg, #1b1b1b 10px, transparent 10px),
		linear-gradient(#1d1d1d 25%, #1a1a1a 25%, #1a1a1a 50%, transparent 50%, transparent 75%, #242424 75%, #242424);
	background-color: #131313;
	background-size: 20px 20px;
}

#header {
	height: 10%;
	background-color: #0f0;
}

#menu {
	width: 10%;
	float: left;
	background-color: #f00;
}

#main {
	width: 80%;
	float: left;
	min-height: 1px;
}

#songQueue {
	z-index: 10;
	width: 80%;
	position: absolute;
}

#log {
	width: 10%;
	float: left;
	background-color: #0ff;
}

.frequency {
	background: 
		radial-gradient(rgba(255,255,255,0) 0, rgba(255,255,255,.15) 30%, rgba(255,255,255,.3) 32%, rgba(255,255,255,0) 33%) 0 0,
		radial-gradient(rgba(255,255,255,0) 0, rgba(255,255,255,.1) 11%, rgba(255,255,255,.3) 13%, rgba(255,255,255,0) 14%) 0 0,
		radial-gradient(rgba(255,255,255,0) 0, rgba(255,255,255,.2) 17%, rgba(255,255,255,.43) 19%, rgba(255,255,255,0) 20%) 0 110px,
		radial-gradient(rgba(255,255,255,0) 0, rgba(255,255,255,.2) 11%, rgba(255,255,255,.4) 13%, rgba(255,255,255,0) 14%) -130px -170px,
		radial-gradient(rgba(255,255,255,0) 0, rgba(255,255,255,.2) 11%, rgba(255,255,255,.4) 13%, rgba(255,255,255,0) 14%) 130px 370px,
		radial-gradient(rgba(255,255,255,0) 0, rgba(255,255,255,.1) 11%, rgba(255,255,255,.2) 13%, rgba(255,255,255,0) 14%) 0 0,
		linear-gradient(45deg, #343702 0%, #184500 20%, #187546 30%, #006782 40%, #0b1284 50%, #760ea1 60%, #83096e 70%, #840b2a 80%, #b13e12 90%, #e27412 100%);
	background-size: 470px 470px, 970px 970px, 410px 410px, 610px 610px, 530px 530px, 730px 730px, 100% 100%;
	background-color: #840b2a;
	width: 5%;
	margin-left: 5%;
	z-index: 2;
	float: left;
	position: relative;
}

.column { float: left; padding-bottom: 10px; }
.portlet { margin: 1em 1em 1em 1em; }
.portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
.portlet-header .ui-icon { float: right; }
.portlet-content { padding: 0.4em; }
.ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
.ui-sortable-placeholder * { visibility: hidden; }

</style>

</head>
<body>
<div id="dialog-confirm" title="Empty the recycle bin?">
 	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>These items will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
	<?php if ($playback)
{
	?>
<div id="apiswf"></div>
<input id="token" type="hidden" value=<?php echo "\"" . $token . "\""; ?>>
<?php
}
?>
<div id="header">
</div>
<div id="menu">
	<button id="play">Play</button>
	<button id="queue">Queue</button>
	<button id="next">Next</button>
</div>
<div id="main">
	<div id="songQueue" class="column">
	</div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
	<div class="frequency"></div>
</div>
<div id="log">
	Fusce sit amet felis nisi. Donec erat eros, 
</div>
</body>
</html>
