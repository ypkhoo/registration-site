<?php
    ini_set("include_path", '/pathTo/yourPHPfolder/php:' . ini_get("include_path") );

    include('Mail.php'); 
    include('Mail/mime.php'); 
    	
    $servername = "localhost";
	$port = "3306";
	$dbuser =  "PROVIDE YOUR DB USERNAME";
	$userpw = "PROVIDE YOUR DB PASSWORD";
	$dbName = "PROVIDE YOUR DB NAME"

    $USERDATA = $_POST["userData"]; 
    $obj = json_decode($USERDATA); 
    
    function sendEmail($email, $pw) {
        $recipient = $email; 
    	$headers['From'] = 'PROVIDE THE EMAIL THAT IS UNDER YOUR DOMAIN'; 
    	$headers['To'] = $email; 
    	$headers['Subject'] = '[EVENT NAME] Registration Confirmation Email';
    	$crlf = "\r\n"; 
    	
    	$mime = new Mail_mime($crlf);
    	
    	$body = "Thanks for registering!"; 
        $message = "<p>Thank you for registering for COMPANY NAME's first ever virtual event: "; 
        $message .= "<br><b>TAGLINE</b></p>"; 
    	$message .= "<p><u>Details of the event:</u><br>";
    	$message .= "Event date: DAY MONTH YEAR<br>";
    	$message .= "Event time: STARTTIME pm - ENDTIME pm (the app is open for log-in at CERTAINTIME pm)<br><br>"; 
    	$message .= "Your unique password and instructions to join the virtual event will be shared to your email 7 days before the event.<br><br>"; 
    	$message .= "Stay tuned for more exciting news to come!</p>"; 
        $message .= "<p>EVENT COMPANY NAME, event coordinator"; 
        $message .= "<br>On behalf of COMPANY NAME</p>";
    	
    	$mime->setTXTBody($body); 
    	$mime->setHTMLBody($message); 
    	
    	$body = $mime->get(); 
    	$headers = $mime->headers($headers); 
    	
    	$params['sendmail_path'] = '/usr/lib/sendmail'; 
    	
    	$mail_object =& Mail::factory('sendmail', $params); 
    	$mail_object->send($recipient, $headers, $body); 
    }
    
    function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
    	$sets = array();
    	if(strpos($available_sets, 'l') !== false)
    		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
    	if(strpos($available_sets, 'u') !== false)
    		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    	if(strpos($available_sets, 'd') !== false)
    		$sets[] = '23456789';
    	if(strpos($available_sets, 's') !== false)
    		$sets[] = '!@#$%&*?';
    
    	$all = '';
    	$password = '';
    	foreach($sets as $set)
    	{
    		$password .= $set[array_rand(str_split($set))];
    		$all .= $set;
    	}
    
    	$all = str_split($all);
    	for($i = 0; $i < $length - count($sets); $i++)
    		$password .= $all[array_rand($all)];
    
    	$password = str_shuffle($password);
    
    	if(!$add_dashes)
    		return $password;
    
    	$dash_len = floor(sqrt($length));
    	$dash_str = '';
    	while(strlen($password) > $dash_len)
    	{
    		$dash_str .= substr($password, 0, $dash_len) . '-';
    		$password = substr($password, $dash_len);
    	}
    	$dash_str .= $password;
    	return $dash_str;
    }
    
    $pref = $obj->pref; 
    $name = $obj->name; 
    $phoneNumber = $obj->phoneNumber; 
    $email = $obj->email; 
    $address = $obj->address; 
    $specialty = $obj->specialty; 
    $institute = $obj->institute; 
    $mmcmdaID = $obj->mmcmdaID; 
    $hearAbout = $obj->hearAbout;
    $pw = generateStrongPassword();
    
    $conn = new mysqli($servername, $dbuser, $userpw, $dbName); 
    
    if(!$conn) 
    {
        die("Connection Failed: ".mysqli_connect_error()); 
        echo "CONNECTION FAILED"; 
    }
    
    $sql = "INSERT INTO `user_profile` (`userID`, `prefix`, `name`, `mobileNumber`, `emailAddress`, `address`, `specialty`, `institute`, `mmcmdaID`, `hearAbout`,`dateCreated`, `pword`) VALUES (NULL,'".$pref."','".$name."','".$phoneNumber."','".$email."', '".$address."','".$specialty."', '".$institute."', '".$mmcmdaID."',  '".$hearAbout."', CURRENT_TIMESTAMP, '".$pw."')"; 

    if (mysqli_query($conn, $sql)) {
        echo "New Record Created Successfully"; 
        sendEmail($email, $pw); 
    }
    else {
        echo "RECORD ERROR"; 
    }
    
    
    
    mysqli_close($conn); 

    header('Content-Type:application/json');
    $arr = ["path" => '123']; 
    echo json_encode($arr);  
?>