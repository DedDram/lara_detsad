<?php
$item->url = str_replace('/school/', '/schools/', $item->url);
?>

<div style="margin: 3px 0;">Имя: <span style="color: #3c452d; font: bold 1em Verdana, Arial, Sans-Serif;"><?php echo $item->username; ?></span></div>
<div style="margin: 3px 0;">Время: <?php echo $item->created; ?></div>
<div style="margin: 3px 0;">Обсуждение: <a href="<?php echo $siteName; ?><?php echo $item->url; ?>" target="_blank"><?php echo $item->title; ?></a></div>
<div style="border: 1px solid #ccc; padding: 10px 5px; margin: 12px 0; font: normal 1em Verdana, Arial, Sans-Serif; border-radius: 7px;"><?php echo $item->description; ?></div>

<div>
    <a href="<?php echo $siteName; ?>/comments?task=unsubscribe&object_group=<?php echo $item->object_group; ?>&object_id=<?php echo $item->object_id; ?>">Отписаться от рассылки</a> / Не отвечайте на это письмо, это всего лишь уведомление о новом отзыве.
</div>
