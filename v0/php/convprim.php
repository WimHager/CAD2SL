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

$InF= 	"4wall.x3d";
$OutF=	preg_replace('/\..+$/', '.' . $GLOBALS[$OutputExt], $InF);

WriteData(ConvInputFileToOutputStr($InF), $OutF);

//==========================================================================


?>