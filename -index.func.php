<?php
/*
 * @ignore
 */
defined('IN_APPLICATION') or exit;

function showData($compName,$img,$name,$type,$phone,$email,$url,$id) {
	if($type == 'Company') {
		return "<div id=\"contactDetails\">
					<fieldset><legend><h2>$compName</h2></legend>
					<img src=\"images/$img\" height=\"180\" width=\"300\"/>
						<div id=\"info\">
							<h3 class=\"name\"><br />$name</h3>
							<h4 class=\"contactInfo\">Company Info</h4>
							<p>$type</p>
							<p>$phone</p>
							<p>$email</p>
						</div>
						<p><a href=\"$url\">$url</a></p>
						<h5>$id</h5>
						<p id=\"view\"><a href=\"contact.php?mode=view&id=$id&type=c\"><button>View Contact</button></a></p>
					</fieldset>
				</div>";
	}
	if($type == 'Person') {
		return "<div id=\"contactDetails\">
					<fieldset><legend><h2>$compName</h2></legend>
					<img src=\"images/$img\" height=\"180\" width=\"300\"/>
						<div id=\"info\">
							<h3 class=\"name\"><br />$name</h3>
							<h4 class=\"contactInfo\">Personel Info</h4>
							<p>$type</p>
							<p>$phone</p>
							<p>$email</p>
						</div>
						<p><a href=\"$url\">$url</a></p>
						<h5>$id</h5>
						<p id=\"view\"><a href=\"contact.php?mode=view&id=$id&type=p\"><button>View Contact</button></a></p>
					</fieldset>
				</div>";
	}
}


