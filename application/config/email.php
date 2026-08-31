<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
/*
|--------------------------------------------------------------------------
| Configure email settings
|--------------------------------------------------------------------------
|
| These values are intentionally non-working placeholders. Replace them in
| an environment-specific configuration file when enabling email delivery.
|
*/
$config['protocol']  = 'smtp';
$config['smtp_host'] = 'ssl://smtp.example.com';
$config['smtp_port'] = '465';
$config['smtp_user'] = 'your-smtp-username@example.com';
$config['smtp_pass'] = 'replace-with-your-smtp-password';
$config['mailtype']  = 'html';
$config['wordwrap']  = TRUE;
$config['newline']   = "\r\n"; // Always use double quotes
