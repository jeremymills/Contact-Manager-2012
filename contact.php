<?php
define('IN_APPLICATION',true);

ini_set('display_errors','on');
error_reporting(E_ALL | E_STRICT);

// =================================
// define absolute path of application
define('ABS_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('FILES_PATH', ABS_PATH . 'images' . DIRECTORY_SEPARATOR);

/*USE THIS SHIT
SELECT c.*, cp.*, cc.*
	FROM contacts AS c
	INNER JOIN contact_person_details AS cp ON (c.id = cp.contact_id)
	INNER JOIN contact_company_details AS cc ON (c.id = cc.id)
WHERE c.id = 1;

*/
// =========================
// initialize bootstrap bill
require_once ABS_PATH . 'bootstrap.php';
//get request to get the mode attribute
$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
//error array to validate error checking
$errors = array();

//validation for contact type
$isAPerson = 1;
$isACompany = 2;

//time variables for created and updated
$createdTime = time();
$updatedTime = time();

//fields array to hold field data
$fields = array(
			'company' => '', 'fName' => '', 'lName' => '', 'phone' => '', 'email' => '', 'address' => '', 'url' => '', 'mobile' => '', 'fBook' => '', 'twitter' => '', 'linkedIn' => '', 'note' => ''
			);

// ==================================
// error checking for upload of files
if( !defined('UPLOAD_ERR_EMPTY') ){
    define('UPLOAD_ERR_EMPTY', 5);
}

$lang = array(
    'upload_error' => array(
        1 => 'UPLOAD_ERR_INI_SIZE',
        2 => 'UPLOAD_ERR_FORM_SIZE',
        3 => 'UPLOAD_ERR_PARTIAL',
        4 => 'UPLOAD_ERR_NO_FILE',
        5 => 'UPLOAD_ERR_EMPTY',
        6 => 'UPLOAD_ERR_NO_TMP_DIR',
        7 => 'UPLOAD_ERR_CANT_WRITE',
        8 => 'UPLOAD_ERR_EXTENSION'
    )
);


if (empty($mode) ) {
	$errors[] = 'Invalid page request, go <a href="-index.php">back</a> and try again.';
}
?>
<!DOCTYPE html>
<head>
<title>PHP MySQL</title>
<link rel="stylesheet" href="main.css" type="text/css" />
</head>
<body>
<?php if( isset($_POST['addP']) ) : ?>
		<div style="width: 100%; background: red; color: white;">
			<?php 
			print 'inside all this shit';
				$fields['cName'] = isset($_POST['cName']) ? strip_tags(stripslashes(trim($_POST['cName']))) : '';
				$fields['fName'] = isset($_POST['fName']) ?  strip_tags(stripslashes(trim($_POST['fName']))) : '';
				$fields['lName'] = isset($_POST['lName']) ?  strip_tags(stripslashes(trim($_POST['lName']))) : '';
				$fields['phone'] = isset($_POST['phone']) ?  strip_tags(stripslashes(trim($_POST['phone']))) : '';
				$fields['email'] = isset($_POST['email']) ?  strip_tags(stripslashes(trim($_POST['email']))) : '';
				$fields['address'] = isset($_POST['address']) ?  strip_tags(stripslashes(trim($_POST['address']))) : '';
				$fields['url'] = isset($_POST['url']) ?  $_POST['url'] : '';
				$fields['mobile'] = isset($_POST['mobile']) ? strip_tags(stripslashes(trim($_POST['mobile']))) : '';
				$fields['fBook'] = isset($_POST['fBook']) ?  $_POST['fBook'] : '';
				$fields['twitter'] = isset($_POST['twitter']) ?  $_POST['twitter'] : '';
				$fields['linkedIn'] = isset($_POST['linkedIn']) ?  $_POST['linkedIn'] : '';
				$fields['note'] = isset($_POST['note']) ? strip_tags(stripslashes(trim($_POST['note']))) : '';
				
				$file = isset($_FILES['photo']) ?  $_FILES['photo'] : array();
				
				if( empty($fields['fName']) ) {
					$errors[] = 'First Name field can not be empty';
				}//end if
				if( empty($fields['lName']) ) {
					$errors[] = 'Last Name field can not be empty';
				}//end if
				if( empty($fields['phone']) ) {
					$errors[] = 'Phone field can not be empty';
				}//end if
				if( !is_numeric($fields['phone']) ) {
					$errors[] = 'Phone field must be all numbers';
				}
				if( empty($fields['email']) ){
					$errors[] = 'Email field can not be empty';
				}
				if( empty($fields['url']) ) {
					$errors[] = 'Website field can not be empty';
				}
				print_r ($errors);
				if(empty($errors) ) {
				//print 'NO ERRORS!!!!!!!!!!!!!!!!!!!!<br />';
					$companyNameExists = false;
					
						if( !empty($fields['cName']) ) {
							print 'lalala=='.$fields['cName'].'==ALALALA...';
							$cNameToLower = strtolower($fields['cName']);
							$check = $mysqli->query("SELECT * FROM contact_company_details");
							if($check){
								$returnCheck = $check->fetch_all(MYSQLI_ASSOC);
								print 'THIS IS INSIDE PERSON';
								if($returnCheck) {
									print_r ($returnCheck);
									//$check->close();
										foreach( $returnCheck as $return ) {
											$i=0;
											$returnLower = strtolower($returnCheck[$i]['Company_Name']);
											print $returnLower;
											print '= Matches =' . $cNameToLower;
												if($returnLower == $cNameToLower){
												print '<br /> MATCH!!!!!';
													$companyNameExists = true;
													$companyName = $returnCheck[$i]['Company_Name'];
													$companyID = $returnCheck[$i]['id'];
													$insertedContactID = $returnCheck[$i]['Contact_ID'];
												}//end if
											++$i;
										}//end foreach
								}//end if true
							}//end if($check)
							$check->close();
							if( $companyNameExists ) {
								//Company Name exists
								//set up the sql statements
								// update the updated time in contacts table
								$updateTheTime = $mysqli->prepare("UPDATE contacts SET updated= ? WHERE id=?");
								print $updatedTime.' '. $insertedContactID;
								if($updateTheTime) {
									$updateTheTime->bind_param('ii',$updatedTime,$insertedContactID);
									print $updateTheTime->error;
									$updateTheTime->close();
								}
								//insert into contact_person_details
								$person = $mysqli->prepare("INSERT into contact_person_details (First_Name, Last_Name, Primary_Telephone, Mobile_Telephone, Email, Facebook, Twitter, LinkdIn, Website, Address, Company_ID, Contact_ID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
								//bind params
								if($person) {
									$person->bind_param('ssssssssssii',$fields['fName'],$fields['lName'],$fields['phone'],$fields['mobile'],$fields['email'],$fields['fBook'],$fields['twitter'],$fields['linkedIn'],$fields['url'],$fields['address'],$companyID,$insertedContactID);
									//execute the sql statement
									$person->execute();
									$person->close();
								}
								
								
							} else {
								//company name does not exist
								print '<br />company name does not EXIST!!!!!!';
								//insert as new contact
								$insertNewContact = $mysqli->prepare("INSERT INTO contacts (created, updated, type_id) VALUES(?,?,?)");
								if($insertNewContact) {
								print '<br />inside insert new contact with company name';
									$insertNewContact->bind_param('iii',$createdTime,$updatedTime,$isACompany);
									$insertNewContact->execute();
									$insertedContactID = $mysqli->insert_id;
									$insertNewContact->close();
								}//if insernewcontact is true
								//insert new company for the contact ID
								$insertNewCompany = $mysqli->prepare("INSERT into contact_company_details (Company_Name, Email, Telephone, Website, Facebook, Twitter, Address, Contact_ID) VALUES (?,?,?,?,?,?,?,?)");
								//bind params
								if($insertNewCompany) {
								print '<br />inside insert new company';
									$insertNewCompany->bind_param('sssssssi',$fields['cName'], $fields['email'], $fields['phone'],$fields['url'],$fields['fBook'],$fields['twitter'],$fields['address'],$insertedContactID);
									$insertNewCompany->execute();
									print 'Error:'.$insertNewCompany->error.'!!!!!!!!!';
									$insertNewCompany->close();
									//grab the last inserted ID
									$insertedCompanyID = $mysqli->insert_id;
								}//end if true
								//insert new person details
								//prepare the sql statement
								$insertNewPerson = $mysqli->prepare("INSERT into contact_person_details (First_Name, Last_Name, Primary_Telephone, Mobile_Telephone, Email, Facebook, Twitter, LinkdIn, Website, Address, Company_ID, Contact_ID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
								//bind params
								if($insertNewPerson) {
								//print '<br />inside insert new person';
									$insertNewPerson->bind_param('ssssssssssii',$fields['fName'],$fields['lName'],$fields['phone'],$fields['mobile'],$fields['email'],$fields['fBook'],$fields['twitter'],$fields['linkedIn'],$fields['url'],$fields['address'],$insertedCompanyID,$insertedContactID);
									$insertNewPerson->execute();
									$insertNewPerson->close();
									$lastInsertPerson->insert_id;
								}//end if $person true
							}//end if-else
						}//end if(isset($fields['cName']))
						else {
						//print '<br />COMPANY NAME FIELD IS EMPTY';
							//company name field is empty
								//insert as new contact
								$insertNewContact = $mysqli->prepare("INSERT into contacts (created, updated, type_id) VALUES(?,?,?)");
								if($insertNewContact) {
								//print '<br />inside insert new contact DUHHHHHH';
									$insertNewContact->bind_param('iii',$createdTime,$updatedTime,$isAPerson);
									$insertNewContact->execute();
									$insertedContactID = $mysqli->insert_id;
									$insertNewContact->close();
								}//end if insertnewcontact is true

								//insert new person details
								//prepare the sql statement
								$newPerson = $mysqli->prepare("INSERT into contact_person_details (First_Name, Last_Name, Primary_Telephone, Mobile_Telephone, Email, Facebook, Twitter, LinkdIn, Website, Address, Company_ID,Contact_ID) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
								//bind params
								if($newPerson) {
								$null=null;
									//print '<br />inside insert new person';
									$newPerson->bind_param('ssssssssssii',$fields['fName'],$fields['lName'],$fields['phone'],$fields['mobile'],$fields['email'],$fields['fBook'],$fields['twitter'],$fields['linkedIn'],$fields['url'],$fields['address'],$null,$insertedContactID);
									$newPerson->execute();
									//print '!!!!!!!!!!'.$newPerson->error.'!!!!!!!!!!!';
									$newPerson->close();
									$lastInsertPerson = $mysqli->insert_id;
								}//end person is true
						}//end add contact
						if( !empty($file) ) {
							print_r ($file);
							$openDes;
							
							if( file_exists($file['tmp_name']) ){
								//chmod($file['tmp_name'], 755);
								chmod(FILES_PATH, 755);
								
								$file_pieces = explode('.', $file['name']);
								$extension = array_pop($file_pieces);
								
								unset($file_pieces); //cleanup the namespace
								
								$name = uniqid(time() . '_', true);
								
								$destination = FILES_PATH . $name . '.' . $extension;
								$openDes = $name.'.'.$extension;
								//print $openDes;
								if( !move_uploaded_file($file['tmp_name'], $destination) ){
									$errors[] = 'Unable to move file to destination.';
								}
							}
							if(empty($errors) ) {
								$insert = $mysqli->prepare("UPDATE contact_person_details SET Picture=? WHERE id=?");
								if( $insert ) {
					
									$insert->bind_param('sd',$openDes,$lastInsertCompany);
									$insert->execute();
									print $insert->error;
								}//end if $insert
								$insert->close();													
							}//end if(empty($errors))
						}//end if ( !empty($file) )
						else {
							$openDes = 'defaultUser.jpg';
							$insert = $mysqli->prepare("UPDATE contact_person_details SET Picture=? WHERE id=?");
								if( $insert ) {
					
									$insert->bind_param('sd',$openDes,$lastInsertCompany);
									$insert->execute();
									print $insert->error;
								}//end if $insert
								$insert->close();
						}
						if( !empty($fields['note']) ) {
							//print '<br />inside field note is not empty!!';
								$insertNewNote = $mysqli->prepare("INSERT into contact_notes (Created,Updated,Contact_id,Message) Values(?,?,?,?)");
									if($insertNewNote) {
									//print 'note query passed';
										$insertNewNote->bind_param('iiis',$createdTime,$updatedTime,$insertedContactID,$fields['note']);
										$insertNewNote->execute();
										$insertNewNote->close();
									}//end if($insertNewNote)
						}//end if isset($fields['note'])
				}//end if(empty($errors))
			?>
				
			<p><?php print_r ($fields);?></p>
			<p><?php print_r ($_POST); ?></p>
		</div>
<?php endif; ?>
<?php if( isset($_POST['addC']) ) : ?>
		<div style="width: 100%; background:red; color:white;">
			<?php 
			print 'inside all this COMPANY shit';
				$fields['cName'] = isset($_POST['cName']) ? strip_tags(stripslashes(trim($_POST['cName']))) : '';
				$fields['fName'] = isset($_POST['fName']) ?  strip_tags(stripslashes(trim($_POST['fName']))) : '';
				$fields['lName'] = isset($_POST['lName']) ?  strip_tags(stripslashes(trim($_POST['lName']))) : '';
				$fields['phone'] = isset($_POST['phone']) ?  strip_tags(stripslashes(trim($_POST['phone']))) : '';
				$fields['email'] = isset($_POST['email']) ?  strip_tags(stripslashes(trim($_POST['email']))) : '';
				$fields['address'] = isset($_POST['address']) ?  strip_tags(stripslashes(trim($_POST['address']))) : '';
				$fields['url'] = isset($_POST['url']) ?  $_POST['url'] : '';
				$fields['mobile'] = isset($_POST['mobile']) ? strip_tags(stripslashes(trim($_POST['mobile']))) : '';
				$fields['fBook'] = isset($_POST['fBook']) ?  $_POST['fBook'] : '';
				$fields['twitter'] = isset($_POST['twitter']) ?  $_POST['twitter'] : '';
				$fields['note'] = isset($_POST['note']) ? strip_tags(stripslashes(trim($_POST['note']))) : '';
				
				$file = isset($_FILES['logo']) ?  $_FILES['logo'] : array();
				
				if( empty($fields['cName']) ) {
					$errors[] = 'Company Name is needed come on Aaron! you can\'t add a company without a name!';
				}//end if
				if( empty($fields['fName']) ) {
					$errors[] = 'First Name field can not be empty';
				}//end if
				if( empty($fields['lName']) ) {
					$errors[] = 'Last Name field can not be empty';
				}//end if
				if( empty($fields['phone']) ) {
					$errors[] = 'Phone field can not be empty';
				}//end if
				if( !is_numeric($fields['phone']) ) {
					$errors[] = 'Phone field must be all numbers';
				}
				if( empty($fields['email']) ){
					$errors[] = 'Email field can not be empty';
				}
				if( empty($fields['url']) ) {
					$errors[] = 'Website field can not be empty';
				}
				print_r ($errors);
				if(empty($errors) ) {
				//print 'NO ERRORS!!!!!!!!!!!!!!!!!!!!<br />';
					$companyNameExists = false;
					
							print $fields['cName'];
							$cNameToLower = strtolower($fields['cName']);
							$check = $mysqli->query("SELECT * FROM contact_company_details");
							if($check){
								$returnCheck = $check->fetch_all(MYSQLI_ASSOC);
								//print 'Here comes the array';
								if($returnCheck) {
									print_r ($returnCheck);
									//$check->close();
										foreach( $returnCheck as $return ) {
											$i=0;
											$returnLower = strtolower($returnCheck[$i]['Company_Name']);
											//print $returnLower;
											//print '= Matches =' . $cNameToLower;
												if($returnLower == $cNameToLower){
												//print '<br /> MATCH!!!!!';
													$companyNameExists = true;
													$errors[] = 'Company Name already Exists, If you own this Name contact administrator @ \'911\'';
												}//end if
											++$i;
										}//end foreach
								}//end if true
							}//end if($check)
							if(!$companyNameExists) {
								//company name does not exist
								print '<br />company name does not EXIST!!!!!!';
								//insert as new contact
								$insertNewContact = $mysqli->prepare("INSERT INTO contacts (created, updated, type_id) VALUES(?,?,?)");
								if($insertNewContact) {
								print '<br />inside insert new contact with company name';
									$insertNewContact->bind_param('iii',$createdTime,$updatedTime,$isACompany);
									$insertNewContact->execute();
									$insertedContactID = $mysqli->insert_id;
									$insertNewContact->close();
								}//if insernewcontact is true
								//insert new company for the contact ID
								$insertNewCompany = $mysqli->prepare("INSERT into contact_company_details (Company_Name, Email, Telephone, Website, Facebook, Twitter, Address, Logo, Contact_ID) VALUES (?,?,?,?,?,?,?,?,?)");
								//bind params
								if($insertNewCompany) {
								print '<br />inside insert new company';
									$insertNewCompany->bind_param('ssssssssi',$fields['cName'], $fields['email'], $fields['phone'],$fields['url'],$fields['fBook'],$fields['twitter'],$fields['address'],$fields['photo'],$insertedContactID);
									$insertNewCompany->execute();
									$lastInsertCompany = $insertNewCompany->insert_id;
									print 'Error:'.$insertNewCompany->error.'!!!!!!!!!';
									$insertNewCompany->close();
								}//end if true
								if( !empty($file) ) {
									print_r ($file);

									if( file_exists($file['tmp_name']) ){
										//chmod($file['tmp_name'], 755);
										chmod(FILES_PATH, 755);
										
										$file_pieces = explode('.', $file['name']);
										$extension = array_pop($file_pieces);
										
										unset($file_pieces); //cleanup the namespace
										
										$name = uniqid(time() . '_', true);
										
										$destination = FILES_PATH . $name . '.' . $extension;
										$openDes = $name.'.'.$extension;
										//print $openDes;
										if( !move_uploaded_file($file['tmp_name'], $destination) ){
											$errors[] = 'Unable to move file to destination.';
										}
									} else {
										$openDes = 'defaultUser.jpg';
									}//set default picture
									if(empty($errors) ) {
										$insert = $mysqli->prepare("UPDATE contact_company_details SET Logo=? WHERE id=?");
										if( $insert ) {
					
											$insert->bind_param('sd',$openDes,$lastInsertCompany);
											$insert->execute();
										}//end if $insert
										$insert->close();													
									}//end if(empty($errors))
								}//end if ( !empty($file) )
								if( !empty($fields['note']) ) {
								//print '<br />inside field note is not empty!!';
								$insertNewNote = $mysqli->prepare("INSERT into contact_notes (Created,Updated,Contact_id,Message) Values(?,?,?,?)");
									if($insertNewNote) {
									//print 'note query passed';
										$insertNewNote->bind_param('iiis',$createdTime,$updatedTime,$insertedContactID,$fields['note']);
										$insertNewNote->execute();
										$insertNewNote->close();
									}
								}//end if isset($fields['note'])
							}//end if-else
				}//end if(empty($errors))
			?>
			<p><?php print_R ($_POST); ?></p>
		</div>
<?php endif;?>
<?php if( isset($_POST['editP']) ) : ?>
		<div style="width: 100%; background:lightgreen; color:white; text-align: center; padding: 10px 0;">
			<?php 
			$theId = $_GET['id'];
			print 'CONTACT successfuly updated!';
			//print_r($_POST);
			//print $theId;
			/*
			update compName/insert if was empty
			update img
			update fname
			update lname
			update phone
			update email
			update theAddress
			update url
			update theMobile
			update theFacebook
			update theTwitter
			update theLinkdIn
			update theNote/insert if was empty
			*/
			if(!empty($_POST['cName'] ) ) {
				$cName = $_POST['cName'];
				$selectCompany = $mysqli->query("SELECT * FROM contact_company_details WHERE id=$theId");
				if($selectCompany) {
					$mysqli->query("UPDATE contact_company_details SET Company_Name=$cName WHERE id=$theId ");
				}else {
					$mysqli->query("INSERT INTO contact_company_details (Company_Name) VALUES($cName)");
				}
				$mysqli->close();
			}
			if(!empty($_POST) ) {
				$firstN = $_POST['fName'];
				$mysqli->query("UPDATE contact_person_details SET First_Name='$firstN' WHERE Contact_ID=$theId");
				$lastN = $_POST['lName'];
				$mysqli->query("UPDATE contact_person_details SET Last_Name='$lastN' WHERE Contact_ID=$theId");
				$phone = $_POST['phone'];
				$mysqli->query("UPDATE contact_person_details SET Primary_Telephone='$phone' WHERE Contact_ID=$theId");
				$mobile = $_POST['mobile'];
				$mysqli->query("UPDATE contact_person_details SET Mobile_Telephone='$mobile' WHERE Contact_ID=$theId");
				
				$email = $_POST['email'];
				$mysqli->query("UPDATE contact_person_details SET Email='$email' WHERE Contact_ID=$theId");
				
				$address = $_POST['address'];
				$mysqli->query("UPDATE contact_person_details SET Address='$address' WHERE Contact_ID=$theId");
				
				$url = $_POST['url'];
				$mysqli->query("UPDATE contact_person_details SET Website='$url' WHERE Contact_ID=$theId");
				
				$fBook = $_POST['fBook'];
				$mysqli->query("UPDATE contact_person_details SET Facebook='$fBook' WHERE Contact_ID=$theId");
				
				$twitter = $_POST['twitter'];
				$mysqli->query("UPDATE contact_person_details SET Twitter='$twitter' WHERE Contact_ID=$theId");
				
				$linkdin = $_POST['linkedIn'];
				$mysqli->query("UPDATE contact_person_details SET LinkdIn='$linkdin' WHERE Contact_ID=$theId");
				
				$note = $_POST['note'];
				$mysqli->query("UPDATE contact_notes SET Message='$note' WHERE Contact_id=$theId");
				
			}
			
			
			?>
<?php if( isset($_POST['editC']) ) : ?>
		<div style="width: 100%; background:lightgreen; color:white; text-align: center; padding: 10px 0;">
			<?php 
			$theId = $_GET['id'];
			print 'COMPANY successfuly updated!';
			//print_r($_POST);
			//print $theId;
			/*
			update compName/insert if was empty
			update img
			update fname
			update lname
			update phone
			update email
			update theAddress
			update url
			update theMobile
			update theFacebook
			update theTwitter
			update theLinkdIn
			update theNote/insert if was empty
			*/
			if(!empty($_POST['cName'] ) ) {
				$cName = $_POST['cName'];
				$selectCompany = $mysqli->query("SELECT * FROM contact_company_details WHERE id=$theId");
				if($selectCompany) {
					$mysqli->query("UPDATE contact_company_details SET Company_Name=$cName WHERE id=$theId ");
				}else {
					$mysqli->query("INSERT INTO contact_company_details (Company_Name) VALUES($cName)");
				}
				$mysqli->close();
			}
			if(!empty($_POST) ) {
				$firstN = $_POST['fName'];
				if (!$mysqli->query("UPDATE contact_person_details SET First_Name='$firstN' WHERE Contact_ID=$theId") ) {
					$mysqli->query("INSERT INTO contact_person_details (First_Name,Contact_ID) VALUES('$firstN',$theId");
				}
				$lastN = $_POST['lName'];
				if( !$mysqli->query("UPDATE contact_person_details SET Last_Name='$lastN' WHERE Contact_ID=$theId") ) {
					$mysqli->query("INSERT INTO contact_person_details (Last_Name,Contact_ID) VALUES('$lastN',$theId");
				}
				$phone = $_POST['phone'];
				$mysqli->query("UPDATE contact_company_details SET Telephone='$phone' WHERE Contact_ID=$theId");
				
				$email = $_POST['email'];
				$mysqli->query("UPDATE contact_company_details SET Email='$email' WHERE Contact_ID=$theId");
				
				$address = $_POST['address'];
				$mysqli->query("UPDATE contact_company_details SET Address='$address' WHERE Contact_ID=$theId");
				
				$url = $_POST['url'];
				$mysqli->query("UPDATE contact_company_details SET Website='$url' WHERE Contact_ID=$theId");
				
				$fBook = $_POST['fBook'];
				$mysqli->query("UPDATE contact_company_details SET Facebook='$fBook' WHERE Contact_ID=$theId");
				
				$twitter = $_POST['twitter'];
				$mysqli->query("UPDATE contact_company_details SET Twitter='$twitter' WHERE Contact_ID=$theId");
				
				$note = $_POST['note'];
				$mysqli->query("UPDATE contact_notes SET Message='$note' WHERE Contact_id=$theId");
				
			}
			
			
			?>
			<br /><a href="-index.php"><button style="padding:10px;">Back to Main</button></a>
		</div>
<?php endif;?>
<div id="wrap">
<!-- IF MODE IS VIEW -->
<?php if( $_GET['mode'] == 'view') : ?>
<?php 
$theId = isset($_GET['id']) ? $_GET['id'] : '';
if(!empty($theId)) {
				//get all the others using contact id
				$getName = $mysqli->query("SELECT First_Name, Last_Name FROM contact_person_details WHERE Contact_ID=$theId");
				if($getName) {
					$row = $getName->fetch_row();
					$fname = $row[0];
					$lname = $row[1];
					$getName->close();
				}
				//print $name;
				$getCompName = $mysqli->query("SELECT Company_Name FROM contact_company_details WHERE Contact_ID=$theId");
				if($getCompName === true) {
					$compName = $getCompName->fetch_object()->Company_Name;
					$getCompName->close();
				} else { $compName = ''; }
				//print $compName;
}
 ?>
<?php if( $_GET['type'] == 'p') {?>
<!-- IF CONTACT IS PERSON/SINGULAR -->
<?php
$theId = isset($_GET['id']) ? $_GET['id'] : '';
if(!empty($theId)) {
				$getPhone = $mysqli->query("SELECT Primary_Telephone FROM contact_person_details WHERE Contact_ID=$theId");
				$phone = $getPhone->fetch_object()->Primary_Telephone;
				//print $phone;
				$getImg = $mysqli->query("SELECT Picture FROM contact_person_details WHERE Contact_ID=$theId");
				//print $theImg;
				if( $getImg === true) {
					$img = $getImg->fetch_object()->Picture;
					$getImg->close();
				} else {
					$img = 'defaultUser.jpg';
				}
				//print $img;
				$getEmail = $mysqli->query("SELECT Email FROM contact_person_details WHERE Contact_ID=$theId");
				$email = $getEmail ? $getEmail->fetch_object()->Email : '';
				if($email) {$getEmail->close();}
				//print $email;
				$getUrl = $mysqli->query("SELECT Website FROM contact_person_details WHERE Contact_ID=$theId");
				$url = $getUrl ? $getUrl->fetch_object()->Website : '';
				if($url) {$getUrl->close();}
				//print $url;
				//get address info
				$getAddress = $mysqli->query("SELECT Address FROM contact_person_details WHERE Contact_ID=$theId");
				if($getAddress == true) {
					$theAddress = $getAddress->fetch_object()->Address;
					$getAddress->close();
				} else { $theAddress = ''; }
				//get mobile info
				$getMobile = $mysqli->query("SELECT Mobile_Telephone FROM contact_person_details WHERE Contact_ID=$theId");
				if($getMobile == true) {
					$theMobile = $getMobile->fetch_object()->Mobile_Telephone;
					$getMobile->close();
				} else { $theMobile = ''; }
				//get social media info
				$getFacebook = $mysqli->query("SELECT Facebook FROM contact_person_details WHERE Contact_ID=$theId");
				if($getFacebook == true) {
					$theFacebook = $getFacebook->fetch_object()->Facebook;
					$getFacebook->close();
				} else { $theFacebook = ''; }
				$getTwitter = $mysqli->query("SELECT Twitter FROM contact_person_details WHERE Contact_ID=$theId");
				if($getTwitter == true) {
					$theTwitter = $getTwitter->fetch_object()->Twitter;
					$getTwitter->close();
				} else { $theTwitter = ''; }
				$getLinkdIn = $mysqli->query("SELECT LinkdIn FROM contact_person_details WHERE Contact_ID=$theId");
				if($getLinkdIn == true) {
					$theLinkdIn = $getLinkdIn->fetch_object()->LinkdIn;
					$getLinkdIn->close();
				} else { $theLinkdIn = ''; }
				//get the notes
				$getNote = $mysqli->query("SELECT Message FROM contact_notes WHERE Contact_ID=$theId");
				if($getNote == true) {
					$theNote = $getNote->fetch_object()->Message;
					$getNote->close();
				} else {
					$theNote = '';
				}
}
?>
		<h1>Edit Contact</h1>
		<div id="contactDetails">
			<form method="POST" enctype="multipart/form-data">
				<fieldset><legend>Company Name: <input type="text" name="cName" name="compName" value="<?php print $compName; ?>"/></legend>
				<img src="images/<?php print $img; ?>" height="180" width="300"/>
				
				<div id="upload">
					<label for="file">Photo:</label>
					<input type="file" name="photo" id="file" />
				</div><!--end upload div-->
				
				<div id="info">
					<h3 class="name"><small>First Name: *</small><input type="text" name="fName" value="<?php print $fname; ?>"/></h3>
					<h3 class="name"><small>Last Name: *</small><input type="text" name="lName" value="<?php print $lname; ?>"/></h3>
					<h4 class="contactInfo">Contact Info</h4>
					<p>Phone #: *<input type="text" name="phone" value="<?php print $phone; ?>" /></p>
					<p>E-mail: *<input type="text" name="email" value="<?php print $email; ?>"/></p>
				</div>
				<div id="additionInfo">
					<p>Address: <input style="width: 250px;" type="text" name="address" value="<?php print $theAddress; ?>"/></p>
					<p>Website: *<input style="width: 250px;" type="text" name="url" value="<?php print $url; ?>"/></p>
					<p>Mobile #: <input type="text" name="mobile" value="<?php print $theMobile; ?>"/></p>
					<p>Facebook: <input style="width: 250px;" type="text" name="fBook" value="<?php print $theFacebook; ?>"/></p>
					<p>Twitter: <input style="width: 250px;" type="text" name="twitter" value="<?php print $theTwitter; ?>"/></p>
					<p>LinkedIn: <input style="width: 250px;" type="text" name="linkedIn" value="<?php print $theLinkdIn; ?>"/></p>
					<p>Additional Notes: <br /><textarea name="note" rows="10" cols="40"><?php print $theNote; ?></textarea></p>
				</div>
				<input class="save" type="submit" name="editP" value="Save"/>
				</fieldset>
			</form>
		</div>
<?php }//end person if ?>
<?php if( $_GET['type'] == 'c') {?>
<!-- IF CONTACT IS Company/Plural -->
<?php
$theId = isset($_GET['id']) ? $_GET['id'] : '';
if(!empty($theId)) {
				$getPhone = $mysqli->query("SELECT Telephone FROM contact_company_details WHERE Contact_ID=$theId");
				$phone = $getPhone->fetch_object()->Telephone;
				//print $phone;
				$getImg = $mysqli->query("SELECT Logo FROM contact_company_details WHERE Contact_ID=$theId");
				//print $theImg;
				if( $getImg == true) {
					$img = $getImg->fetch_object()->Logo;
					$getImg->close();
				} else {
					$img = 'defaultUser.jpg';
				}
				//print $img;
				$getEmail = $mysqli->query("SELECT Email FROM contact_company_details WHERE Contact_ID=$theId");
				$email = $getEmail ? $getEmail->fetch_object()->Email : '';
				if($email) {$getEmail->close();}
				//print $email;
				$getUrl = $mysqli->query("SELECT Website FROM contact_company_details WHERE Contact_ID=$theId");
				$url = $getUrl ? $getUrl->fetch_object()->Website : '';
				if($url) {$getUrl->close();}
				//print $url;
				//get address info
				$getAddress = $mysqli->query("SELECT Address FROM contact_company_details WHERE Contact_ID=$theId");
				if($getAddress == true) {
					$theAddress = $getAddress->fetch_object()->Address;
					$getAddress->close();
				} else { $theAddress = ''; }
				//get social media info
				$getFacebook = $mysqli->query("SELECT Facebook FROM contact_company_details WHERE Contact_ID=$theId");
				if($getFacebook == true) {
					$theFacebook = $getFacebook->fetch_object()->Facebook;
					$getFacebook->close();
				} else { $theFacebook = ''; }
				$getTwitter = $mysqli->query("SELECT Twitter FROM contact_company_details WHERE Contact_ID=$theId");
				if($getTwitter == true) {
					$theTwitter = $getTwitter->fetch_object()->Twitter;
					$getTwitter->close();
				} else { $theTwitter = ''; }
				//get the notes
				$getNote = $mysqli->query("SELECT Message FROM contact_notes WHERE Contact_ID=$theId");
				if($getNote == true) {
					$theNote = $getNote->fetch_object()->Message;
					$getNote->close();
				} else {
					$theNote = '';
				}
}
?>
		<h1>Edit Company</h1>
		<div id="contactDetails">
			<form method="POST" enctype="multipart/form-data">
				<fieldset><legend>Company Name: <input type="text" name="cName" name="compName" value="<?php print $compName; ?>"/></legend>
				<img src="images/<?php print $img; ?>" height="180" width="300"/>
				
				<div id="upload">
					<label for="file">Photo:</label>
					<input type="file" name="photo" id="file" />
				</div><!--end upload div-->
				
				<div id="info">
					<h3 class="name"><small>First Name: *</small><input type="text" name="fName" value="<?php print $fname; ?>"/></h3>
					<h3 class="name"><small>Last Name: *</small><input type="text" name="lName" value="<?php print $lname; ?>"/></h3>
					<h4 class="contactInfo">Contact Info</h4>
					<p>Phone #: *<input type="text" name="phone" value="<?php print $phone; ?>" /></p>
					<p>E-mail: *<input type="text" name="email" value="<?php print $email; ?>"/></p>
				</div>
				<div id="additionInfo">
					<p>Address: <input style="width: 250px;" type="text" name="address" value="<?php print $theAddress; ?>"/></p>
					<p>Website: *<input style="width: 250px;" type="text" name="url" value="<?php print $url; ?>"/></p>
					<p>Facebook: <input style="width: 250px;" type="text" name="fBook" value="<?php print $theFacebook; ?>"/></p>
					<p>Twitter: <input style="width: 250px;" type="text" name="twitter" value="<?php print $theTwitter; ?>"/></p>
					<p>Additional Notes: <br /><textarea name="note" rows="10" cols="40"><?php print $theNote; ?></textarea></p>
				</div>
				<input class="save" type="submit" name="editC" value="Save"/>
				</fieldset>
			</form>
		</div>
<?php }//end person if ?>
<?php endif; ?>

<!-- IF MODE IS ADD -->
<?php if( $_GET['mode'] == 'add') : ?>
<?php if(!isset($_POST['submit'] ) ) :?>
<h1>Chose Contact Type</h1>
	<div id="contactDetails">
		<form method="POST" enctype="multipart/form-data">
		<fieldset><legend>Type: <select name="type">
			<option value="person">Person</option>
			<option value="company">Company</option>
		</select></legend>
		<input type="submit" class="submit" name="submit" value="submit" />
		</fieldset>
		</form>
		<?php if ( isset($_POST['addP']) || isset($_POST['addC']) ) {?>
		<p>Success!</p>
		<a href="-index.php"><button id="back">Back to Contacts</button></a>
		<?php } ?>
	</div>
</div>
<?php endif;?>
<?php if( isset($_POST['submit']) ) : ?>
<?php if( $_POST['type'] == 'person') {?>
<!-- IF CONTACT IS PERSON/SINGULAR -->
		<h1>Add Person</h1>
		<div id="contactDetails">
			<form method="POST" enctype="multipart/form-data">
				<fieldset><legend>Company Name: <input type="text" name="cName" name="compName" /></legend>
				<img src="" height="180" width="300"/>
				
				<div id="upload">
					<label for="file">Photo:</label>
					<input type="file" name="photo" id="file" />
				</div><!--end upload div-->
				
				<div id="info">
					<h3 class="name"><small>First Name: *</small><input type="text" name="fName"/></h3>
					<h3 class="name"><small>Last Name: *</small><input type="text" name="lName"/></h3>
					<h4 class="contactInfo">Contact Info</h4>
					<p>Phone #: *<input type="text" name="phone" /></p>
					<p>E-mail: *<input type="text" name="email"/></p>
				</div>
				<div id="additionInfo">
					<p>Address: <input style="width: 250px;" type="text" name="address"/></p>
					<p>Website: *<input style="width: 250px;" type="text" name="url"/></p>
					<p>Mobile #: <input type="text" name="mobile"/></p>
					<p>Facebook: <input style="width: 250px;" type="text" name="fBook"/></p>
					<p>Twitter: <input style="width: 250px;" type="text" name="twitter"/></p>
					<p>LinkedIn: <input style="width: 250px;" type="text" name="linkedIn"/></p>
					<p>Additional Notes: <br /><textarea name="note" rows="10" cols="40"></textarea></p>
				</div>
				<input class="save" type="submit" name="addP" value="Save"/>
				</fieldset>
			</form>
		</div>
<?php }//end person if ?>
<?php if( $_POST['type'] == 'company' ) {?>
<h1>Add a Company</h1>
		<div id="contactDetails">
			<form method="POST" enctype="multipart/form-data">
				<fieldset><legend>Company Name: *<input type="text" name="cName" /></legend>
				<img src="" height="180" width="300"/>
				
				<div id="upload">
					<label for="file">Company Logo:</label>
					<input type="file" name="logo" id="file" />
				</div><!--end upload div-->
				
				<div id="info">
					<h3 class="name"><small>First Name: *</small><input type="text" name="fName"/></h3>
					<h3 class="name"><small>Last Name: *</small><input type="text" name="lName"/></h3>
					<h4 class="contactInfo">Contact Info</h4>
					<p>Phone #: *<input type="text" name="phone" /></p>
					<p>E-mail: *<input type="text" name="email" /></p>
				</div>
				<div id="additionInfo">
					<p>Address: <input style="width: 250px;" type="text" name="address"/></p>
					<p>Website: *<input style="width: 250px;" type="text" name="url"/></p>
					<p>Mobile #: <input type="text" name="mobile"/></p>
					<p>Facebook: <input style="width: 250px;" type="text" name="fBook"/></p>
					<p>Twitter: <input style="width: 250px;" type="text" name="twitter"/></p>
					<p>Additional Notes: <br /><textarea name="note" rows="10" cols="40"></textarea></p>
				</div>
				<input class="save" type="submit" name="addC" value="Save"/>
				</fieldset>
			</form>
		</div>

<?php }//end company if ?>
<?php endif; ?>
<?php endif; ?>
	</div><!--end wrap class-->
	
	<footer id="footer">
		<p><em>&copy Copyright Jeremy Mills</em></p>
	</footer>
</body>
</html>
