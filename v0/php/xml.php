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

$library= simplexml_load_file('blokjes.x3d');
//print_r($library); //Shows you what array are in it
foreach ($library->Scene as $Scene) {
	foreach ($Scene->Transform as $Transform) {
		echo "Pos: ".$Transform[translation]."  ";
		foreach ($Transform->Shape as $Shape) {
			foreach ($Shape->Appearance as $Appearance) {
				foreach ($Appearance->Material as $Material) {
					$attributes= get_object_vars($Material);
					$Color= $attributes["@attributes"]["DEF"];
					echo "Color: ".$Color."  ";
				}	
			}
			foreach ($Shape->Box as $Box) {
				$attributes= get_object_vars($Box); 
				$Size= $attributes["@attributes"]["size"];
				echo "Size: ".$Size."\n"; 
			}
		}
	}

}
?> 