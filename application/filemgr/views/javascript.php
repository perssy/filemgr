<?php

$filepath = $base_path.'libraries/externals/'.$scriptname;
if(!showjs($filepath) && is_dir($filepath))
{
	if(!showjs($filepath.'/'.$scriptname))
	{
		show_404();
	}
}

function showjs($file)
{
	if(is_file($file.'.js'))
	{
		header('Content-type:application/javascript');
		echo "/*Import by Resource Controller*/\n\n";
		echo file_get_contents($file.'.js');
		return true;
	}
	else if(is_file($file.'.php'))
	{
		header('Content-type:application/javascript');
		echo "/*Import by Resource Controller*/\n\n";
		include ($file.'.php');
		return true;
	}
	return false;
}