<?php
$filepath = $base_path.'libraries/externals/'.$cssname;

if(!showcss($filepath) && is_dir($filepath))
{
	if(!showcss($filepath.'/'.$cssname))
	{
		show_404();
	}
}

function showcss($file)
{
	if(is_file($file.'.css'))
	{
		header('Content-type:text/css');
		echo "/* Imported by Resource Controller */\n\n";
		echo file_get_contents($file.'.css');
		return true;
	}
	else if(is_file($file.'.php'))
	{
		header('Content-type:text/css');
		echo "/* Imported by Resource Controller */\n\n";
		include ($file.'.php');
		return true;
	}
	return false;
}