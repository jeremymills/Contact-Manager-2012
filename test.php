<!DOCTYPE html>
<head>
	<title>Test</title>
	<link rel="stylesheet" href="main.css" type="text/css" />
</head>

	<body>
	<div id="wrap">
	<a href="contact.php?mode=add"><button id="add" >New User?</button></a>
		<h1>Contacts</h1>
		<div id="contactDetails">
		<fieldset><legend><h2>$compName</h2></legend>
		<img src="" height="180" width="300"/>
			<div id="info">
				<h3 class="name">$name</h3>
				<h4 class="contactInfo">$contactInfo</h4>
				<p>$type</p>
				<p>$phone</p>
				<p>$email</p>
			</div>
			<p><a href="#">$url</a></p>
			<h5>$id</h5>
		</div>
		
		<div id="contactDetails">
		<fieldset><legend><h2>$compName</h2></legend>
		<img src="" height="180" width="300"/>
			<div id="info">
				<h3 class="name">$name</h3>
				<h4 class="contactInfo">$contactInfo</h4>
				<p>$type</p>
				<p>$phone</p>
				<p>$email</p>
			</div>
			<p class="web"><a href="#">$url</a></p>
			<h5>$id</h5>
		</div>
		
		
		
	</div>
	</body>
</html>