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
		var filename = jQuery(selrec.filename).text();
		var mod_url = '<?php echo $this->config->item( 'base_url' );?>index.php/filemgr/' + type;
		//var filename = selrec.filename.replace(/<(?:.|\n)*?>/gm, '');//also effect
		$.ajax({
			url : mod_url ,
			data : '<?php echo $curr_dir;?>' + filename ,
			method : 'POST' ,
			success : function(data){
				switch(type)
				{
					case 'del':
						break;
					case 'edit':
						break;
					case 'add':
						break;
					case 'save':
						break;
					case 'exec':
						var res = JSON.parse( data );
						if ( res.code == 200 )
						{
							//console.log(res);
							w2popup.open({
								title : filename,
								//url : res.url,//invalid in open method
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
				//w2alert( 'success' , 'Warning' );
			} ,
			error : function(){
				w2alert( 'Failed to connect!' , 'Error' );
			} 
		});
	}
}

$( '#grid' ).w2grid(<?php echo $grid_src;?>);
w2ui.grid.toolbar.on( 'click' , function(event) {
	console.log(event.target);
	event.preventDefault();
	var selected = w2ui.grid.getSelection();
	var length = selected.length;
	if ( length != 1 )
	{
		return;
	}
	switch(event.target)
	{
		case 'exebtn' : 
			modify( selected[0] , 'exec' );
			break;
	}
});
w2ui.grid.on( 'add' , function(event) {
	
});

w2ui.grid.on( 'edit' , function(event) {
	w2alert($(this).attr('class'));
});

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

$( '.dir' ).click(function(e){
	var url = '<?php echo $this->config->item( 'base_url' );?>index.php/filemgr/index/explore/<?php echo $curr_dir;?>'+$(this).html();
	window.location.href = url;
	//window.location.replace(url);
});

$( '#tb_grid_toolbar_item_parentbtn' ).click(function(e){
	//window.location.href = "<?php echo $this->config->item( ' base_url' );?>";
	$( '#parentlink' ).trigger( 'click' );
});
</script>

</html>