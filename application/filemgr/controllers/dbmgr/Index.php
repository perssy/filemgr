<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Index extends CI_Controller {

	private $db_conn;
	private $curr_dir = '';
	private $real_path = '';
	private $full_path = '';
	private $curr_path = '';
	private $file_query;
	private $dir_query;
	private $temp_path = '';
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
		array( 'field' => 'size' , 'caption' => 'FileSize' , 'size' => '20%' , 'sortable' => false ),
		array( 'field' => 'modifytime' , 'caption' => 'ModifyTime' , 'size' => '20%' , 'sortable' => false ),
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
		
	}
	
	public function explore()
	{
		
		/*validate php files dir*/
		if ( $this->config->item( 'php_dir' , 'filemgr' ) !== '')
		{
			$this->curr_dir = urldecode( implode( '/' , func_get_args() ) );
			
			if ( $this->curr_dir !== '' )
			{
				$this->curr_dir = '/' . $this->curr_dir;
			}
			
			$this->file_path = $this->config->item( 'php_dir' , 'filemgr' ) . $this->curr_dir;
		}
		else
		{
			show_error( 'Invalid directory config for PHP files!' );
		}
		
		$this->db_conn = $this->load->database( 'filemgr_mysql' , true );
		
		$this->file_query = $this->db_conn->select( 'path, filename, type, length(content) as size, updatetime as modifytime' )
													->from( 'files' )
													->where( 'path' , $this->file_path )
													->get();
	
		$this->file_list = $this->file_query->result_array();
		
		if ( empty( $this->file_list ) || !is_array( $this->file_list ) )
		{
			show_error( 'No file found on this directory!' );
		}
		
		$this->curr_path = $this->config->item( 'php_dir' , 'filemgr' ) . $this->curr_dir . '/';
		$this->dir_query = $this->db_conn->distinct()
														->select( 'path' )
														->from( 'files' )
														->like( 'path' , $this->curr_path )
														->get();
		$this->path_list = $this->dir_query->result_array();
		foreach( $this->path_list as $val )
		{
			if ( strpos( $val['path'] , $this->curr_path ) !== false )
			{
				$this->temp_path = str_replace( $this->curr_path , '' , $val['path'] );
				if ( strstr( $this->temp_path , '/' ) === false && strstr( $this->temp_path , "\\" ) === false )
				{
					$this->dir_list[$this->temp_path] = array();
				}
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
				'toolbarDelete' => true,
			),
			'toolbar' => array(
				'name' => 'operations',
				'items' => array(
					array( 'type' => 'break'),
					array( 'type' => 'button' , 'id' => 'parentbtn' , 'caption' => 'Parent' , 'img' => 'icon-back' , 'disabled' => !$this->hasParent),
					array( 'type' => 'button' , 'id' => 'exebtn' , 'caption' => 'Execute' , 'img' => 'icon-exec' ),
					array( 'type' => 'button' , 'id' => 'viewbtn' , 'caption' => 'View' , 'img' => 'icon-view' ),
				),
			),
			'columns' => array(),
			'searches' => array(),
			'sortData' => array(),
			'records' => array(),
		);
	
		foreach ( $this->dir_list as $key => $val )
		{
			$this->recid++;
			$this->records[] = array(
				'recid' => $this->recid,
				'filename' => '<span class="dir">' . $key . '</span>',
				'size' => '',
				'type' => 'Dir',
				'modifytime' => '',
			);
		}
		foreach ( $this->file_list as $key => $val )
		{
			$this->recid++;
			if ( $val['size'] > 1024 )
			{
				$filesize = round( $val['size'] / 1024 , 2 ) . ' KB';
			}
			else
			{
				$filesize = $val['size'] . ' Byte';
			}
			$this->records[] = array(
				'recid' => $this->recid,
				'filename' => '<span class="file">' . $val['filename'] . '</span>',
				'size' => $filesize,
				'type' => 'File',
				'modifytime' => $val['modifytime'],
			);
		}
		
		$this->gridlist['columns'] = $this->columns;
		$this->gridlist['searches'] = $this->searches;
		$this->gridlist['sortData'] = $this->sortData;
		$this->gridlist['records'] = $this->records;
		
		$this->grid_src = json_encode( $this->gridlist );
		$this->load->view( 'filemgr/header' , array( 'title' => 'PHP manager  --  CodeIgniter ver 2.2.1' , 'grid_src' => $this->grid_src , 'curr_dir' => $this->curr_dir , 'environment' => ENVIRONMENT , 'curr_url' => $this->config->item( 'base_url' ) . $this->config->item( 'index_page' ) . '/dbmgr' ) );
		$this->load->view( 'filemgr/index' , array( 'filelist' => $this->file_list ) );
	}
}