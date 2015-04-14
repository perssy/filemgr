<?php 
if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class View extends CI_Controller {
	
	private $post_data = '';
	private $db_conn;
	private $file_query;
	private $file_res;
	private $dir_query;
	private $dir_res;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->post_data = addslashes( trim( file_get_contents( 'php://input' ) ) );
		
		if ( $this->post_data === '' )
		{
			return;
		}
		
		$this->config->load( 'filemgr' , true );
		
		$this->db_conn = $this->load->database( 'filemgr_mysql' , true );
	}
	
	public function Index()
	{
		if ( $this->post_data !== '' )
		{
			if ( $this->post_data !== '..' && substr( $this->post_data , -2 ) !== '..' )
			{
				$this->file_path = $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
				
				$this->pathinfo = pathinfo( $this->file_path );
				
				$this->file_query = $this->db_conn->select( 'type, content' )
																		->from( 'files' )
																		->where( 'path' , $this->pathinfo['dirname'] )
																		->where( 'filename' , $this->pathinfo['basename'] )
																		->get();
				
				if ( $this->file_query->num_rows() > 0 )
				{
					$this->file_res = $this->file_query->row_array();
					if ( $this->file_res['type'] == 0 )
					{
						$this->ret_arr = array(
							'url' => $this->config->item( 'base_url' ) . 'index.php/filemgr/index/explore/' . $this->post_data,
							'code' => 301 
						);
					}
					else
					{
						$this->ret_arr = array( 'msg' => 'Success' , 'code' => 200 , 'content' => $this->file_res['content'] );
					}
				}
				else
				{
					$this->dir_query = $this->db_conn->select( 'id' )
																			->from( 'files' )
																			->where( 'path' , $this->file_path )
																			->get();
					
					if ( $this->dir_query->num_rows() > 0 )//is not a dir
					{
						$this->ret_arr = array(
							'url' => $this->config->item( 'base_url' ) . 'index.php/filemgr/index/explore/' . $this->post_data,
							'code' => 301 
						);
					}
					else
					{
						$this->ret_arr = array( 'msg' => 'File not found!' , 'code' => 404 );
					}
				}
			}
			else
			{
				$this->ret_arr = array( 'msg' => 'Parent folder cannot be executed!' , 'code' => 500 );
			}
		}
		else
		{
			$this->ret_arr = array( 'msg' => 'Invalid parameters!' , 'code' => 500 );
		}
		
		$this->ret_json = json_encode( $this->ret_arr );
		
		echo $this->ret_json;
		return;
	}
}