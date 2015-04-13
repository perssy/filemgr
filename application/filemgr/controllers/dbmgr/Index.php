<?php 
if ( ! defined( 'BASEPATH' )) exit( 'No direct script access allowed' );

class Index extends CI_Controller {

	private $db_conn;
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
		array( 'field' => 'size' , 'caption' => 'FileSize' , 'size' => '20%' , 'sortable' => false ),
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
		//$this->load->database( 'filemgr_mysql' );
		$this->db_conn = $this->load->database( 'filemgr_mysql' , true );
	}
	
	public function explore()
	{
		//echo '<pre>';
		/*$query = $this->db->query( 'SELECT type,path,filename,content FROM f_files' );
		var_dump($query->row());
		if ( $query->num_rows() > 0 )
		{
			echo 'record exists';
		}*/
		
		/*$newline = array(
			'type' => 1,
			'path' => '',
			'filename' => 'new.file',
			'content' => 'aaa'
		);
		
		$this->db->insert('files',$newline);*/
			
		//$this->db_conn->select('type','path','content');
		//$this->db_conn->from('files');
		//$query = $this->db_conn->get();
		/*$sql = 'SELECT * FROM f_files WHERE type=?';
		$query = $this->db_conn->query($sql, array(1));
		var_dump($query->row_array());
		var_dump($query->next_row());
		var_dump($query->next_row());
		if ( ! $query=$this->db_conn->simple_query('SELECT `type` FROM `f_files`'))
		{
				$error = $this->db_conn->error(); // Has keys 'code' and 'message'
				var_dump($error);
		}
		else
		{
			var_dump($query);
		}*/
		//var_dump($this->db_conn->simple_query('select * from f_files'));
		
		/*$query = $this->db_conn->get_where( 'files' , array( 'id>0 and type<' => 2 ) , 1 );
		//var_dump($query->result_array());
		
		$this->db_conn->select('type,filename,content' );
		$query2 = $this->db_conn->get('files');
		//var_dump($query2->result_array());
		
		$query3 = $this->db_conn->select('filename')
							->from('files')
							->where('id>',0)
							->order_by('id desc')
				->get();
		var_dump($query3->result_array());*/
		//$this->db_conn->where( 'id' , 1 );
		/*$this->db_conn->update('files',array('filename'=>'editedfilename'),array('id'=>1));
		$this->db_conn->from('files');
		$query4 = $this->db_conn->get();
		var_dump($query4->result_array());
		exit;*/
			
		/*validate php files dir*/
		if ( $this->config->item( 'php_dir' , 'filemgr' ) !== '')
		{
			$this->curr_dir = urldecode( implode( '/' , func_get_args() ) );
			
			if ( $this->curr_dir !== '' )
			{
				$this->curr_dir = '/' . $this->curr_dir;
			}
			
			$this->file_path = $this->config->item( 'php_dir' , 'filemgr' ) . $this->curr_dir;
			
			/*if ( $this->config->item( 'convert_path' , 'filemgr' ) === true )
			{
				$this->file_path = mb_convert_encoding( $this->file_path  , $this->config->item( 'encode_native' , 'filemgr' ) , $this->config->item( 'encode_web' , 'filemgr' ) );
			}*/

			/*if ( ! is_dir( $this->file_path ) )
			{
				show_error( 'Incorrect directory!' );
			}*/
		}
		else
		{
			show_error( 'Invalid directory config for PHP files!' );
		}
		
		/*list all files and directories*/
		//$this->all_list = scandir( $this->file_path );
		//$list_sql = 'SELECT path, filename FROM f_files WHERE path= ?';
		$file_query = $this->db_conn->select( 'path, filename, type, length(content) as size, updatetime as modifytime' )
												->from( 'files' )
												->where( 'path' , $this->file_path )
												->get();
	
		$file_list = $file_query->result_array();
		
		if ( empty( $file_list ) || !is_array( $file_list ) )
		{
			show_error( 'No file found on this directory!' );
		}
		//var_dump($file_list);
		$curr_path = $this->config->item( 'php_dir' , 'filemgr' ) . $this->curr_dir . '/';
		$dir_query = $this->db_conn->distinct()
														->select( 'path' )
														->from( 'files' )
														->like( 'path' , $curr_path )
														->get();
		$this->path_list = $dir_query->result_array();
		foreach( $this->path_list as $val )
		{
			if ( strpos( $val['path'] , $curr_path ) !== false )
			{
				$path = str_replace( $curr_path , '' , $val['path'] );
				if ( strstr( $path , '/' ) === false && strstr( $path , "\\" ) === false )
				{
					$dir_list[$path] = array();
				}
			}
		}
		//var_dump( $dir_list );
		//exit;
		
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
				'toolbarDelete' => true,
				//'toolbarEdit' => true,
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
		foreach ( $dir_list as $key => $val )
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
		foreach ( $file_list as $key => $val )
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
				//'filename' => "<span class='file'><img src='{$this->config->item( 'base_url' )}index.php/resource/png/images/fileicon'>{$key}</span>",
				'size' => $filesize,
				'type' => 'File',
				'modifytime' => $val['modifytime'],
				//'operation' => '',
			);
		}
		
		$this->gridlist['columns'] = $this->columns;
		$this->gridlist['searches'] = $this->searches;
		$this->gridlist['sortData'] = $this->sortData;
		$this->gridlist['records'] = $this->records;
		
		$this->grid_src = json_encode( $this->gridlist );
		$this->load->view( 'filemgr/header' , array( 'title' => 'PHP manager  --  CodeIgniter ver 2.2.1' , 'grid_src' => $this->grid_src , 'curr_dir' => $this->curr_dir , 'environment' => ENVIRONMENT ) );
		//$this->load->view( 'filemgr/leftmenu' );
		$this->load->view( 'filemgr/index' , array( 'filelist' => $this->file_list ) );
		//$this->load->view( 'filemgr/footer' );
	}
}