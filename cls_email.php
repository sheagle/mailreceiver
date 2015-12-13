<?php
/**
 * Program Name : fetch email with imap
 * Programmer : shahin mohseni
 * website : www.shahinmohseni.ir
**/
class email
{
	var $useremail;
	var $passemail;
	var $addressemail;
	var $portemail;
	var $ssl = false;
	private $mbox;

	function imapopen()
	{
		$this->mbox = @imap_open("{".$this->addressemail.($this->ssl ? "/ssl" : "").":".$this->portemail."}INBOX",$this->useremail, $this->passemail);
		if(!$this->mbox)
		{
			echo "Can not Connect";
			exit;
		}
	}
	
	function imapclose()
	{
		if(!$this->mbox)
			return false;

		@imap_close($this->mbox);
	}
	
	function imapcount()
	{
		if(!$this->mbox)
			return false;

		$headers=imap_headers($this->mbox);
		return count($headers);
	}
	function get_mime_type(&$structure)
	{
		$primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

		if($structure->subtype)
		{
			return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
		}
		return "TEXT/PLAIN";
	}
	
	function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false)
	{
		$prefix = '';
		if(!$structure)
		{
			$structure = imap_fetchstructure($stream, $msg_number);
		}
		if($structure)
		{
			if($mime_type == $this->get_mime_type($structure))
			{
				if(!$part_number)
				{
					$part_number = "1";
				}
				$text = imap_fetchbody($stream, $msg_number, $part_number);
				if($structure->encoding == 3)
				{
					return imap_base64($text);
				}
				else if($structure->encoding == 4)
				{
					return imap_qprint($text);
				}
				else
				{
					return $text;
				}
			}
			if($structure->type == 1)
			{
				while(list($index, $sub_structure) = each($structure->parts))
				{
					if($part_number)
					{
						$prefix = $part_number . '.';
					}
					$data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
					if($data)
					{
						return $data;
					}
				}
			}
		}
		return false;
	}
	function imapbody($nummsg)
	{
		if(!$this->mbox)
			return false;

		$body = $this->get_part($this->mbox, $nummsg, "TEXT/HTML");
		if ($body == "")
			$body = $this->get_part($this->mbox, $nummsg, "TEXT/PLAIN");
		if ($body == "")
		{
			return "";
		}
		return $body;
	}
	function imapheader($nummsg)
	{
		if(!$this->mbox)
			return false;

		$mail_header=imap_headerinfo($this->mbox,$nummsg);
		$sender=$mail_header->from[0];
		$sender_replyto=$mail_header->reply_to[0];
		if(strtolower($sender->mailbox)!='mailer-daemon' && strtolower($sender->mailbox)!='postmaster')
		{
			if(isset($sender_replyto->mailbox))
			{
				$mailbox = strtolower($sender_replyto->mailbox).'@'.$sender_replyto->host;
			}
			else
			{
				$mailbox = '';
			}
			if(isset($sender_replyto->personal))
			{
				$mailpersonal = iconv_mime_decode($sender_replyto->personal,0,"UTF-8");
			}
			else
			{
				$mailpersonal = '';
			}
			if(isset($sender->personal))
			{
				$mailsenderpersonal = iconv_mime_decode($sender->personal,0,"UTF-8");
			}
			else
			{
				$mailsenderpersonal = '';
			}
			if(isset($mail_header->subject))
			{
				$mailsubject = iconv_mime_decode($mail_header->subject,0,"UTF-8");
			}
			else
			{
				$mailsubject = '';
			}
			$mail_details=array(
			'from'=>strtolower($sender->mailbox).'@'.$sender->host,
			'fromName'=>$mailsenderpersonal,
			'reply_to'=>$mailbox,
			'reply_toname'=>$mailpersonal,
			'subject'=>$mailsubject,
			'to'=>strtolower($mail_header->toaddress)
			);
		}
		return $mail_details;
	}
}

?>
