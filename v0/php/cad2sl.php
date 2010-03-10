<?

//todo: filter on SL servers only

// Only works with PHP compiled as an Apache module
//$headers = apache_request_headers();
$headers = request_headers();

$objectName = $headers["X-SecondLife-Object-Name"];
$objectKey  = $headers["X-SecondLife-Object-Key"];
$ownerKey   = $headers["X-SecondLife-Owner-Key"];
$ownerName  = $headers["X-SecondLife-Owner-Name"];
$region     = $headers["X-SecondLife-Region"];

// get things from $_POST[]
$name	= $_POST["name"]; 	//file name
$primnr	= $_POST["primnr"];	//prim number we want to read
$data	= $_POST["data"];	//prim data

//if (strlen($func) < 3) {
//	echo "Wrong SHA1 KEY, Action logged !!"; 
//	exit;
//}

$data= load_data($name);
$hash= md5($data[$primnr].':'.$nonce);
echo "&md5=".$hash."&data=".$data[$primnr]."&total=".count($data); //Don't change order !!!


// Functions block =============================================================================

function write_log($filename, $message) {
    $fp = fopen($filename, "a+");
    fwrite($fp, date("[Y/m/d-H:i:s];").$message."\n");
    fclose($fp);
}

function load_data($filename) {
    $content= file_get_contents($filename);
    $content= trim($content, "\n");	
    $content= explode("\n", $content);
    return($content);
}

function save_data($filename, $data) {
    file_put_contents($filename, $data);
}

function request_headers() { // Replacement if apache_request_headers not exist 
    foreach($_SERVER as $name => $value)
    if(substr($name, 0, 5) == 'HTTP_')
        $headers[str_replace('X-Secondlife-', 'X-SecondLife-', str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))))] = $value;
    return $headers;
}


?> 
