<style>
*{
	/*使用Verdana字体和11px会导致背景图片底部不显示*/
	font-family:Arial,sans-serif;
	font-size:13px;
}
.dir {
	background:url("<?php echo $this->config->item( 'base_url' );?>index.php/resource/png/images/folder") no-repeat;
	padding: 0 0 0 20px;
	cursor:pointer;
}
.file {
	background:url("<?php echo $this->config->item( 'base_url' );?>index.php/resource/png/images/fileicon") no-repeat;
	padding: 0 0 0 20px;
	cursor:pointer;
}
</style>
<div id="grid" style="width:100%;height:600px;">
</div>
</body>
<script type="text/javascript">
function modify(recid , type)
{
	var selrec = w2ui.grid.get( recid );
	if (selrec != null)
	{
		var filename = jQuery( selrec.filename ).text();
		var mod_url = '<?php echo $this->config->item( 'base_url' );?>index.php/filemgr/' + type;
		//var filename = selrec.filename.replace(/<(?:.|\n)*?>/gm, '');//also effect
		$.ajax({
			url : mod_url ,
			data : '<?php echo $curr_dir;?>' + filename ,
			method : 'POST' ,
			success : function(data){
				switch(type)
				{
					case 'edit':
						break;
					case 'add':
						break;
					case 'save':
						break;
					case 'view':
						var res = JSON.parse( data );
						if ( res.code == 200 )
						{
							w2popup.open({
								title : filename,
								width : 900,
								height : 600,
								body : '<pre class=\'brush:php\'>' + res.content + '</pre>',
								buttons : '<button class="btn" onclick="w2popup.close();">Close</button>',
							});
							SyntaxHighlighter.highlight();
						}
						else if ( res.code == 301 )
						{
							location.href = res.url;
						}
						else
						{
							w2alert( res.msg , 'Warning' );
						}
						break;
					case 'exec':
						var res = JSON.parse( data );
						if ( res.code == 200 )
						{
							w2popup.open({
								title : filename,
								//url : res.url,//invalid in open method
								width : 700,
								height : 500,
								body : '<div id="res"></div>'+
									'<script type="type/javascript">$("#res").load("'+res.url+'");<\/script>',
								buttons : '<button class="btn" onclick="window.open(\''+res.url+'\',\'_blank\')">Open in new tab</button>'+
									'<button class="btn" onclick="w2popup.close();">Close</button>',
							});
							//w2popup.load({url:res.url});//nothing displayed
						}
						else
						{
							w2alert( res.msg , 'Warning' );
						}
						break;
					case 'del':
						var json = JSON.parse( data );
						w2alert( json.msg , 'Warning' );
						if ( json.code == 200)
						{
							location.reload();
						}
						break;
					default:
						var json = JSON.parse( data );
						w2alert( json.msg , 'Warning' );
						if ( json.code == 200)
						{
							location.reload();
						}
						break;
				}
			} ,
			error : function(){
				w2alert( 'Failed to connect!' , 'Error' );
			} 
		});
	}
}

$( '#grid' ).w2grid(<?php echo $grid_src;?>);

w2ui.grid.on( 'delete' , function(event) {
	if( event.force == true ){
		event.preventDefault();
		var selected = w2ui.grid.getSelection();
		var length = selected.length;
		if ( length >= 1)
		{
			var i = 0;
			for (; i<length; i++)
			{
				modify(selected[i] , 'del' );
			}
		}
	}
});

w2ui.grid.toolbar.on( 'click' , function(event) {
	event.preventDefault();
	//console.log(event);
	var selected = w2ui.grid.getSelection();
	var length = selected.length;
	if ( length != 1 )
	{
		return;
	}
	switch( event.target )
	{
		case 'exebtn' : 
			modify( selected[0] , 'exec' );
			break;
		case 'viewbtn' :
			modify( selected[0] , 'view' );
			break;
		case 'parentbtn' :
			$( '#parentlink' ).trigger( 'click' );
			break;
	}
});

$( '.dir' ).click(function(e){
	var url = '<?php echo $this->config->item( 'base_url' );?>index.php/filemgr/index/explore/<?php echo $curr_dir;?>'+$(this).html();
	window.location.href = url;
});

$( '.file' ).click(function(e){
	var recid = $(this).parent().parent().parent().attr('recid');
	w2ui.grid.selectNone();
	//w2ui.grid.select(recid);
	modify( recid , 'view' );
});
</script>

</html>