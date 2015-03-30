<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Save extends CI_Controller {
	
	private $encodings = array( 'UTF-8' , 'GBK' , 'ASCII' , 'CP936' );
	public function __construct()
	{
		parent::__construct();
		$this->config->load( 'filemgr' , true );
	}
}