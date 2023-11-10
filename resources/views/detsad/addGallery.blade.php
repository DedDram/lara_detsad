<!DOCTYPE html>
<html>
<head>
    <meta name="robots" content="noindex, nofollow" />
</head>
<body>
<div id="msg"></div>
@if (!empty($total) && $total>=8)
<div id="msg">К данной клинике уже добавлено 8 фотографий, это максимум.</div>
@else
<form class="add-gallery" method="post" action="/post/add-gallery" enctype="multipart/form-data">
    @csrf
    <input type="file" name="myfile" style="width: 506px; margin-bottom: 4px;" required>
    <textarea name="description" style="width: 98%; height: 105px; margin: 10px 0; padding: 4px;border-color: #999;" class="postform" placeholder="Описание изображения" required></textarea><br>
    <input type="checkbox" name="condition" required> <span style="font-style: italic; color: #888;">Отправляя нам фотографии, Вы подтверждаете, что права на их использование принадлежат Вам, либо они получены из открытых источников и Вы не возражаете против публикации фото на нашем сайте</span>
    <br><br>
    <input type="hidden" name="item_id" value="{{$item_id}}">
    <input type="hidden" name="task" value="addGallery">
    <input type="submit" name="submit" value="Добавить фото"> <span class="percent" style="display: none; background: url('/images/loader.gif') left center no-repeat; padding-left: 20px;">0%</span>
</form>

<script>
    $(document).ready(function() {
        $(document).on("submit", ".add-gallery", function(e) {
            e.preventDefault();
            let form = $(this);
            form.ajaxSubmit({
                beforeSend: function() {
                    form.find('.percent').html('0%').show();
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    form.find('.percent').html(percentComplete + '%').show();
                },
                success: function(data) {
                    if(data.status === 1) {
                        $('#msg').attr('class', 'ui-state-highlight ui-corner-all').html(data.msg).show();
                        $('form').hide();
                    }
                    if(data.status === 2) {
                        $('#msg').attr('class', 'ui-state-error ui-corner-all').html(data.msg).show();
                    }
                    form.find('.percent').html('100%').hide();
                    $('.postform').css('background-color', '#fff');
                    $('.postform').css('border', '1px solid #aaa');
                    if(data.fields) {
                        $(data.fields).css('background-color', '#fef1ec');
                        $(data.fields).css('border', '1px solid #cd0a0a');
                    }
                    if(data.status > 0) {
                        scrollUp();
                    }
                }
            });
        });
    });

    function scrollUp() {
        let curPos = $(document).scrollTop();
        let scrollTime = curPos/1.73;
        $("body,html").animate({"scrollTop":0}, scrollTime);
    }
</script>
@endif
</body>
</html>
