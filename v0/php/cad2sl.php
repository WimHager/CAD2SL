<h3>x3d upload interface for CAD2SL</h3>

<?php 
//Edit this part to your needs========================================== 
 
$location=  "uploads/";			//Upload directory, must have write rights
$allowed=   array ("x3d", "X3D"); 	// Allowed extensions
$max_size=  1500;			//Max. File size in bytes /1024 = kb /1024 = mb
$youresite= "http://yoursite.com/"; 	// Must end with trailing slash.
$fileperm=  0444;
 
//End of part===========================================================
 
if(isset($_POST['upload'])) 
{
	if(is_uploaded_file($_FILES['file']['tmp_name'])) 
	{
		$extention_file = pathinfo($_FILES['file']['name']);
        	$extention_file = $extention_file[extension];

 		$extentions_allowed = explode(", ", $allowed);
 	
		$ok = in_array($extention_file, $allowed);
 	
		if($ok == 1)
		{		
			if($_FILES['file']['size'] > $max_size)
			{
				echo "File is too big, Max. file size is: <b>".$max_size."</b>";
				exit;
			}
		
			if(!move_uploaded_file($_FILES['file']['tmp_name'],$location.$_FILES['file']['name'])) 
			{
				echo "File cannot be placed";
				exit;
			}

			chmod($location . $_FILES['file']['name'], $fileperm);
			echo "File: ".$_FILES['file']['name']." is uploaded<br />";				
		}
		else
		{
			echo "Wrong extention, allowd extentions are:<b>";

			for ( $i = 0; $i < count($allowed); $i ++) 
			{
				echo " ." . $allowed[$i];
			}
			
			echo "</b>";
		}
	}

	else
	{
		echo "Upload has failed!!!";
	}
}
?>
<br />
<form method="post" action="" enctype="multipart/form-data">
<input type="file" name="file" /><br />
<input type="submit" name="upload" value="Uploaden!" />
</form>
