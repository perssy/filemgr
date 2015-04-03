<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Del extends CI_Controller {
	
	private $post_data = '';
	private $path_info = array();
	private $file_path = '';
	private $file_list = array();
	private $ret_arr = array();
	private $ret_json = '';
	public function __construct()
	{
		parent::__construct();
		
		$this->post_data = addslashes( trim( file_get_contents( 'php://input' ) ) );
		if ( $this->post_data === '' )
		{
			exit;
		}
		
		$this->config->load( 'filemgr' , true );
	}
	
	public function index()//cannot deal with filename containing utf-8 characters
	{
		if ( $this->post_data !== '' )
		{
			if ( $this->post_data !== '..' && substr( $this->post_data , -2 ) !== '..' )
			{
				$this->file_path = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
				
				if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
				{
					$this->file_path = mb_convert_encoding( $this->file_path , $this->config->item( 'encode_native' , 'filemgr' ) , $this->config->item( 'encode_web' , 'filemgr' ) );
				}
				
				if( ! is_dir( $this->file_path ) )
				{
					if( file_exists( $this->file_path ) )
					{
						try{
							/*unlink( $this->file_path );
							$this->ret_arr = array( 'msg' => 'Delete successfully!' , 'code' => 200 );*/
							$this->ret_arr = array( 'msg' => "Delete action not allowed!" , 'code' => 200 );
						}catch(Exception $e)
						{
							$this->ret_arr = array( 'msg' => $e , 'code' => 500 );
						}
					}
					else
					{
						$this->ret_arr = array( 'msg' => 'File not exists!' , 'code' => 404 );
					}
				}
				else
				{
					$this->file_list = scandir( $this->file_path );
					
					if ( version_compare( phpversion() , '5.4.0' , '>=' ) )
					{
						$this->file_list = array_diff( $this->file_list , [ '.' , '..' ] );
					}
					else
					{
						$this->file_list = array_diff( $this->file_list , array( '.' , '..' ) );
					}
					
					//if ( count( $this->file_list )  <= 2 && $this->file_list[0] == '.'  && $this->file_list[1] == '..' )
					if ( empty( $this->file_list ) )
					{
						/*rmdir( $this->file_path );
						$this->ret_arr = array( 'msg' => 'Delete successfully!' , 'code' => 200 );*/
						$this->ret_arr = array( 'msg' => 'Folders cannot be deleted!' , 'code' => 500 );
					}
					else
					{
						$this->ret_arr = array( 'msg' => 'Folder is not empty!' , 'code' => 500 );
					}
				}
			}
			else
			{
				$this->ret_arr = array( 'msg' => 'Parent folder cannot be deleted!' , 'code' => 500 );
			}
		}
		else
		{
			$this->ret_arr = array( 'msg' => 'Invalid parameters!' , 'code' => 500 );
		}
		
		$this->ret_json = json_encode( $this->ret_arr );
		
		echo $this->ret_json;
		exit;
	}
}