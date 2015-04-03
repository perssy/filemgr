<?php 
if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Exec extends CI_Controller {
	
	private $encodings = array( 'UTF-8' , 'GBK' , 'ASCII' , 'CP936' );
	public function __construct()
	{
		parent::__construct();
		
		$this->post_data = addslashes( trim( file_get_contents( 'php://input' ) ) );
		if ( $this->post_data === '' )
		{
			exit;
		}
		$this->config->load( 'filemgr' , true );
		$this->load->helper( 'directory' );
	}
	
	public function Index()
	{
		if ( $this->post_data !== '' )
		{
			if ( $this->post_data !== '..' && substr( $this->post_data , -2 ) !== '..' )
			{
				$find_method = $this->config->item( 'find_method' , 'filemgr' );
				
				if ( $find_method === 3 )
				{
					$this->post_data = my_find_path( $this->post_data , APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' );
					
					$this->file_path_encoded = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
				}
				else if (  $find_method === 1 )
				{
					$this->path_info = pathinfo( $this->post_data );
					
					$this->file_path_folder = $this->path_info['dirname'];
					
					$this->file_list = scandir( $this->file_path_folder );
					
					foreach ( $this->file_list as $val )//find corrsponding file name
					{
						if ( $val === '.' || $val === '..' )
						{
							continue;
						}
						
						if ( $this->path_info['basename'] === mb_convert_encoding( $val , 'UTF-8' , mb_detect_encoding( $val , $this->encodings ) ) )
						{
							$this->file_path_encoded = mb_convert_encoding( $this->file_path_folder , mb_detect_encoding( $val , $this->encodings ) , 'UTF-8' ) . $val ;

							break;
						}
					}
				}
				else
				{
					$this->file_path_encoded = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
				}
				
				if( ! is_dir( $this->file_path_encoded ) )
				{
					if( file_exists( $this->file_path_encoded ) )
					{
						try{
							/*unlink( $this->file_path );
							$this->ret_arr = array( 'msg' => 'Delete successfully!' , 'code' => 200 );*/
							$this->ret_arr = array( 'msg' => "Success" , 'code' => 200 , 'url' => $this->config->item( 'base_url' ) . $this->file_path_encoded);
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