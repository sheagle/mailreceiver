<?php
/**
 * Program Name : fetch email with imap
 * Programmer : shahin mohseni
 * website : www.shahinmohseni.ir
**/
set_time_limit(0);
require_once('cls_email.php');
$imap = new email;
$imap->addressemail = "imap.gmail.com/imap";
$imap->portemail = "993";
$imap->ssl = true;
$imap->useremail = "";
$imap->passemail = "";
$imap->imapopen();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Your Inbox</title>
</head>
<body>

<?php

$tot=$imap->imapcount();
for($i=$tot;$i>0;$i--)
{
	$head=$imap->imapheader($i);
	if($head['subject'] != ''){echo "Subjects :: ".$head['subject']."<br>";}else{echo "Subjects :: No Subject"."<br>";}
	echo "TO :: ".$head['to']."<br>";
	if($head['reply_to'] != ''){echo "Reply To :: ".$head['reply_to']."<br>";}
	if($head['reply_toname'] != ''){echo "Repty To Name :: ".$head['reply_toname']."<br>";}
	echo "From :: ".$head['from']."<br>";
	if($head['fromName'] != ''){echo "FromName :: ".$head['fromName']."<br>";}else{echo "FromName :: No From Name"."<br>";}
	echo "<br><br>";
	echo "<br>********************************************* **********************************************<BR> ";
	echo $imap->imapbody($i);
}
?>

</body>
<?php
$imap->imapclose();

?>
</html> 
