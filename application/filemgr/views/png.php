<?php
$filepath = $base_path.'libraries/externals/'.$pngname;

if(!showpng($filepath) && is_dir($filepath))
{
	if(!showpng($filepath.'/'.$pngname))
	{
		show_404();
	}
}

function showpng($file)
{
	if(is_file($file.'.png'))
	{
		header('Content-type:image/png');
		//echo "/* Imported by Resource Controller */\n\n";
		echo file_get_contents($file.'.png');
		return true;
	}
	else if(is_file($file.'.php'))
	{
		header('Content-type:image/png');
		//echo "/* Imported by Resource Controller */\n\n";
		include ($file.'.php');
		return true;
	}
	return false;
}