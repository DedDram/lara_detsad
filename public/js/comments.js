jQuery(document).ready(function(){
  if(jQuery("#scomments").length>0||jQuery("#last-comments").length>0||jQuery(".scomments-form").length>0){new nicEditor({iconsPath : '/media/com_comments/html/images/nicEditorIcons.gif', buttonList : []}).panelInstance('description');}
});
var comments = (function() {
  var _private = {
    'list': function() {
	    jQuery(".scomments-item-images-toogle").click(function(e) {
	        e.preventDefault();
	        var id = jQuery(this).attr("data-id");
	        var el = jQuery(this).parent().find(".scomments-item-images");
			jQuery.ajax({
				type: 'POST',
				url: '/index.php',
				dataType: 'json',
				timeout: 5000,
				data: {
					option: 'com_comments',
					view: 'images',
					format: 'json',
	                task: 'cut',
	                id: id
				},
				success: function (rows) {
	                el.html('');
	                jQuery.each(rows, function (n, row) {
	                    el.append('<a href="/images/comments/'+ row.original +'" class="simplemodal" data-width="800" data-height="500"><img src="/images/comments/'+ row.thumb +'"></a>');
	                });
				}
			});
	        el.toggle("fast");
	    });
	    jQuery(".scomments-vote a").click(function(e) {
	        e.preventDefault();
	        var el = jQuery(this).parent();
	        var id = jQuery(this).attr("data-id");
	        var value = jQuery(this).attr("data-value");
        
			jQuery.ajax({
				type: 'POST',
				url: '/index.php',
				dataType: 'json',
				timeout: 5000,
				data: {
					option: 'com_comments',
					view: 'item',
					format: 'json',
	                task: 'vote',
	                value: value,
	                id: id
				},
				success: function (rows) {
	                el.html('Спасибо ваш голос принят');
				}
			});
    	});
    	jQuery(".scomments-form-toogle").click(function(e){
    	  e.preventDefault();
   	    var URL = jQuery(this).attr("href");
   	    if(jQuery(this).hasClass('scomments-add')&&jQuery(this).attr('href')=='#ADD'){
				_private.scroll(function(){});
   	    }else{
			jQuery.get(URL, function(res){
				_private.scroll(function(){
					jQuery(".scomments-form").html(res);
					jQuery('#slider').slick({
						slidesToShow: 3,
						slidesToScroll: 3,
						variableWidth: true,
						infinite: false,
					});
					new nicEditor({iconsPath : '/media/com_comments/html/images/nicEditorIcons.gif', buttonList : []}).panelInstance('description');
 				});
			});
   	    }
	    });

		jQuery(".checked_comm_div input").click(function (e) {
				var votes = $("input[name='radio']:checked").val();
				var objectid = $("input[name='object_id']").val();
				var objectgroup = $("input[name='object_group']").val();
				var all = $(".scomments-all");
				var count_good = document.getElementById('count_good');
				var count_neutrally = document.getElementById('count_neutrally');
				var count_bad = document.getElementById('count_bad');
				var link_comment = '';
				if (votes == 'good') {
					count_good.style.fontWeight = 'bold';
					count_good.style.color = '#8af78f';
					count_good.style.textShadow = 'black 1px 1px 1px, green 0px 0px 0em';
					count_bad.style.fontWeight = '';
					count_bad.style.color = '';
					count_neutrally.style.fontWeight = '';
					link_comment = '😀';
				} else if (votes == 'neutrally') {
					count_neutrally.style.fontWeight = 'bold';
					count_good.style.fontWeight = '';
					count_good.style.textShadow = '';
					count_good.style.color = '';
					count_bad.style.fontWeight = '';
					count_bad.style.color = '';
					link_comment = '😐';
				} else if (votes == 'bad') {
					count_bad.style.fontWeight = 'bold';
					count_bad.style.color = '#f44336';
					count_good.style.fontWeight = '';
					count_good.style.textShadow = '';
					count_good.style.color = '';
					count_neutrally.style.fontWeight = '';
					link_comment = '😡';
				} else {
					count_good.style.fontWeight = '';
					count_good.style.color = '';
					count_good.style.textShadow = '';
					count_neutrally.style.fontWeight = '';
					count_bad.style.fontWeight = '';
					count_bad.style.color = '';
					link_comment = '#';
				}

				jQuery.ajax({
					type: 'POST',
					url: '/index.php',
					dataType: 'json',
					timeout: 5000,
					data: {
						option: 'com_comments',
						view: 'item',
						format: 'json',
						task: 'votes',
						votes: votes,
						objectid: objectid,
						objectgroup: objectgroup
					},
					success: function (data) {
						//console.log(data);
						var str = '';
						var styleComments;
						var text_title;
						var status;

						for (var i = 0; i < data.length; i++) {
							if (Number(data[i].rate) >= 4) {
								styleComments = 'good_comm';
								text_title = 'Хороший отзыв';
							} else if (Number(data[i].rate) == 3 || Number(data[i].rate) === 0) {
								styleComments = 'neutrally_comm';
								text_title = 'Нейтральный отзыв';
							} else {
								styleComments = 'bad_comm';
								text_title = 'Плохой отзыв';
							}

							if (Number(data[i].status) === 0) {
								status = 'style="background-color: #ffebeb;"';
							} else {
								status = '';
							}

							str += '<div class="scomments-item ' + styleComments + '"' + status + '>';

							if (data[i].registered) {
								str += '<div class="comments-avatar-registered" ' + text_title + '  зарегистрированного пользователя"></div>';
							} else {
								str += '<div class="comments-avatar-guest"  title="' + text_title + '"></div>';
							}

							str += '<div class="comments-content">' +
								'<div class="scomments-title">' +
								'<span class="scomments-vote">' +
								'<a rel="nofollow" href="#" title="Согласен!" class="scomments-vote-good" data-id="' + data[i].id + '" data-value="up">Это правда' + (data[i].isgood ? '<span>' + data[i].isgood + '</span>' : '') + '</a>' +
								'<a rel="nofollow" href="#" title="Не согласен!" class="scomments-vote-poor" data-id="' + data[i].id + '" data-value="down">Это ложь' + (data[i].ispoor ? '<span>' + data[i].ispoor + '</span>' : '') + '</a>' +
								'</span>' +
								'<div>' +
								'<a href="#scomment-' + data[i].id + '" name="scomment-' + data[i].id + '" id="scomment-' + data[i].id + '"> ' + link_comment + '</a>';
							if (data[i].user_name) {
								str += '<span class="scomments-user-name" itemprop="author">' + data[i].user_name + '</span>';
							} else {
								str += '<span class="scomments-guest-name" itemprop="author">' + data[i].guest_name + '</span>';
							}

							str += '</div></div><div>' +
								'<span class="scomments-date" itemprop="datePublished" content="' + data[i].created + '">' + data[i].created + '</span>';
							if (data[i].country && data[i].country != 'unknown') {
								str += '<span class="scomments-marker"></span><span class="scomments-country">' + data[i].country + '</span>';
							}
							str += '</div>' +
								'<div class="scomments-text" itemprop="reviewBody">' + data[i].description + '</div>';
							if (Number(data[i].mages) > 0) {
								str += '<a href="#" data-id="' + data[i].id + '" class="scomments-item-images-toogle">Показать прикрепленное фото</a>' +
									'<div class="scomments-item-images"></div>';
							}
							str += '</div></div>';
						}

						$("div.pagination").empty();
						all.html(str);

						$('.scomments-vote a').bind('click', function (e) {
							e.preventDefault();
							var el = jQuery(this).parent();
							var id = jQuery(this).attr("data-id");
							var value = jQuery(this).attr("data-value");

							jQuery.ajax({
								type: 'POST',
								url: '/index.php',
								dataType: 'json',
								timeout: 5000,
								data: {
									option: 'com_comments',
									view: 'item',
									format: 'json',
									task: 'vote',
									value: value,
									id: id
								},
								success: function () {
									el.html('Спасибо ваш голос принят');
								}
							});
						});
					}
				});
			}
		);
    },
    'form': function() {
		jQuery(document).on("blur", "#email", function(e) {
			jQuery("label[for='email']").hide();
		});
		jQuery(document).on("focus", "#email", function(e) {
			jQuery("label[for='email']").show();
		});
		jQuery(document).on("click", "#myfile", function(e) {
					jQuery('#slider').slick({
						slidesToShow: 3,
						slidesToScroll: 3,
						variableWidth: true,
						infinite: false,
					});
		});
		jQuery(document).on("click", "#submit", function(e) {
			e.preventDefault();
    	    jQuery('#myform').submit();
		});
		jQuery(document).on("change", "#upload [type=file]", function(e) {
			e.preventDefault();
	        jQuery('#upload').submit();
		});
		jQuery(document).on("submit", "#myform", function(e) {
			e.preventDefault();
    	    var el = jQuery("#submit");
    	    if(!el.hasClass("disabled"))
    	    {
    	        el.addClass("disabled");
              nicEditors.findEditor('description').saveContent();
    	        jQuery('#myform').ajaxSubmit({
    	            beforeSend: function() {
    	                jQuery('#msg').hide();
    	            },
    	            success: function(data) {
    	                if(data.status == 1) {
    	                    jQuery('#msg').attr('class', 'msg-success').html(data.msg).show();
    	                    jQuery('#myform').clearForm();
    	                    jQuery('#wrapper').hide();
    	                    nicEditors.findEditor('description').setContent('');
    	                }
    	                if(data.status == 2) {
    	                    jQuery('#msg').attr('class', 'msg-error').html(data.msg).show();
    	                }
    	                el.removeClass("disabled");
						//
						_private.scroll();
    	            }
    	        });
        	}
		});
		jQuery(document).on("submit", "#upload", function(e) {
			e.preventDefault();
			jQuery('#upload').ajaxSubmit({
                beforeSend: function() {
    		        jQuery('#msg').hide();
					jQuery('#percent').html('0%').show();
				},
				uploadProgress: function(event, position, total, percentComplete) {
					jQuery('#percent').html(percentComplete + '%');
				},
				success: function(data) {
    		        if(data.status == 1) {
    		            jQuery('#slider').slick('slickAdd','<div class="row-slide"><a href="#" data-id="'+ data.id +'" data-attach="'+ data.attach +'" class="remove-slide"></a><img src="/images/comments/'+ data.thumb +'"></div>');
    		        }
    		        if(data.status == 2) {
    		            jQuery('#msg').attr('class', 'msg-error').html(data.msg).show();
    		        }
    		        jQuery('#upload').clearForm();
					jQuery('#percent').html('100%').hide();
				}
			});
		});
		jQuery(document).on("click", ".remove-slide", function(e) {
			e.preventDefault();
        	jQuery('#msg').hide();
			var slideIndex = jQuery(this).parent().attr("data-slick-index");
        	var id = jQuery(this).attr("data-id");
        	var attach = jQuery(this).attr("data-attach");
			jQuery.ajax({
				type: 'POST',
				url: '/index.php',
				dataType: 'json',
				timeout: 5000,
				data: {
					option: 'com_comments',
					view: 'images',
					format: 'json',
        	        task: 'remove',
        	        id: id,
					attach: attach
				},
				success: function (data) {
        	        if(data.status == 1) {
        	            jQuery('#slider').slick('slickRemove', slideIndex);
        	        }
        	        if(data.status == 2) {
        	            jQuery('#msg').attr('class', 'msg-error').html(data.msg).show();
        	        }
				}
			});
		});
    },
    'scroll': function(callback) {
			jQuery('body,html').animate({
				scrollTop: jQuery(".scomments-anchor").offset().top
			}, 300, function() {
				if(callback) {
					callback();
				}
			});
    }
  };
  return {
    init: function() {
		_private.list();
		_private.form();
    }
  }
}());

//

jQuery(document).ready(function() {
	comments.init();
});

