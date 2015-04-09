<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Index extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function redirect()
	{
		header( 'Location:' . $this->config->item( 'base_url' ) . $this->config->item( 'index_page' ) . '/filemgr/index/explore');
		return;
	}
}