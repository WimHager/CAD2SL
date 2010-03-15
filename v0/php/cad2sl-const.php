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

$location=  "uploads/";			//Upload directory, must have write rights
$allowed=   array ("x3d", "X3D"); 	//Allowed extensions for upload
$max_size=  1500;			//Max. File size in bytes /1024 = kb /1024 = mb
$youresite= "http://yoursite.com/"; 	//Must end with trailing slash.
$fileperm=  0444;
$OutputExt= "prim";			//Converted data extension
 

define("LogFile", "cad2sl.log>");

//Color table
$Colors["Red"]=		"<1.00000,0.00000,0.00000>";
$Colors["Green"]=	"<0.00000,1.00000,0.00000>";
$Colors["Blue"]=	"<0.00000,0.00000,1.00000>";
$Colors["Purple"]=	"<0.00000,1.00000,1.00000>";
$Colors["Yellow"]=	"<1.00000,1.00000,1.00000>";
$Colors["Black"]=	"<0.00000,0.00000,0.00000>";
$Colors["White"]=	"<1.00000,1.00000,1.00000>";
$Colors["None"]=	"<1.00000,1.00000,1.00000>";

?>