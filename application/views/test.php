<html>
<head>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script src="/media/js/jquery.rdio.min.js"></script>
<script src="/media/js/dj.js"></script>
<title></title>
  
</head>
<body>
	<?php if ($playback)
{
	?>
<div id="apiswf"></div>
	<input id="play_key"/><button id="play">Play</button>
	<input id="token" type="hidden" value=<?php echo "\"" . $token . "\""; ?>>
<?php
}
?>
<ul id="queue">
</ul>
</body>
</html>