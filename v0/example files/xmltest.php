<?php


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
			$BoxArr[$ObjC]["Rot"]= $Rot;
			foreach ($Transform->Shape as $Shape) {
				foreach ($Shape->Appearance as $Appearance) {
					foreach ($Appearance->Material as $Material) {
						$Attributes= get_object_vars($Material);
						$Color= (string)$Attributes["@attributes"]["DEF"];
						$BoxArr[$ObjC]["Color"]= $Color; //Get Object Color
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
	return $BoxArr;
}


print_r(GetBoxes('blokjes.x3d'));

?> 