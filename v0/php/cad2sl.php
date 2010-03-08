<?

//todo: filter on SL servers only
//filter on name owner
//remove not used (old) entry's

function write_log($filename, $message) {
    $fp = fopen($filename, "a+");
    fwrite($fp, date("[Y/m/d-H:i:s];").$message."\n");
    fclose($fp);
}

function load_data($filename) {
    $content= file_get_contents($filename);
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


// Only works with PHP compiled as an Apache module
//$headers = apache_request_headers();
$headers = request_headers();

$objectName = $headers["X-SecondLife-Object-Name"];
$objectKey  = $headers["X-SecondLife-Object-Key"];
$ownerKey   = $headers["X-SecondLife-Owner-Key"];
$ownerName  = $headers["X-SecondLife-Owner-Name"];
$region     = $headers["X-SecondLife-Region"];

// get things from $_POST[]
$key	= $_POST["key"];
$func	= $_POST["func"];
$cryp	= $_POST["cryp"];
$data	= $_POST["data"];

if (strlen($func) < 3) {
	echo "Wrong SHA1 KEY, Action logged !!"; 
	exit;
}

$nonce = 0;
$hash = md5($data.':'.$nonce);

if ($func == "PUT") {
	if ($cryp == $hash) {
		save_data($key,$data);
		echo "Data ok.";
	}else{ echo "Received Corrupt Data!!"; }
}
if ($func == "GET") {
	$data = load_data($key);
	$hash = md5($data.':'.$nonce);
	echo "&cryp=".$hash."&data=".$data; //Don't change order !!!
}


?> 
