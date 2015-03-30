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
	whether to deal with filename while doing opeartion save or view
	0:do not convert
	1:utf8_encode utf8_decode
	2:mb_convert_encoding
*/
$config['convert_filename'] = 2;

/*
	method to specify file on disk according to file name in utf-8
	0:do not convert
	1:list all files and convert them to utf-8 to compare      --prefered
	2:get encoding by randomly select a filename and transfer utf-8 filename
	3:use my_find_path to get file path
	!deprecated
*/
$config['find_method'] = 3;