<?php
define('IN_APPLICATION',true);

ini_set('display_errors','on');
error_reporting(E_ALL | E_STRICT);

// =================================
// define absolute path of application
define('ABS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

// =========================
// initialize bootstrap bill
require_once ABS_PATH . 'bootstrap.php';
require_once ABS_PATH . '-index.func.php';
//require_once ABS_PATH . 'index.func.php';
$errors = array();

$query = $mysqli->query("SELECT * FROM contacts");

if( $query ) {
	$results = array();
	
	$results = $query->fetch_all(MYSQLI_ASSOC);
	//print_r ($results);
	$query->close();
	foreach( $results as $result ) {
		//get the contact id
		$theId = $result['id'];
		$typeID = $result['type_id'];
		$getType = $mysqli->query("SELECT Label FROM contact_types WHERE id=$typeID");
		$type = $getType->fetch_object()->Label;
		$getType->close();
		//print $type;
		if( !empty($type) ) {
			if($type == 'Company') {
				//get all the others using contact id
				$getCompName = $mysqli->query("SELECT Company_Name FROM contact_company_details WHERE Contact_ID=$theId");
				$compName = $getCompName->fetch_object()->Company_Name;
				if($compName) {$getCompName->close();}
				//print $compName;
				$getImg = $mysqli->query("SELECT Logo FROM contact_company_details WHERE Contact_ID=$theId");
				$theImg = $getImg->fetch_object()->Logo;
				//print $theImg;
				if( $theImg ) {
					$img = $theImg;
					$getImg->close();
				}else {
					$img = 'defaultUser.jpg';
				}
				//print $img;
				$getName = $mysqli->query("SELECT First_Name, Last_Name FROM contact_person_details WHERE Contact_ID=$theId");
				$row = $getName->fetch_row();
				$name = $row[0].' '.$row[1];
				if($row) {$getName->close();}
				//print $name;
				$getPhone = $mysqli->query("SELECT Telephone FROM contact_company_details WHERE Contact_ID=$theId");
				$phone = $getPhone ? $getPhone->fetch_object()->Telephone: '';
				if($phone) {$getPhone->close();}
				//print $phone;
				$getEmail = $mysqli->query("SELECT Email FROM contact_company_details WHERE Contact_ID=$theId");
				$email = $getEmail ? $getEmail->fetch_object()->Email : '';
				if($email) {$getEmail->close();}
				//print $email;
				$getUrl = $mysqli->query("SELECT Website FROM contact_company_details WHERE Contact_ID=$theId");
				$url = $getUrl ? $getUrl->fetch_object()->Website : '';
				if($url) {$getUrl->close();}
				//print $url;
				
				
			}//end if type company
			if($type == 'Person') {
				$getPhone = $mysqli->query("SELECT Primary_Telephone FROM contact_person_details WHERE Contact_ID=$theId");
				$phone = $getPhone->fetch_object()->Primary_Telephone;
				//print $phone;
				//get all the others using contact id
				$getImg = $mysqli->query("SELECT Picture FROM contact_person_details WHERE Contact_ID=$theId");
				$theImg = $getImg->fetch_object()->Picture;
				//print $theImg;
				if( $theImg ) {
					$img = $theImg;
					$getImg->close();
				}else {
					$img = 'defaultUser.jpg';
				}
				//print $img;
				$getName = $mysqli->query("SELECT First_Name, Last_Name FROM contact_person_details WHERE Contact_ID=$theId");
				$row = $getName->fetch_row();
				$name = $row[0].' '.$row[1];
				if($row) {$getName->close();}
				//print $name;
				$getCompName = $mysqli->query("SELECT Company_Name FROM contact_company_details WHERE Contact_ID=$theId");
				if($getCompName === true) {
					$compName = $getCompName->fetch_object()->Company_Name;
					$getCompName->close();
				} else { $compName = $name; }
				
				
				//print $compName;
				$getEmail = $mysqli->query("SELECT Email FROM contact_person_details WHERE Contact_ID=$theId");
				$email = $getEmail ? $getEmail->fetch_object()->Email : '';
				if($email) {$getEmail->close();}
				//print $email;
				$getUrl = $mysqli->query("SELECT Website FROM contact_person_details WHERE Contact_ID=$theId");
				$url = $getUrl ? $getUrl->fetch_object()->Website : '';
				if($url) {$getUrl->close();}
				//print $url;
				
				
				
				
			}//end if type person
		}//end if !empty($type)
		$data[] = showData($compName,$img,$name,$type,$phone,$email,$url,$theId);
		//print showData($compName,$img,$name,$type,$phone,$email,$url,$theId);
	}//end foreach
	//print_r ($data);
}//end if
else {
	$errors[] = 'Error: No contacts were found';
}
?>
<!DOCTYPE html>
<head>
<title>PHP MySQL</title>
<link rel="stylesheet" href="main.css" type="text/css" />
</head>
<body>
	<div id="wrap">
<?php if( empty($errors) ) :?>
	<a href="contact.php?mode=add"><button id="add" >New User?</button></a>
		<h1>Contacts</h1>
		<?php foreach($data as $div) { print $div; } ?>
	</div><!--end wrap class-->
<?php endif;?>
	<footer id="footer">
		<p><em>&copy Copyright Jeremy Mills</em></p>
	</footer>
</body>
</html>
