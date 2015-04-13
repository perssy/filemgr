<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Bak extends CI_Controller {
	
	private $db_conn;
	private $walk_type;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->config->load( 'filemgr' , true );
		$this->config->load( 'dbmgr' , true );
		
		//$this->load->database( 'filemgr_mysql' );
		$this->load->helper( 'directory' );
		
		$this->db_conn = $this->load->database( 'filemgr_mysql' , true );
		
		$this->walk_type = $this->config->item( 'walk_type' , 'dbmgr' );
	}
	
	public function file2db()
	{
		if ( $this->walk_type === 1 )
		{
			$dir_map = directory_map( APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) );
			
			/*list all items and its full path in a array*/
			$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($dir_map), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $k => $v) {
				$indent = str_repeat('&nbsp;', 10 * $iterator->getDepth());
				// Not at end: show key only
				if ($iterator->hasChildren()) {
					echo "$indent$k :<br>";
				// At end: show key, value and path
				} else {
					for ($p = array(), $i = 0, $z = $iterator->getDepth(); $i <= $z; $i++) {
						$p[] = $iterator->getSubIterator($i)->key();
					}
					$path = implode(',', $p);
					echo "$indent$k : $v : path -> $path<br>";
				}
			}
		}
		else if ( $this->walk_type === 2 )
		{
			$dir_map = directory_map( APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) );
			//use an array as stack to store path
			return;
		}
		else if ( $this->walk_type === 3 )
		{
			$php_dir = $this->config->item( 'php_dir' , 'filemgr' );
			$dir_stack = array();
			$dir_map = array();
			if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
			{
				$php_dir = mb_convert_encoding( $php_dir  , $this->config->item( 'encode_native' , 'filemgr' ) , $this->config->item( 'encode_web' , 'filemgr' ) );
			}
			array_push( $dir_stack , $php_dir );
			while( true )
			{
				if ( empty( $dir_stack ) )
				{
					break;
				}
				$path = array_pop( $dir_stack );
				$temp_path = APPPATH . $path;
				$temp_path = str_replace( "\\" , '/' , $temp_path );
				
				$file_list = scandir( $temp_path );
				foreach( $file_list as $val )
				{
					$file_path = APPPATH . $path . '/' . trim( $val );
					//var_dump($val);
					if ( $val === '' || $val === '.' || $val === '..' )
					{
						continue;
					}
					if ( is_dir( $file_path ) )
					{
						array_push( $dir_stack , $path . '/' . $val );
						continue;
					}
					if ( is_file( $file_path ) )
					{
						$res = $path . '/' . $val;
						$dir_map[] = $res;
						continue;
					}
				}
			}
		}
		
		$sql = 'SELECT  id, path, filename FROM f_files WHERE path= ? AND filename= ?';
		foreach( $dir_map as $val )
		{
			$full_path = str_replace( "\\" , '/' , $val );
			$pos = strrpos( $full_path , '/' );
			$path = substr( $full_path , 0 , $pos );
			$filename = substr( $full_path , $pos+1 );
			
			$query = $this->db_conn->query( $sql , array( $path , $filename ) );
			if ( $query->num_rows() <= 0 )
			{
				$content = file_get_contents( APPPATH . $path . '/' . $filename );
				$modifytime = filemtime( APPPATH . $path . '/' . $filename );
				if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
				{
					//$content = mb_convert_encoding( $content  , $this->config->item( 'encode_web' , 'filemgr' ) , $this->config->item( 'encode_native' , 'filemgr' ) );
					$filename = mb_convert_encoding( $filename  , $this->config->item( 'encode_web' , 'filemgr' ) , $this->config->item( 'encode_native' , 'filemgr' ) );
					$path = mb_convert_encoding( $path  , $this->config->item( 'encode_web' , 'filemgr' ) , $this->config->item( 'encode_native' , 'filemgr' ) );
				}
				$newrecord = array(
					'type' => 1,
					'path' => $path,
					'filename' => $filename,
					'content' => $content,
					'encode' => $this->config->item( 'encode_native' , 'filemgr' ),
					'updatetime' => gmdate( 'Y-m-d H:i:s' , $modifytime ),
				);
				$this->db_conn->insert( 'files' , $newrecord );
			}
		}
	}
}