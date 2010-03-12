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

function write_log($FileName, $Message) {
    $fp= fopen($FileName, "a+");
    fwrite($fp, date("[Y/m/d-H:i:s];").$Message."\n");
    fclose($fp);
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
	return "1=6|5=<".number_format($X,5,".","").",".number_format($Y,5,".","").",".number_format($Z,5,".","").",".number_format($S,5,".","").">";
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

// Get AllBoxes from X3D---------------------------------------------------
// Todo: make init array with defaults.
function GetBoxes($FileName) {
	$BoxArr= array();
	$ObjC= 0;
	$X3Data= simplexml_load_file($FileName);
	foreach ($X3Data->Scene as $Scene) {
		foreach ($Scene->Transform as $Transform) {
			$Pos= (string)$Transform[translation]; //Get Object Pos
			$Pos= explode(" ",$Pos); //Make it XYZ
			$BoxArr[$ObjC]["Pos"]= $Pos;

			$Rot= (string)$Transform[rotation];  //Get Oject Rotation
			$Rot= explode(" ",$Rot);  //Make it XYZ

			// Check if Rotation is in file else set defaults
			if(count($Rot) == 3) $BoxArr[$ObjC]["Rot"]= $Rot;
			else{ 
				$BoxArr[$ObjC]["Rot"][0]= 0;
				$BoxArr[$ObjC]["Rot"][1]= 0;
				$BoxArr[$ObjC]["Rot"][2]= 0;
			}

			foreach ($Transform->Shape as $Shape) {
				foreach ($Shape->Appearance as $Appearance) {
					foreach ($Appearance->Material as $Material) {
						$Attributes= get_object_vars($Material);

						//Need some investigation Why it uses DEF and USE labels!
						$Color= (string)$Attributes["@attributes"]["DEF"];
						if (empty($Color)) $Color= (string)$Attributes["@attributes"]["USE"];

						if (empty($Color)) $BoxArr[$ObjC]["Color"]= "None";
						else $BoxArr[$ObjC]["Color"]= $Color; //Get Object Color
					}	
				}
				foreach ($Shape->Box as $Box) {
					$Attributes= get_object_vars($Box); //Get Object Size
					$Size= (string)$Attributes["@attributes"]["size"];
					$Size= explode(" ",$Size);  //Make it XYZ
					$BoxArr[$ObjC]["Size"]= $Size; 
				}
			}
			$ObjC++;
		}
	}
	//print_r($BoxArr);
	return $BoxArr;
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


	$BoxArr= GetBoxes($FileN);
	$i= 0;
	$PrimParmStr= "";
	foreach ($BoxArr as $Parts) {
		$PrimParmStr=  AddBlockSize($BoxArr[$i]["Size"][0], $BoxArr[$i]["Size"][1], $BoxArr[$i]["Size"][2])."|"; //Size
		$PrimParmStr.= AddBlockPos ($BoxArr[$i]["Pos" ][0], $BoxArr[$i]["Pos" ][1], $BoxArr[$i]["Pos" ][2])."|"; //Pos
		$PrimParmStr.= AddBlockCol ($BoxArr[$i]["Color"]);  //Color
		$PrimParmStr= CountParms($PrimParmStr)."|".$PrimParmStr; // add objects count at begin
		$ObjStr.= $PrimParmStr."\n";
		$i++;
	}
	return $ObjStr;

}


// ------------------------------------------------------------------------


?>