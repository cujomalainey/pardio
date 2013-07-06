<html>
<head>
	<title>Wizuma DJ</title>
	<script src="/media/js/jquery-2.0.2.min.js"></script>
	<script type="text/javascript">
    	function checkCode()
      	{
      		var str = encodeURI(document.getElementById("code").value);
      		$.getJSON('http://wizuma.com/index.php/voter_json/check_code/' + str, function(data) {
      			if (data) {
      				window.location.replace("http://wizuma.com/index.php/voter/party");
      			}
      			else
      			{
      				document.getElementById("response").innerHTML = "Sorry we did not find that code, please check your spelling.";
      			}	
      		})
      	}
    </script>
</head>
<body>
	<form name="login" action="">
		Email: <input type="email" name="user"><br/>
		Password: <input type="password" name="user"><br/>
		<input type="submit" value="Submit">
	</form>
	<hr />
	<form name="site" action="javascript:checkCode()">
		Site Code: <input type="text" name="site" id="code">
		<input type="submit" value="Submit">
	</form>
	<p id="response">
	</p>
</body>
</html>