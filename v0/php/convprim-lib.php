<?php


// FUNCTIONS BLOCK=========================================================

function CountParms($Str) {
	$Nr= count(explode("|",$Str));
	return $Nr;
}

function WriteData($Str) {
	$Hash = md5($Str.':'.$nonce);
	file_put_contents($GLOBALS['PrimFile'],$Str);

}


// SetSize----------------------------------------------------------------
function AddBlockSize($X, $Y, $Z) {
	return "1=7|5=<".number_format($X,6,".","").",".number_format($Y,6,".","").",".number_format($Z,6,".","").">";
}
// -----------------------------------------------------------------------

// SetPos-----------------------------------------------------------------
function AddBlockPos($X, $Y, $Z) {
	return "1=6|5=<".number_format($X,6,".","").",".number_format($Y,6,".","").",".number_format($Z,6,".","").">";
}
// -----------------------------------------------------------------------

// SetRot-----------------------------------------------------------------
function AddBlockRot($X, $Y, $Z, $S) {
	return "1=6|5=<".number_format($X,6,".","").",".number_format($Y,6,".","").",".number_format($Z,6,".","").",".number_format($S,6,".","").">";
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


?>