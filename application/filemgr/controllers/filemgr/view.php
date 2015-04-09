<?php 
if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class View extends CI_Controller {
	
	private $post_data = '';
	private $file_path = '';
	private $file_content = '';
	private $file_output = '';
	private $encoding = '';
	private $encode_web = '';
	private $encode_native = '';
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
				
				if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
				{
					$this->file_path = mb_convert_encoding( $this->file_path , $this->config->item( 'encode_native' , 'filemgr' ) , $this->config->item( 'encode_web' , 'filemgr' ) );
				}
				
				if( ! is_dir( $this->file_path ) )
				{
					if( file_exists( $this->file_path ) )
					{
						try{
							$this->file_content = file_get_contents( $this->file_path );
							//ob_start();
							//print_r( $this->file_content );
							//$this->file_output = ob_get_clean();
							//$this->file_output = $this->file_content;
							
							$this->file_output = str_replace( "\xEF\xBB\xBF" , '' , $this->file_content );//remove BOM
							//$bom = pack('H*', 'EFBBBF');
							//$this->file_output = preg_replace("/^$bom/",'',$this->file_output);
							
							$this->encode_web = $this->config->item( 'encode_web' , 'filemgr' );
							$this->encode_native = $this->config->item( 'encode_native' , 'filemgr' );
							
							$this->encoding = mb_detect_encoding( $this->file_output , array( $this->encode_web , $this->encode_native ) );
							if ( $this->encoding != $this->encode_web )
							{
								$this->file_output = mb_convert_encoding( $this->file_output , $this->encode_web , $this->encoding );
							}
							
							$this->file_output = htmlspecialchars( $this->file_output );
							
							$this->ret_arr = array( 'msg' => "Success" , 'code' => 200 , 'content' => $this->file_output );
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
					$this->ret_arr = array(
						'url' => $this->config->item( 'base_url' ) . 'index.php/filemgr/index/explore/' . $this->post_data,
						'code' => 301 
					);
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