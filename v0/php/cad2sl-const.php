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

$Debug= TRUE;

$Location=  "uploads/";			//Upload directory, must have write rights
$Allowed=   array ("x3d", "X3D"); 	//Allowed extensions for upload
$MaxSize=   1500;			//Max. File size in bytes /1024 = kb /1024 = mb
$FilePerm=  0444;
$OutputExt= "prim";			//Converted data extension
$LogFile=   "cad2sl.log";		//Name of debug log file	

//Color table
$Colors["Red"]=		"<1.00000,0.00000,0.00000>";
$Colors["Green"]=	"<0.00000,1.00000,0.00000>";
$Colors["Blue"]=	"<0.00000,0.00000,1.00000>";
$Colors["Yellow"]=	"<1.00000,1.00000,0.00000>";
$Colors["Cyan"]=	"<0.00000,1.00000,1.00000>";
$Colors["Purple"]=	"<1.00000,0.00000,1.00000>";
$Colors["Rust"]=	"<0.40000,0.20000,0.00000>";
$Colors["Black"]=	"<0.00000,0.00000,0.00000>";
$Colors["White"]=	"<1.00000,1.00000,1.00000>";
$Colors["Shiny_Red"]=	"<1.00000,0.00000,0.00000>"; // Shiny for now!! need with extra params
$Colors["Shiny_Green"]=	"<0.00000,1.00000,0.00000>";
$Colors["Shiny_Blue"]=	"<0.00000,0.00000,1.00000>";
$Colors["Shiny_Yellow"]="<1.00000,1.00000,0.00000>";
$Colors["Shiny_Cyan"]=	"<0.00000,1.00000,1.00000>";
$Colors["Shiny_Purple"]="<0.00000,1.00000,1.00000>";
$Colors["Shiny_Rust"]=	"<0.40000,0.20000,0.00000>";
$Colors["Shiny_Black"]=	"<0.00000,0.00000,0.00000>";
$Colors["Shiny_White"]=	"<1.00000,1.00000,1.00000>";
$Colors["None"]=	"<1.00000,1.00000,1.00000>";

?>
