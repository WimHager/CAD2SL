<h3>x3d upload interface for CAD2SL</h3>

<?php 

include 'cad2sl-const.php';
include 'cad2sl-lib.php';
include 'cad2sl-conv.php';

if(isset($_POST['upload'])) 
{
	if(is_uploaded_file($_FILES['file']['tmp_name'])) 
	{
		$ExtentionFile= pathinfo($_FILES['file']['name']);
        	$ExtentionFile= $ExtentionFile[extension];

 		$ExtentionsAllowed= explode(", ", $Allowed);
 	
		$Ok= in_array($ExtentionFile, $Allowed);
 	
		if($Ok == 1) {		
			if($_FILES['file']['size'] > $MaxSize)	{
				echo "File is too big, Max. file size is: <b>".$MaxSize."</b>";
				exit;
			}
		
			if(!move_uploaded_file($_FILES['file']['tmp_name'],$Location.$_FILES['file']['name'])) {
				echo "File cannot be placed";
				exit;
			}

			chmod($Location . $_FILES['file']['name'], $FilePerm);
			echo "File: ".$_FILES['file']['name']." is uploaded<br />";				
		}else{
			echo "Wrong extention, allowd extentions are:<b>";

			for ( $I = 0; $I < count($Allowed); $I ++) {
				echo " ." . $Allowed[$I];
			}
			
			echo "</b>";
		}
	}else{
		echo "Upload has failed!!!";
	}
}
?>
<br />
<form method="post" action="" enctype="multipart/form-data">
<input type="file" name="file" /><br />
<input type="submit" name="upload" value="Uploaden!" />
</form>
