<html><body>
<h3>x3d upload interface for CAD2SL</h3>

<?php
 
$location="uploads/"; //map where the file will be uploaded, don't forget to change the map permissions!
$allowed = array ("x3d", "X3D"); //witch extentions are allowed to uploaded
$max_size = 2048; //max size of the file in byte 
$youresite = "http://www.youredomein.com/"; //web adress always ending with a /
$fileperm = 0444; //file permissions the uploaded file will get after uploading
 
 
if(isset($_POST['upload'])) {
	if(is_uploaded_file($_FILES['file']['tmp_name'])) {
	$extention_file = pathinfo($_FILES['file']['name']);
        $extention_file = $extention_file[extension];
 	$extentions_allowed = explode(", ", $allowed);
 	$ok = in_array($extention_file, $allowed);
 	
	if($ok == 1) {
		if($_FILES['file']['size'] > $max_size) {
			echo "File is too big, max. file size is: <b>".$max_size."</b>";
			exit;
		}
		
		if(!move_uploaded_file($_FILES['file']['tmp_name'],$location.$_FILES['file']['name'])) {
			echo "File cann't be placed";
			exit;
		}
			
		chmod($location . $_FILES['file']['name'], $fileperm);

		echo "The file: ".$_FILES['file']['name']." is succesfully uploaded!<br />";
		//<a href='".$location.$_FILES['file']['name']."' target='_blank'>click here to view the file</a><br />
		//The link is : ". $youresite . $location .$_FILES['file']['name'];
		//sleep(10);
				
		}

		else {
			echo "Wrong extention, allowd extentions are:<b>";
			for ( $i = 0; $i < count($allowed); $i ++) {
				echo " ." . $allowed[$i];
			}
			echo "</b>";
			//echo "Wrong extention, allowd extentions are: <b>".$allowed[0].' '.$allowed[1]."</b>";
		}
	}

	else {
		echo "Upload has failed!!!";
	}
}

?>

<br />
<form method="post" action="" enctype="multipart/form-data">
<input type="file" name="file" /><br />
<input type="submit" name="upload" value="Uploaden!" />
</form>
</body></html>
