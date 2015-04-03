<?php

/*
return a file function friendly path string from a UTF-8 version
I wrote this because PHP file functions do not support UTF-8 encoding well
$path : the raw string containing path in UTF-8 encoding
$base_path : base path or root of the relative path 
$encodings : encoding list to use
$urlencode : determine if url encode or decode the $path parameter
$returnall : true for returning result including base path , false for not
*/
function my_find_path( $path , $base_path = '.' , $encodings = array() , $urlencode = 0 , $returnall = false)
{
	$logflag = true;
	
	if ( !is_array( $encodings ) || empty( $encodings ))
	{
		$encodings = array( 'UTF-8' , 'GBK' , 'ASCII' , 'CP936' , 'ISO-8859-1' );
	}

	$path = addslashes( str_replace( "\\" , '/' , $path ) );
	
	$logflag && file_put_contents( 'findpath.log' , "\n path 1:" , FILE_APPEND );
	$logflag && file_put_contents( 'findpath.log' , print_r( $path , true ) , FILE_APPEND );
	
	if ( $urlencode === 1 )
	{
		$path = urlencode( $path );
	}
	else if ( $urlencode === -1 )
	{
		$path = urldecode( $path );
	}

	$logflag && file_put_contents( 'findpath.log' , "\n path 2:" , FILE_APPEND );
	$logflag && file_put_contents( 'findpath.log' , print_r( $path , true ) , FILE_APPEND );
	
	$full_path = $base_path . '/' . $path;

	$logflag && file_put_contents( 'findpath.log' , "\n full path 1:" , FILE_APPEND );
	$logflag && file_put_contents( 'findpath.log' , print_r( $full_path , true ) , FILE_APPEND );
	
	if ( is_dir( $full_path ) || is_file( $full_path ) )
	{
		return $path;
	}
	
	$path_arr = explode( '/' , $path );
	
	$logflag && file_put_contents( 'findpath.log' , "\n path arr 1:" , FILE_APPEND );
	$logflag && file_put_contents( 'findpath.log' , print_r( $path_arr , true ) , FILE_APPEND );
	
	$dept = count( $path_arr );
	
	$i = 0;

	//$temp_path = $base_path;
	
	array_unshift( $path_arr , $base_path );
	
	$logflag && file_put_contents( 'findpath.log' , "\n path arr 2:" , FILE_APPEND );
	$logflag && file_put_contents( 'findpath.log' , print_r( $path_arr , true ) , FILE_APPEND );
	
	$find_path = $base_path;
	$real_path = '';

	$oldencode = 'UTF-8';
	
	for ( ; $i < $dept ; $i++)
	{
		$logflag && file_put_contents( 'findpath.log' , "\n outer loop a new turn:" , FILE_APPEND );
		
		$temp_path = mb_convert_encoding( $find_path , $oldencode , 'UTF-8' );

		$logflag && file_put_contents( 'findpath.log' , "\n old encode:" , FILE_APPEND );
		$logflag && file_put_contents( 'findpath.log' , print_r( $oldencode , true ) , FILE_APPEND );
	
		$logflag && file_put_contents( 'findpath.log' , "\n temp path:" , FILE_APPEND );
		$logflag && file_put_contents( 'findpath.log' , print_r( $temp_path , true ) , FILE_APPEND );
		
		$file_list = scandir( $temp_path );

		$logflag && file_put_contents( 'findpath.log' , "\n file list:" , FILE_APPEND );
		$logflag && file_put_contents( 'findpath.log' , print_r( $file_list , true ) , FILE_APPEND );
	
		$target = $path_arr[$i+1];

		foreach ( $file_list as $val )
		{
			$logflag && file_put_contents( 'findpath.log' , "\n inner loop:" , FILE_APPEND );
			$logflag && file_put_contents( 'findpath.log' , print_r( $val , true ) , FILE_APPEND );
			
			if ( $val === '.' || $val === '..' )
			{
				continue;
			}
			
			$encode = mb_detect_encoding( $val , $encodings );

			$logflag && file_put_contents( 'findpath.log' , "\n encode:" , FILE_APPEND );
			$logflag && file_put_contents( 'findpath.log' , print_r( $encode , true ) , FILE_APPEND );
	
			if ( strtoupper( $encode ) !== 'UTF-8' )
			{
				$encoded = mb_convert_encoding( $val , 'UTF-8' , $encode );
				$oldencode = $encode;
			}
			else
			{
				$encoded = $val;
			}

			$logflag && file_put_contents( 'findpath.log' , "\n encoded:" , FILE_APPEND );
			$logflag && file_put_contents( 'findpath.log' , print_r( $encoded , true ) , FILE_APPEND );
			
			if ( $encoded === $target )
			{
				//$real_path_arr[$i] = $val;
				$find_path .= '/' . $encoded;
				if ( $real_path !== '' )
				{
					$real_path .= '/';
				}
				$real_path .= $val;
				
				$logflag && file_put_contents( 'findpath.log' , "\n find path:" , FILE_APPEND );
				$logflag && file_put_contents( 'findpath.log' , print_r( $find_path , true ) , FILE_APPEND );
				$logflag && file_put_contents( 'findpath.log' , "\n real path:" , FILE_APPEND );
				$logflag && file_put_contents( 'findpath.log' , print_r( $real_path , true ) , FILE_APPEND );
				
				break;
			}
		}
	}
	$logflag && file_put_contents( 'findpath.log' , "\n result:" , FILE_APPEND );
	$logflag && file_put_contents( 'findpath.log' , print_r( $real_path , true ) , FILE_APPEND );
	
	if ( $returnall )
	{
		return $base_path . $real_path;
	}
	else
	{
		return $real_path;
	}
}