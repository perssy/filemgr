<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Index extends CI_Controller {

	private $curr_dir = '';
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
	
	public function __construct()
	{
		parent::__construct();
		
		$this->config->load( 'filemgr' , true );
		$this->load->helper( 'file' );
	}
	
	public function explore()
	{
		/*validate php files dir*/
		if ( $this->config->item( 'php_dir' , 'filemgr' ) !== '' && is_dir( APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) ) )
		{
			$this->curr_dir = urldecode( implode( '/' , func_get_args() ) );
			
			if ( $this->curr_dir !== '' )
			{
				$this->curr_dir .= '/';
			}
			
			$this->file_path = APPPATH . $this->config->item( 'php_dir' , 'filemgr' ) . '/' . $this->curr_dir;
			
			if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
			{
				$this->file_path = mb_convert_encoding( $this->file_path  , $this->config->item( 'encode_native' , 'filemgr' ) , $this->config->item( 'encode_web' , 'filemgr' ) );
			}

			if ( ! is_dir( $this->file_path ) )
			{
				show_error( 'Incorrect directory!' );
			}
		}
		else
		{
			show_error( 'Invalid directory config for PHP files!' );
		}
		
		/*list all files and directories*/
		$this->all_list = scandir( $this->file_path );
	
		foreach ( $this->all_list as $val )
		{
			if ( $val === '' || $val === '.' || $val === '..' )
			{
				continue;
			}
			
			//$val = mb_convert_encoding( $val , 'UTF-8' , mb_detect_encoding( $val , $this->encodings ) );
			
			$this->full_path = $this->file_path.'/'.$val;
			
			if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
			{
				$this->temp_file = mb_convert_encoding( $val  , $this->config->item( 'encode_web' , 'filemgr' ) , $this->config->item( 'encode_native' , 'filemgr' ) );
			}
			else
			{
				$this->temp_file = $val;
			}
			
			if ( is_dir( $this->full_path ) )//dir list
			{
				$newdir = array();
				$this->dir_list[$this->temp_file] = $newdir;
			}
			else if ( is_file( $this->full_path ) )//file list
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
				
				$this->file_list[$this->temp_file] = $newfile;
			}
			else
			{
				$newotherfile = array();
				$this->other_list[$this->temp_file] = $newotherfile;
			}
		}
		
		if ( $this->curr_dir !== '' )//if current folder has parent folder
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
				//'toolbarAdd' => true,
				//'toolbarDelete' => true,
				'toolbarEdit' => true,
			),
			'toolbar' => array(
				'name' => 'operations',
				'items' => array(
					array( 'type' => 'break'),
					array( 'type' => 'button' , 'id' => 'parentbtn' , 'caption' => 'Parent' , 'img' => 'icon-back' , 'disabled' => !$this->hasParent),
					array( 'type' => 'button' , 'id' => 'exebtn' , 'caption' => 'Execute' , 'img' => 'icon-exec' ),
					array( 'type' => 'button' , 'id' => 'viewbtn' , 'caption' => 'View' , 'img' => 'icon-view' ),
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
			$this->recid++;
			$this->records[] = array(
				'recid' => $this->recid,
				'filename' => '<span class="dir">' . $key . '</span>',
				//'filename' => "<span class='dir'><img src='{$this->config->item( 'base_url' )}index.php/resource/png/images/folder'>{$key}</span>",
				//'filename' => "<span class='dir'><a href='{$this->config->item( 'base_url' )}index.php/filemgr/index/dir/{$key}'>{$key}</a></span>",
				'size' => '',
				'type' => 'Dir',
				'modifytime' => '',
				//'operation' => '',
			);
		}
		foreach ( $this->file_list as $key => $val )
		{
			$this->recid++;
			$this->records[] = array(
				'recid' => $this->recid,
				'filename' => '<span class="file">' . $key . '</span>',
				//'filename' => "<span class='file'><img src='{$this->config->item( 'base_url' )}index.php/resource/png/images/fileicon'>{$key}</span>",
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