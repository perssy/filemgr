<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Index extends CI_Controller {

	private $curr_dir = '';
	private $path_info = array();
	private $raw_path = '';
	private $real_path = '';
	private $full_path = '';
	private $temp_file = '';
	private $hasParent = false;
	private $file_list = array();
	private $all_list = array();
	private $dir_list = array();
	private $other_list = array();
	private $recid = 0;
	private $grid_src = '';
	private $gridlist = array();
	private $records = array();
	private $columns = array(
		array( 'field' => 'recid' , 'caption' => 'No.' , 'size' => '40px' , 'sortable' => true , 'attr' => 'align=center' , 'hidden' => true ),
		array( 'field' => 'filename' , 'caption' => 'Filename' , 'size' => '20%' , 'sortable' => true ),
		array( 'field' => 'type' , 'caption' => 'FileType' , 'size' => '15%' , 'sortable' => true ),
		array( 'field' => 'size' , 'caption' => 'FileSize' , 'size' => '20%' , 'sortable' => true ),
		array( 'field' => 'modifytime' , 'caption' => 'ModifyTime' , 'size' => '20%' , 'sortable' => false ),
		//array( 'field' => 'operation' , 'caption' => 'Operation' , 'size' => '250px' , 'sortable' => false ),
	);
	private $searches = array(
		array( 'field' => 'filename' , 'caption' => 'FileName' , 'type' => 'text' ),
		array( 'field' => 'type' , 'caption' => 'FileType' , 'type' => 'text' ),
	);
	private $sortData = array( 'field' => 'filename' ,  'direction' => 'ASC' );
	private $encodings = array( 'UTF-8' , 'GBK' , 'ASCII' , 'CP936' );
	
	public function __construct()
	{
		parent::__construct();
		$this->config->load( 'filemgr' , true );
		$this->load->helper( 'file' );
		$this->load->helper( 'directory' );
	}
	
	public function explore()
	{
		/*validate php files dir*/
		if ( $this->config->item( 'php_dir' , 'filemgr' ) != '' && is_dir( APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) ) )
		{
			$this->curr_dir = urldecode( implode( '/' , func_get_args() ) );
			
			if ( $this->curr_dir != '' )
			{
				$this->curr_dir .= '/';
			}
			//$this->path_info = pathinfo( $this->curr_dir );
			$this->raw_path = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->curr_dir;
			
			$this->raw_path = my_find_path( $this->raw_path );
			
			$this->path_info = pathinfo( $this->raw_path );
			
			$this->file_list = scandir( $this->path_info['dirname'] );
			
			foreach ( $this->file_list as $val )
			{
				if ( $this->path_info['basename'] == mb_convert_encoding( $val , 'UTF-8' , mb_detect_encoding( $val , $this->encodings ) ) )
				{//matched
					$this->real_path = mb_convert_encoding( $this->raw_path , mb_detect_encoding( $val , $this->encodings ) , 'UTF-8' );
					break;
				}
			}

			$this->file_list = array();
			//unset( $this->file_list );
			if ( $this->real_path == '' )
			{
				$this->real_path = $this->raw_path;
			}
			//$this->real_path = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->curr_dir;
			if ( ! is_dir( $this->real_path ) )
			{
				show_error( 'Incorrect directory!' );
			}
		}
		else
		{
			show_error( 'Invalid directory config for PHP files!' );
		}
		//echo '<pre>';
		//var_dump(get_dir_file_info($this->real_path));
		//var_dump(directory_map($this->real_path));
		//exit;
		//exit;
		/*list all files and directories*/
		$this->all_list = scandir( $this->real_path );
	
		foreach ( $this->all_list as $val )
		{
			if ( $val == '' || $val == '.' || $val == '..' )
			{
				continue;
			}
			
			//$val = mb_convert_encoding( $val , 'UTF-8' , mb_detect_encoding( $val , $this->encodings ) );
			
			$this->full_path = $this->real_path.'/'.$val;
			if ( is_dir( $this->full_path ) )
			{
				$newdir = array();
				$this->dir_list[$val] = $newdir;
			}
			else if ( is_file( $this->full_path ) )
			{
				$newfile = array(
					//'size' => ((filesize($full_path))/1024).' KB',
					//'type' => filetype($full_path),
					'modifytime' => filemtime( $this->full_path ),
				);
				$filesize = filesize( $this->full_path );
				if ( $filesize >= 1024 )
				{
					$newfile['size'] = round( $filesize / 1024 , 2 ) . ' KB';
				}
				else
				{
					$newfile['size'] = $filesize . ' Byte';
				}
				
				$this->file_list[$val] = $newfile;
			}
			else
			{
				$newotherfile = array();
				$this->other_list[$val] = $newotherfile;
			}
		}
		
		if ( $this->curr_dir != '' )//parent folder
		{
			$this->records[] = array(
				'recid' => '',
				'filename' => '<span class="dir" id="parentlink">..</span>',
				'size' => '',
				'type' => '',
				'modifytime' => '',
			);
			$this->hasParent = true;
		}
		else
		{
			$this->hasParent = false;
		}
		
		$this->gridlist = array(
			'name' => 'grid',
			'header' => 'List of Files',
			'show' => array(
				'header' => true,
				'toolbar' => true,
				'footer' => true,
				'lineNumbers' => true,
				'toolbarAdd' => true,
				'toolbarDelete' => true,
				'toolbarEdit' => true,
			),
			'toolbar' => array(
				'name' => 'operations',
				'items' => array(
					array( 'type' => 'break'),
					array( 'type' => 'button' , 'id' => 'parentbtn' , 'caption' => 'Parent' , 'img' => 'icon-back' , 'disabled' => !$this->hasParent),
					array( 'type' => 'button' , 'id' => 'exebtn' , 'caption' => 'Execute' , 'img' => 'icon-view' ),
					//array( 'type' => 'button' , 'id' => 'addbutton' , 'caption' => 'Add' , 'img' => 'icon-cross' ),
					//array( 'type' => 'button' , 'id' => 'editbutton' , 'caption' => 'Edit' , 'img' => 'icon-pencil' ),
				),
			),
			'columns' => array(),
			'searches' => array(),
			'sortData' => array(),
			'records' => array(),
		);
	
		/*if ( $this->curr_dir != '' )//parent folder
		{
			$this->records[] = array(
				'recid' => '',
				'filename' => '<span class="dir" id="parentlink">..</span>',
				'size' => '',
				'type' => '',
				'modifytime' => '',
			);
		}*/
		foreach ( $this->dir_list as $key => $val )
		{
			$convert_type = $this->config->item( 'convert_filename' , 'filemgr' );
			if ( $convert_type === 1 )
			{
				$dirname = utf8_decode( $key );
			}
			else if ( $convert_type === 2 )
			{
				$dirname = mb_convert_encoding( $key , 'UTF-8' , mb_detect_encoding( $key , $this->encodings ) );
			}
			else
			{
				$dirname = $key;
			}
			
			$this->recid++;
			$this->records[] = array(
				'recid' => $this->recid,
				'filename' => '<span class="dir">' . $dirname . '</span>',
				//'filename' => "<span class='dir'><img src='{$this->config->item( 'base_url' )}resource/png/images/folder'>{$key}</span>",
				//'filename' => "<span class='dir'><a href='{$this->config->item( 'base_url' )}filemgr/index/dir/{$key}'>{$key}</a></span>",
				'size' => '',
				'type' => 'Dir',
				'modifytime' => '',
				//'operation' => '',
			);
		}
		foreach ( $this->file_list as $key => $val )
		{
			$convert_type = $this->config->item( 'convert_filename' , 'filemgr' );
			if ( $convert_type === 1 )
			{
				$filename = utf8_decode( $key );
			}
			else if ( $convert_type === 2 )
			{
				$filename = mb_convert_encoding( $key , 'UTF-8' , mb_detect_encoding( $key , $this->encodings ) );
			}
			else
			{
				$filename = $key;
			}
			$this->recid++;
			$this->records[] = array(
				'recid' => $this->recid,
				'filename' => '<span class="file">' . $filename . '</span>',
				//'filename' => "<span class='file'><img src='{$this->config->item( 'base_url' )}resource/png/images/fileicon'>{$key}</span>",
				'size' => $val['size'],
				'type' => 'File',
				'modifytime' => gmdate( 'Y-m-d H:i:s' , $val['modifytime'] ),
				//'operation' => '',
			);
		}
		
		$this->gridlist['columns'] = $this->columns;
		$this->gridlist['searches'] = $this->searches;
		$this->gridlist['sortData'] = $this->sortData;
		$this->gridlist['records'] = $this->records;
		
		$this->grid_src = json_encode( $this->gridlist );
		//echo '<pre>';
		//var_dump($this);
		//exit;
		//echo '<pre>';
		//var_dump(ENVIRONMENT);
		//exit;
		//$this->load->library( 'javascript' );
		$this->load->view( 'filemgr/header' , array( 'title' => 'PHP manager  --  CodeIgniter ver 2.2.1' , 'grid_src' => $this->grid_src , 'curr_dir' => $this->curr_dir , 'environment' => ENVIRONMENT ) );
		//$this->load->view( 'filemgr/leftmenu' );
		$this->load->view( 'filemgr/index' , array( 'filelist' => $this->file_list ) );
		//$this->load->view( 'filemgr/footer' );
	}
}