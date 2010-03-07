  <?php
  $library= simplexml_load_file('two.x3d');
  //print_r($library); //Shows you what array are in it
  foreach ($library->Scene as $Scene) {
	foreach ($Scene->Transform as $Transform) {
		foreach ($Transform->Shape as $Shape) {
			foreach ($Shape->Appearance as $Appearance) {
				foreach ($Appearance->Material as $Material) {
					$attributes= get_object_vars($Material); 
					print_r($attributes["@attributes"]["DEF"]);
				}	
			}
			foreach ($Shape->Box as $Box) {
				$attributes= get_object_vars($Box); 
				print_r($attributes["@attributes"]["size"]); 
			}
		}
	}
  }
  ?> 