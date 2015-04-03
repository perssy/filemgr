<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	folder containing individual php files
	relative path under application folder
*/
$config['php_dir'] = 'legacy/files';

/*
	folder containing php projects
	relative path under application folder
*/
$config['project_dir'] = 'legacy/projects';

/*
	determine if convert encoding while process with file path
*/
$config['convert_path'] = true;

/*
	the encoding of disk file
*/
$config['encode_native'] = 'CP936';

/*
	the encoding used in php script
*/
$config['encode_web'] = 'UTF-8';