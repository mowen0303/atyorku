<!DOCTYPE html>
<html>
<body>
<?php
$to = "mowen0303@gmail.com";
$subject = "主题";
$message = "Hello!,This is a simple email message.";
$from = "coder";
$headers = "From: $from";
if(mail($to,$subject,$message,$headers))
    echo " Mail sent.";
else echo " Mail fail.";
?>
</body>
</html>