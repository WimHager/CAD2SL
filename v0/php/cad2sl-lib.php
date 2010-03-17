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


// FUNCTIONS BLOCK=========================================================

function WriteLog($Message) {
    if ($GLOBALS['Debug']) {
    	$Fp= fopen($GLOBALS['LogFile'], "a+");
    	fwrite($Fp, date("[Y/m/d-H:i:s];").$Message."\n");
    	fclose($Fp);
    }	
}

function CountParms($Str) {
	$Nr= count(explode("|",$Str));
	return $Nr;
}

function WriteData($Str,$FileN) {
	$Hash = md5($Str.':'.$nonce);
	file_put_contents($FileN,$Str);

}

// LSL Float is 5 digits !!!
// SetSize----------------------------------------------------------------
function AddBlockSize($X, $Y, $Z) {
	return "1=7|5=<".number_format($X,5,".","").",".number_format($Y,5,".","").",".number_format($Z,5,".","").">";
}
// -----------------------------------------------------------------------

// SetPos-----------------------------------------------------------------
function AddBlockPos($X, $Y, $Z) {
	return "1=6|5=<".number_format($X,5,".","").",".number_format($Y,5,".","").",".number_format($Z,5,".","").">";
}
// -----------------------------------------------------------------------

// SetRot-----------------------------------------------------------------
function AddBlockRot($X, $Y, $Z, $S) {
	return "1=8|6=<".number_format($X,5,".","").",".number_format($Y,5,".","").",".number_format($Z,5,".","").",".number_format($S,5,".","").">";
}
// -----------------------------------------------------------------------

// SetTempRez-------------------------------------------------------------
function AddTempOnRez($Enable) {
	return "1=4|1=".$Enable;
}
// -----------------------------------------------------------------------

// SetMaterial------------------------------------------------------------
// 0 stone	1 metal	2 glass	3 wood 	4 flesh	5 plastic 6 rubber 7 light
function AddPrimMaterial($Material) {
	return "1=2|1=".$Material;
}
// -----------------------------------------------------------------------

// SetColor-----------------------------------------------------------------
function AddBlockCol($Color) {
	global $Colors;
	return "1=18|1=-1|5=".$Colors[$Color]."|2=1.00000";
}
// -----------------------------------------------------------------------

// SetPhantom-------------------------------------------------------------
function AddPhantom($Enable) {
	return "1=5|1=".$Enable;
}
// -----------------------------------------------------------------------

// SetPhysical-------------------------------------------------------------
function AddPhysical($Enable) {
	return "1=3|1=".$Enable;
}
// ------------------------------------------------------------------------

// SetPrimType-------------------------------------------------------------
// 0 PRIM_TYPE_BOX 1 PRIM_TYPE_CYLINDER 2 PRIM_TYPE_PRISM 3 PRIM_TYPE_SPHERE

function AddPrimType($Type) {
	if ($Type == 0) $Param= "1=9|1=0|1=0|5=<0.000000,1.000000,0.000000>|2=0.000000|5=<0.000000,0.000000,0.000000>|5=<1.000000,1.000000,0.000000>|5=<0.000000,0.000000,0.000000>";
	if ($Type == 1) $Param= "1=9|1=1|1=1|5=<0.000000,1.000000,0.000000>|2=0.000000|5=<0.000000,0.000000,0.000000>|5=<1.000000,1.000000,0.000000>|5=<0.000000,0.000000,0.000000>";
	if ($Type == 2) $Param= "1=9|1=1|1=0|5=<0.000000,1.000000,0.000000>|2=0.000000|5=<0.000000,0.000000,0.000000>|5=<0.000000,0.000000,0.000000>|5=<0.000000,0.000000,0.000000>";
	if ($Type == 3)	$Param= "1=9|1=3|1=0|5=<0.000000,1.000000,0.000000>|2=0.000000|5=<0.000000,0.000000,0.000000>|5=<0.000000,1.000000,0.000000>";
  	return	$Param;
}
// ------------------------------------------------------------------------

// Get AllShapes from X3D---------------------------------------------------
// Todo: make init array with defaults.
function GetShapes($FileName) {
	$ShapeArr= array();
	$ObjC= 0;
	$X3Data= simplexml_load_file($FileName);
	if ($GLOBALS['DebugLevel'] == 2) WriteLog("X3Data arr: ".print_r($X3Data,true));
	foreach ($X3Data->Scene as $Scene) {
		foreach ($Scene->Transform as $Transform) {
			$Pos= (string)$Transform[translation]; //Get Object Pos
			$Pos= explode(" ",$Pos); //Make it XYZ
			$ShapeArr[$ObjC]["Pos"]= $Pos;

			$Rot= (string)$Transform[rotation];  //Get Oject Rotation
			$Rot= explode(" ",$Rot);  //Make it XYZ

			// Check if Rotation is in file else set defaults
			if(count($Rot) == 4) $ShapeArr[$ObjC]["Rot"]= $Rot;
			else{ 
				$ShapeArr[$ObjC]["Rot"][0]= 0;
				$ShapeArr[$ObjC]["Rot"][1]= 0;
				$ShapeArr[$ObjC]["Rot"][2]= 0;
				$ShapeArr[$ObjC]["Rot"][3]= 0;
			}

			foreach ($Transform->Shape as $Shape) {

				foreach ($Shape->Appearance as $Appearance) {
					foreach ($Appearance->Material as $Material) {
						$Attributes= get_object_vars($Material);

						//Need some investigation Why it uses DEF and USE labels!
						$Color= (string)$Attributes["@attributes"]["DEF"];
						if (empty($Color)) $Color= (string)$Attributes["@attributes"]["USE"];

						if (empty($Color)) $ShapeArr[$ObjC]["Color"]= "None";
						else $ShapeArr[$ObjC]["Color"]= $Color; //Get Object Color
					}	
				}

				foreach ($Shape->Box as $Box) {
					$ShapeArr[$ObjC]["Shape"]= "Box";
					$Attributes= get_object_vars($Box); //Get Object Size
					$Size= (string)$Attributes["@attributes"]["size"];
					$Size= explode(" ",$Size);  //Make it XYZ
					$ShapeArr[$ObjC]["Size"]= $Size; 
				}

				foreach ($Shape->Sphere as $Sphere) {
					$ShapeArr[$ObjC]["Shape"]= "Sphere";
					$Attributes= get_object_vars($Sphere); //Get Object Radius
					$Radius= (string)$Attributes["@attributes"]["radius"];
					$ShapeArr[$ObjC]["Radius"]= $Radius; 
				}

				foreach ($Shape->Cylinder as $Cylinder) {
					$ShapeArr[$ObjC]["Shape"]= "Cylinder";
					$Attributes= get_object_vars($Cylinder); //Get Object Radius / Height
					$Radius= (string)$Attributes["@attributes"]["radius"];
					$ShapeArr[$ObjC]["Radius"]= $Radius; 
					$Height= (string)$Attributes["@attributes"]["height"];
					$ShapeArr[$ObjC]["Height"]= $Height; 
				}

				foreach ($Shape->Cone as $Cone) {
					$ShapeArr[$ObjC]["Shape"]= "Cone";
					$Attributes= get_object_vars($Cone); //Get Object Radius / Height
					$Radius= (string)$Attributes["@attributes"]["bottomRadius"];
					$ShapeArr[$ObjC]["Radius"]= $Radius; 
					$Height= (string)$Attributes["@attributes"]["height"];
					$ShapeArr[$ObjC]["Height"]= $Height; 
				}

			}
			$ObjC++;
		}
	}
	if ($GLOBALS['DebugLevel'] == 2) WriteLog("Box arr: ".print_r($ShapeArr,true));
	return $ShapeArr;
}

function ConvInputFileToOutputStr($FileN) {
	//To do adding all primitve types
	//adding more primitive params
	//use separator = | !!!!!!!

	//$PrimParmStr.= AddBlockSize(1.000000, 0.500000, 0.500000). "|";
	//$PrimParmStr.= AddBlockPos(255.000000, 70.000000, 1000.500000)."|";
	//$PrimParmStr.= AddBlockRot(0.000000, 0.000000, 0.000000, 1.000000."|");
	//$PrimParmStr.= AddTempOnRez(0)."|";
	//$PrimParmStr.= AddPhantom(0)."|";
	//$PrimParmStr.= AddPhysical(0)."|";
	//$PrimParmStr.= AddPrimMaterial(3);
	//$PrimParmStr= CountParms($PrimParmStr)."|".$PrimParmStr; // add objects counter at begin


	$ShapeArr= GetShapes($FileN); //Get all Shape types from File
	$i= 0;
	$PrimParmStr= "";
	foreach ($ShapeArr as $Parts) {
		if ($ShapeArr[$i]["Shape"] == "Box") {
			$PrimParmStr=  AddPrimType (0)."|"; //BOX
			$PrimParmStr.= AddBlockSize($ShapeArr[$i]["Size"][0], $ShapeArr[$i]["Size"][1], $ShapeArr[$i]["Size"][2])."|"; //Size
		}
		if ($ShapeArr[$i]["Shape"] == "Sphere") {
			$PrimParmStr=  AddPrimType (3)."|"; //SPHERE
			$PrimParmStr.= AddBlockSize($ShapeArr[$i]["Radius"][0]*2, $ShapeArr[$i]["Radius"][0]*2, $ShapeArr[$i]["Radius"][0]*2)."|"; //Radius  SL uses Dia!!
		}
		if ($ShapeArr[$i]["Shape"] == "Cylinder") {
			$PrimParmStr=  AddPrimType (1)."|"; //CYLINDER
			$PrimParmStr.= AddBlockSize($ShapeArr[$i]["Radius"][0]*2, $ShapeArr[$i]["Radius"][0]*2, $ShapeArr[$i]["Height"][0])."|"; //Radius&Height SL uses Dia!!
		}
		if ($ShapeArr[$i]["Shape"] == "Cone") {
			$PrimParmStr=  AddPrimType (2)."|"; //CONE Be aware this is not a LSL Type !!!! due to fact that cone is a cylinder with X taper
			$PrimParmStr.= AddBlockSize($ShapeArr[$i]["Radius"][0]*2, $ShapeArr[$i]["Radius"][0]*2, $ShapeArr[$i]["Height"][0])."|"; //Radius&Height
		}
		$PrimParmStr.= AddBlockPos ($ShapeArr[$i]["Pos"][0], $ShapeArr[$i]["Pos"][1], $ShapeArr[$i]["Pos"][2])."|";    //Pos
		$PrimParmStr.= AddBlockCol ($ShapeArr[$i]["Color"])."|";  //Color
		$PrimParmStr.= AddBlockRot ($ShapeArr[$i]["Rot"][0], $ShapeArr[$i]["Rot"][1], $ShapeArr[$i]["Rot"][2], $ShapeArr[$i]["Rot"][3]); //Rot
		$PrimParmStr= CountParms($PrimParmStr)."|".$PrimParmStr; // add objects count at begin
		$ObjStr.= $PrimParmStr."\n";
		$i++;
	}
	WriteLog("LSL data: \n".$ObjStr);
	return $ObjStr;

}


// ------------------------------------------------------------------------


?>