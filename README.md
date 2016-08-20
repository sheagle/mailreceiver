# mailreceiver
receive by imap
____________________________________________________________
Settings for gmail :
turn off 2-step verification or use app password

turn on Allow less secure apps from this url : https://myaccount.google.com/security#connectedapps
____________________________________________________________
set_time_limit(0);

require_once('cls_email.php');

$imap = new email;

$imap->addressemail = "imap.gmail.com/imap";

$imap->portemail = "993";

$imap->ssl = true;

$imap->useremail = "username@gmail.com";

$imap->passemail = "your password";

$imap->imapopen();

`` programmed by : shahin mohseni ``
