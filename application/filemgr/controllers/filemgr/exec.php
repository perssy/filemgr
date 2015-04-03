<?php 
if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Exec extends CI_Controller {
	
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
	
	public function Index()
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
							$this->ret_arr = array( 'msg' => "Success" , 'code' => 200 , 'url' => $this->config->item( 'base_url' ) . $this->file_path);
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
					$this->ret_arr = array( 'msg' => 'Folder cannot be executed!' , 'code' => 500 );
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
		exit;
	}
}