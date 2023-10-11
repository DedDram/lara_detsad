jQuery(document).ready(function() {
	jQuery(document).on("click", ".scomments-control-delete, .scomments-control-publish, .scomments-control-unpublish, .scomments-control-blacklist", function(e) {
		e.preventDefault();
		var el = jQuery(this).parent().find('.scomments-control-msg');
        var task = jQuery(this).attr("data-task");
		var object_group = jQuery(this).attr("data-object-group");
        var object_id = jQuery(this).attr("data-object-id");
        var item_id = jQuery(this).attr("data-item-id");
        el.html('<img src="/media/com_comments/html/images/loader.gif">').show();
		jQuery.ajax({
			type: 'POST',
			url: '/index.php',
			dataType: 'json',
			timeout: 5000,
			data: {
				option: 'com_comments',
				view: 'moderation',
				format: 'json',
				task: task,
                object_group: object_group,
                object_id: object_id,
                item_id: item_id
			},
			success: function (data) {
                el.html(data.msg);
			}
		});
	});
});
