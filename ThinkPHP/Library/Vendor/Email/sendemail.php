<?php  
include_once("class.phpmailer.php");
function sendmail($emailto,$customer_name,$content,$from_email='service@lilysilk.com',$subject_content="lilysilk"){   
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->Host = "smtp.mailgun.org";
	$mail->SMTPAuth = true;
	$mail->Username = "service@lilysilk.com";
	$mail->Password = "cmd0147";
	$mail->Port = 25;        
	$mail->From = $from_email;   
	$mail->FromName = "lilysilk";
	$mail->AddAddress("$emailto",$customer_name);
	
	$mail->IsHTML(true);
	$mail->CharSet = "UTF-8";
	$mail->Subject = $subject_content;   
	$mail->Body = "$content";
	$mail->AltBody = "This is the body in plain text for non-HTML mail clients";  
	if(!$mail->Send())
	{
       if($mail->ErrorInfo)
	   {
	   		return false;
	   }
    } 
	else 
	{
        return true;
    }
		
}

?>
