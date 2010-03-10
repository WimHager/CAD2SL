<?php
//verander de onderstaande gegevens
 
$location="uploads/"; //of een andere map, vergeet niet de w-rechten
$allowed = array ("x3d", "X3D"); // extensies die toegestaan zijn
$max_size = 1500; //maximale grootte van het file in bytes /1024 = kb /1024 = mb
$youresite = "http://tuxed.nl/"; // eindigent op een slash
$fileperm = 0444;
 
//stop met veranderen
 
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
                echo "File is too big, max. file size is: <b>".$max_size."</b>";
                exit;
            }
 
            if(!move_uploaded_file($_FILES['file']['tmp_name'],$location.$_FILES['file']['name']))
            {
                echo "File cann't be placed";
                exit;
            }
 	    chmod($location . $_FILES['file']['name'], $fileperm);
            echo "File ".$_FILES['file']['name']." is uploaded<br /><a href='".$location.$_FILES['file']['name']."' target='_blank'>click here to view the file</a><br />The link is : ". $youresite . $location .$_FILES['file']['name'];
        }
        else
        {
            echo "Wrong extention, allowd extentions are: <b>".$allowed[0].' '.$allowed[1]."</b>";
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

