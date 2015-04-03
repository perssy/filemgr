<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?php echo $title ?></title>
<style>
* { margin:0;padding:0; }
</style>
<script src="<?php echo $this->config->item('base_url');?>index.php/resource/javascript/jquery-1.11.0.min" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo $this->config->item('base_url');?>index.php/resource/css/w2ui/w2ui-1.4.2<?php if( $environment != 'development' ){?>.min<?php }?>" type="text/css"/>
<script src="<?php echo $this->config->item('base_url');?>index.php/resource/javascript/w2ui/w2ui-1.4.2<?php if( $environment != 'development' ){?>.min<?php }?>" type="text/javascript"></script>
</head>
<body>