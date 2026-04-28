<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Controller extends CI_Controller
{
    function __construct ()
    {
        parent::__construct();        
        if($this->config->item('mod_rewrite') == 'Off') {
			define('MOD_VALUE', 'index.php/');
		} else {
			define('MOD_VALUE', '');
		}
    }   
}