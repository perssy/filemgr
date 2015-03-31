<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Del extends CI_Controller {
	
	private $post_data = '';
	private $path_info = array();
	private $file_path_folder = '';
	private $file_path_encoded = '';
	private $file_list = array();
	private $ret_arr = array();
	private $ret_json = '';
	private $encodings = array( 'UTF-8' , 'GBK' , 'ASCII' , 'CP936' );
	public function __construct()
	{
		parent::__construct();
		//$this->load->helper( 'file' );
		$this->post_data = addslashes( trim( file_get_contents( 'php://input' ) ) );
		if ( $this->post_data == '' )
		{
			exit;
		}
		$this->config->load( 'filemgr' , true );
		$this->load->helper( 'directory' );
	}
	
	public function index()//cannot deal with filename containing utf-8 characters
	{
		if ( $this->post_data != '' )
		{
			if ( $this->post_data != '..' && substr( $this->post_data , -2 ) != '..' )
			{
				$find_method = $this->config->item( 'find_method' , 'filemgr' );
				if (  $find_method == 1 )
				{
					//$this->post_data = my_find_path( $this->post_data , APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' );
					
					$this->path_info = pathinfo( $this->post_data );
					
					//$this->post_data = $this->path_info['dirname'] . urlencode( $this->path_info['basename'] );
					
					//$this->file_path = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
					
					$this->file_path_folder = $this->path_info['dirname'];
					
					$this->file_list = scandir( $this->file_path_folder );
					
					foreach ( $this->file_list as $val )//find corrsponding file name
					{
						if ( $val == '.' || $val == '..' )
						{
							continue;
						}
						
						if ( $this->path_info['basename'] == mb_convert_encoding( $val , 'UTF-8' , mb_detect_encoding( $val , $this->encodings ) ) )
						{//matched
							$this->file_path_encoded = mb_convert_encoding( $this->file_path_folder , mb_detect_encoding( $val , $this->encodings ) , 'UTF-8' ) . $val ;
							//unlink(mb_convert_encoding($this->file_path,mb_detect_encoding($val,$this->encodings),'utf-8').$val);
							break;
						}
					}
				}
				else if ( $find_method == 3 )
				{
					$this->post_data = my_find_path( $this->post_data , APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' );
					
					$this->file_path_encoded = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
				}
				else
				{
					$this->file_path_encoded = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
				}
				//$this->file_path = utf8_decode($this->file_path);
				
				//$this->file_path = urlencode( $this->file_path );
				//$this->file_path = mb_convert_encoding( $this->file_path , 'ASCII' , mb_detect_encoding( $this->file_path , $encodelist ) );
				
				if( ! is_dir( $this->file_path_encoded ) )
				{
					if( file_exists( $this->file_path_encoded ) )
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
					$this->file_list = scandir( $this->file_path_encoded );
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
						$this->ret_arr = array( 'msg' => 'Folders cannot be deleted!' , 'code' => 500 );
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
	
	/*public function edit()
	{
		$this->post_data = addslashes( trim( file_get_contents( 'php://input' ) ) );
		if ( $this->post_data != '' )
		{
			if ( $this->post_data != '..' && substr( $this->post_data , -2 ) != '..' )
			{
				$this->file_path = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->post_data;
				if( ! is_dir( $this->file_path ) )
				{
					if( file_exists( $this->file_path ) )
					{
						try{
							
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
					$this->ret_arr = array( 'msg' => 'Folders cannot be edited!' , 'code' => 500 );
				}
			}
			else
			{
				$this->ret_arr = array( 'msg' => 'Parent folder cannot be edited!' , 'code' => 500 );
			}
		}
		else
		{
			$this->ret_arr = array( 'msg' => 'Invalid parameters!' , 'code' => 500 );
		}
		
		$this->ret_json = json_encode( $this->ret_arr );
		
		echo $this->ret_json;
		exit;
	}*/
}