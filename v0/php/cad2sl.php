<?php

/*
    This file is part of CAD2SL.

    CAD2SL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    CAD2SL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with CAD2SL.  If not, see <http://www.gnu.org/licenses/>.
*/

include 'cad2sl-const.php';
include 'cad2sl-lib.php';

if($_SERVER['HTTP_HOST']) { //Are we started from prompt or web.

	if(isset($_POST['upload'])) {
		if(is_uploaded_file($_FILES['file']['tmp_name'])) {
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

				//Start to convert the file and write a converted file.
				$InF = 	$Location.$_FILES['file']['name'];
				$OutF =	preg_replace('/\..+$/', '.' . $GLOBALS['OutputExt'], $InF);

				WriteData(ConvInputFileToOutputStr($InF), $OutF);

				echo "File: ".$Location.$_FILES['file']['name']." is uploaded & converted.<br />";
		
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
}else{
	$InF= 	$argv[1]; //input file as command line input
	$OutF=	preg_replace('/\..+$/', '.' . $GLOBALS['OutputExt'], $InF);
	WriteData(ConvInputFileToOutputStr($InF), $OutF);
	echo "Conversion Completed\n";
	exit;
}
?>
<h3>x3d upload interface for CAD2SL</h3>
<br />
<form method="post" action="" enctype="multipart/form-data">
<input type="file" name="file" /><br />
<input type="submit" name="upload" value="Uploaden!" />
</form>
