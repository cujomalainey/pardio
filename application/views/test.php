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
      height:240,
      width:600,
      modal: true,
      buttons: {
        "Initialize": function() {
        	controller.init();
        },
        "Run without Rdio": function() {
          	$( this ).dialog( "close" );
        }
      }
    });
    $( "#progressbar" ).progressbar({value: false});
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
	width: 12.5%;
	z-index: 2;
	float: left;
	position: relative;
	min-height: 1px;
}

.column { float: left; padding-bottom: 10px;}
.portlet { margin: 1em 1em 1em 1em; }
.portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
.portlet-header .ui-icon { float: right; }
.portlet-content { padding: 0.4em; }
.ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
.ui-sortable-placeholder * { visibility: hidden; }

</style>

</head>
<body>
<div id="dialog-confirm" title="Initialize">
 	<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>You need to initialize rdio if you want to use it.<br/><div id="progressbar"></div></p>
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
	<div class="frequency" style="background-image: linear-gradient(left , rgb(0,0,0) 13%, rgb(200,32,32) 57%);
background-image: -o-linear-gradient(left , rgb(0,0,0) 13%, rgb(200,32,32) 57%);
background-image: -moz-linear-gradient(left , rgb(0,0,0) 13%, rgb(200,32,32) 57%);
background-image: -webkit-linear-gradient(left , rgb(0,0,0) 13%, rgb(200,32,32) 57%);
background-image: -ms-linear-gradient(left , rgb(0,0,0) 13%, rgb(200,32,32) 57%);

background-image: -webkit-gradient(
	linear,
	left top,
	right top,
	color-stop(0.13, rgb(0,0,0)),
	color-stop(0.57, rgb(200,32,32))
);"></div>
	<div class="frequency" style="background-image: linear-gradient(right , rgb(255,116,24) 13%, rgb(200,32,32) 57%);
background-image: -o-linear-gradient(right , rgb(255,116,24) 13%, rgb(200,32,32) 57%);
background-image: -moz-linear-gradient(right , rgb(255,116,24) 13%, rgb(200,32,32) 57%);
background-image: -webkit-linear-gradient(right , rgb(255,116,24) 13%, rgb(200,32,32) 57%);
background-image: -ms-linear-gradient(right , rgb(255,116,24) 13%, rgb(200,32,32) 57%);

background-image: -webkit-gradient(
	linear,
	right top,
	left top,
	color-stop(0.13, rgb(255,116,24)),
	color-stop(0.57, rgb(200,32,32))
);"></div>
	<div class="frequency" style="background-image: linear-gradient(left , rgb(255,116,24) 13%, rgb(232,232,16) 57%);
background-image: -o-linear-gradient(left , rgb(255,116,24) 13%, rgb(232,232,16) 57%);
background-image: -moz-linear-gradient(left , rgb(255,116,24) 13%, rgb(232,232,16) 57%);
background-image: -webkit-linear-gradient(left , rgb(255,116,24) 13%, rgb(232,232,16) 57%);
background-image: -ms-linear-gradient(left , rgb(255,116,24) 13%, rgb(232,232,16) 57%);

background-image: -webkit-gradient(
	linear,
	left top,
	right top,
	color-stop(0.13, rgb(255,116,24)),
	color-stop(0.57, rgb(232,232,16))
);"></div>
	<div class="frequency" style="background-image: linear-gradient(right , rgb(46,232,0) 13%, rgb(232,232,16) 57%);
background-image: -o-linear-gradient(right , rgb(46,232,0) 13%, rgb(232,232,16) 57%);
background-image: -moz-linear-gradient(right , rgb(46,232,0) 13%, rgb(232,232,16) 57%);
background-image: -webkit-linear-gradient(right , rgb(46,232,0) 13%, rgb(232,232,16) 57%);
background-image: -ms-linear-gradient(right , rgb(46,232,0) 13%, rgb(232,232,16) 57%);

background-image: -webkit-gradient(
	linear,
	right top,
	left top,
	color-stop(0.13, rgb(46,232,0)),
	color-stop(0.57, rgb(232,232,16))
);"></div>
	<div class="frequency" style="background-image: linear-gradient(left , rgb(46,232,0) 13%, rgb(16,230,219) 57%);
background-image: -o-linear-gradient(left , rgb(46,232,0) 13%, rgb(16,230,219) 57%);
background-image: -moz-linear-gradient(left , rgb(46,232,0) 13%, rgb(16,230,219) 57%);
background-image: -webkit-linear-gradient(left , rgb(46,232,0) 13%, rgb(16,230,219) 57%);
background-image: -ms-linear-gradient(left , rgb(46,232,0) 13%, rgb(16,230,219) 57%);

background-image: -webkit-gradient(
	linear,
	left top,
	right top,
	color-stop(0.13, rgb(46,232,0)),
	color-stop(0.57, rgb(16,230,219))
);"></div>
	<div class="frequency" style="background-image: linear-gradient(right , rgb(0,12,230) 13%, rgb(16,230,219) 57%);
background-image: -o-linear-gradient(right , rgb(0,12,230) 13%, rgb(16,230,219) 57%);
background-image: -moz-linear-gradient(right , rgb(0,12,230) 13%, rgb(16,230,219) 57%);
background-image: -webkit-linear-gradient(right , rgb(0,12,230) 13%, rgb(16,230,219) 57%);
background-image: -ms-linear-gradient(right , rgb(0,12,230) 13%, rgb(16,230,219) 57%);

background-image: -webkit-gradient(
	linear,
	right top,
	left top,
	color-stop(0.13, rgb(0,12,230)),
	color-stop(0.57, rgb(16,230,219))
);"></div>
	<div class="frequency" style="background-image: linear-gradient(left , rgb(0,12,230) 13%, rgb(144,16,230) 57%);
background-image: -o-linear-gradient(left , rgb(0,12,230) 13%, rgb(144,16,230) 57%);
background-image: -moz-linear-gradient(left , rgb(0,12,230) 13%, rgb(144,16,230) 57%);
background-image: -webkit-linear-gradient(left , rgb(0,12,230) 13%, rgb(144,16,230) 57%);
background-image: -ms-linear-gradient(left , rgb(0,12,230) 13%, rgb(144,16,230) 57%);

background-image: -webkit-gradient(
	linear,
	left top,
	right top,
	color-stop(0.13, rgb(0,12,230)),
	color-stop(0.57, rgb(144,16,230))
);"></div>
	<div class="frequency" style="background-image: linear-gradient(right , rgb(0,0,0) 13%, rgb(144,16,230) 57%);
background-image: -o-linear-gradient(right , rgb(0,0,0) 13%, rgb(144,16,230) 57%);
background-image: -moz-linear-gradient(right , rgb(0,0,0) 13%, rgb(144,16,230) 57%);
background-image: -webkit-linear-gradient(right , rgb(0,0,0) 13%, rgb(144,16,230) 57%);
background-image: -ms-linear-gradient(right , rgb(0,0,0) 13%, rgb(144,16,230) 57%);

background-image: -webkit-gradient(
	linear,
	right top,
	left top,
	color-stop(0.13, rgb(0,0,0)),
	color-stop(0.57, rgb(144,16,230))
);"></div>
</div>
<div id="log">
	Fusce sit amet felis nisi. Donec erat eros, 
</div>
</body>
</html>
