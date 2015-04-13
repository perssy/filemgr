<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
type of method to list all files
1:get file map using directory_map and then walk through by RecursiveArrayIterator
2:get file map using directory_map and then walk through manually using foreach or recursive
3:get file map manually by scandir recursively
*/
$config['walk_type'] = 3;