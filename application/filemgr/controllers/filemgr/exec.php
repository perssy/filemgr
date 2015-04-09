<?php 
if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Exec extends CI_Controller {
	
	private $post_data = '';
	private $file_path = '';
	private $ret_path = '';
	private $ret_arr = array();
	private $ret_json = '';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->post_data = addslashes( trim( file_get_contents( 'php://input' ) ) );
		
		if ( $this->post_data === '' )
		{
			return;
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
				
				$this->file_path = str_replace( "\\" , '/' ,$this->file_path );
				
				if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
				{
					$this->file_path = mb_convert_encoding( $this->file_path , $this->config->item( 'encode_native' , 'filemgr' ) , $this->config->item( 'encode_web' , 'filemgr' ) );
				}
				
				if( ! is_dir( $this->file_path ) )
				{
					if( file_exists( $this->file_path ) )
					{
						try{
							$this->ret_path = str_replace( str_replace( "\\" , '/' , FCPATH ) , str_replace( "\\" , '/' , $this->config->item( 'base_url' ) ) , $this->file_path );
							//$this->ret_arr = array( 'msg' => "Success" , 'code' => 200 , 'url' => /*$this->config->item( 'base_url' ) .*/ $this->file_path);
							$this->ret_arr = array( 'msg' => "Success" , 'code' => 200 , 'url' => $this->ret_path );
						}catch( Exception $e )
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
		return;
	}
}