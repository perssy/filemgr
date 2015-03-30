<div id="mainframe">
</div>

<script type="text/javascript">
$(function () {
    var pstyle = 'background-color: #F5F6F7; border: 1px solid #dfdfdf; padding: 5px;';
    $('#mainframe').w2layout({
        name: 'mainframe',
        panels: [
            { type: 'top',  size: 50, resizable: true, style: pstyle, content: 'top' },
            { type: 'left', size: 200, resizable: true, style: pstyle, content: 'left' },
            { type: 'main', style: pstyle, content: 'main' },
            { type: 'preview', size: '50%', resizable: true, style: pstyle, content: 'preview' },
            { type: 'right', size: 200, resizable: true, style: pstyle, content: 'right' },
            { type: 'bottom', size: 50, resizable: true, style: pstyle, content: 'bottom' }
        ]
    });
});
// first define a layout
$('#layout_mainframe_panel_left').w2layout({
    name: 'layout_mainframe_panel_left',
    panels: [
        { type: 'left', size: 200, resizable: true, style: 'background-color: #F5F6F7;', content: 'left' },
        { type: 'main', style: 'background-color: #F5F6F7; padding: 5px;' }
    ]
});

// then define the sidebar
w2ui['layout_mainframe_panel_left'].content('left', $().w2sidebar({
	name: 'sidebar',
	img: null,
	nodes: [ 
		{ id: 'level-1', text: 'Level 1', img: 'icon-folder', expanded: true, group: true,
		  nodes: [ { id: 'level-1-1', text: 'Level 1.1', icon: 'fa-home' },
				   { id: 'level-1-2', text: 'Level 1.2', icon: 'fa-star' },
				   { id: 'level-1-3', text: 'Level 1.3', icon: 'fa-check' }
				 ]
		},
		{ id: 'level-2', text: 'Level 2', img: 'icon-folder', expanded: true, group: true,
		  nodes: [ { id: 'level-2-1', text: 'Level 2.1', img: 'icon-folder', 
					 nodes: [
					   { id: 'level-2-1-1', text: 'Level 2.1.1', img: 'icon-page' },
					   { id: 'level-2-1-2', text: 'Level 2.1.2', img: 'icon-page' },
					   { id: 'level-2-1-3', text: 'Level 2.1.3', img: 'icon-page' }
				 ]},
				   { id: 'level-2-2', text: 'Level 2.2', img: 'icon-page' },
				   { id: 'level-2-3', text: 'Level 2.3', img: 'icon-page' }
				 ]
		},
		{ id: 'level-3', text: 'Level 3', img: 'icon-folder', expanded: true, group: true,
		  nodes: [ { id: 'level-3-1', text: 'Level 3.1', img: 'icon-page' },
				   { id: 'level-3-2', text: 'Level 3.2', img: 'icon-page' },
				   { id: 'level-3-3', text: 'Level 3.3', img: 'icon-page' }
				 ]
		}
	],
	onClick: function (event) {
		w2ui['layout_mainframe_panel_left'].content('main', 'id: ' + event.target);
	}
}));
</script>