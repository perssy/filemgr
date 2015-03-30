<style>
*{
	/*使用Verdana字体和11px会导致背景图片底部不显示*/
	font-family:Arial,sans-serif;
	font-size:13px;
}
.dir {
	background:url("<?php echo $this->config->item( 'base_url' );?>resource/png/images/folder") no-repeat;
	padding: 0 0 0 20px;
	cursor:pointer;
}
.file {
	background:url("<?php echo $this->config->item( 'base_url' );?>resource/png/images/fileicon") no-repeat;
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
		var mod_url = '<?php echo $this->config->item( 'base_url' );?>filemgr/' + type;
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
					default:
						var json = JSON.parse( data );
						alert(json.msg);
						if ( json.code == 200)
						{
							location.reload();
						}
						break;
				}
				if (type == 'del' )
				{
					var json = JSON.parse( data );
					alert(json.msg);
					if ( json.code == 200)
					{
						location.reload();
					}
				}
				else
				{
				}
				//alert('success');
			} ,
			error : function(){
				alert('fail');
			} 
		});
	}
}

$( '#grid' ).w2grid(<?php echo $grid_src;?>);
w2ui.grid.on( 'add' , function(event) {
	
});

w2ui.grid.on( 'edit' , function(event) {
	alert($(this).attr('class'));
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
	var url = '<?php echo $this->config->item( 'base_url' );?>filemgr/index/explore/<?php echo $curr_dir;?>'+$(this).html();
	window.location.href = url;
	//window.location.replace(url);
});

$( '#tb_grid_toolbar_item_parentbtn' ).click(function(e){
	//window.location.href = "<?php echo $this->config->item( ' base_url' );?>";
	$( '#parentlink' ).trigger( 'click' );
});
</script>

</html>