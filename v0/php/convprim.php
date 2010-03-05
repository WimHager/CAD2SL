<?php

include 'convprim-const.php';
include 'convprim-lib.php';


// Main
//==========================================================================

$PrimParmStr.= AddBlockSize(1.000000, 0.500000, 0.500000). "|";
$PrimParmStr.= AddBlockPos(255.000000, 70.000000, 1000.500000)."|";
$PrimParmStr.= AddBlockRot(0.000000, 0.000000, 0.000000, 1.000000."|");
$PrimParmStr.= AddTempOnRez(0)."|";
$PrimParmStr.= AddPhantom(0)."|";
$PrimParmStr.= AddPhysical(0)."|";
$PrimParmStr.= AddPrimMaterial(3);

$PrimParmStr= CountParms($PrimParmStr)."|".$PrimParmStr; // add objects counter at begin


echo $PrimParmStr. "     Written to: ". $PrimFile ;
WriteData($PrimParmStr);

//==========================================================================


?>