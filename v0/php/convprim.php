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


include 'convprim-const.php';
include 'convprim-lib.php';


// Main
//==========================================================================

$BoxArr= GetBoxes('blokjes.x3d');

Print_r($BoxArr);

$PrimParmStr.= AddBlockSize($BoxArr[0]["Size"][0], $BoxArr[0]["Size"][1], $BoxArr[0]["Size"][2]);

//$PrimParmStr.= AddBlockSize(1.000000, 0.500000, 0.500000). "|";
//$PrimParmStr.= AddBlockPos(255.000000, 70.000000, 1000.500000)."|";
//$PrimParmStr.= AddBlockRot(0.000000, 0.000000, 0.000000, 1.000000."|");
//$PrimParmStr.= AddTempOnRez(0)."|";
//$PrimParmStr.= AddPhantom(0)."|";
//$PrimParmStr.= AddPhysical(0)."|";
//$PrimParmStr.= AddPrimMaterial(3);

$PrimParmStr= CountParms($PrimParmStr)."|".$PrimParmStr; // add objects counter at begin


echo $PrimParmStr. "     Written to: ". $PrimFile ;
WriteData($PrimParmStr);

//==========================================================================


?>