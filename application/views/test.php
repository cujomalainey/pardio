<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script src="/media/js/dj.js"></script>
<title><?php echo $site; ?></title>
<style type="text/css">

body {
	margin: 0px;
}

#header {
	height: 40px;
	background-color: #0f0;
}

#menu {
	width: 10%;
	float: left;
	background-color: #f00;
}

#songqueue {
	width: 80%;
	float: left;
	background-color: #00f;
}

#log {
	width: 10%;
	float: left;
	background-color: #0ff;
}

</style>  
</head>
<body>
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
<div id="songQueue">
	Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ligula nisi, rutrum eu auctor at, adipiscing mattis tellus. Morbi a risus turpis. Nulla odio nisl, ultrices sit amet bibendum nec, sodales id metus. Integer rutrum vehicula gravida. Ut pretium ultrices sapien, a rhoncus enim venenatis sit amet. Suspendisse ullamcorper neque et pretium sollicitudin. Integer ornare mattis dui, sit amet blandit justo lacinia quis.
</div>
<div id="log">
	Fusce sit amet felis nisi. Donec erat eros, rhoncus et tempus a, suscipit sit amet ante. Vestibulum tristique nec nibh vel aliquam. Phasellus interdum lectus nec mi varius hendrerit. Integer iaculis ante sit amet blandit condimentum. Donec semper risus et nisl posuere egestas. Curabitur feugiat lacus mi, et rutrum sem pharetra quis. In id scelerisque orci. Mauris et commodo orci, nec ullamcorper dolor. Aliquam sit amet vehicula augue, at accumsan nibh. Donec sapien odio, consequat in magna id, commodo imperdiet ante.
</div>
</body>
</html>