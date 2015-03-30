<?php
/*
project:test
使用ajax跨域访问需要服务端配合
使用php修改user agent后即可直接访问
*/
do{
	$url = 'http://www.18008.com/lhc/ajax.aspx?act=getqihao';

	$ch = curl_init($url);

	$header = array(
		'User-Agent:Mozilla/5.0 (Windows NT 5.1; rv:36.0) Gecko/20100101 Firefox/36.0',
	);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'get');
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	$res = curl_exec($ch);

	print_r($res);
}while(false);