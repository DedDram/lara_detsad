<div style="margin: 3px 0;">Имя: <span style="color: #3c452d; font: bold 1em Verdana, Arial, Sans-Serif;"><?php echo $item->username; ?></span> / <a href="mailto:<?php echo $item->useremail; ?>" target="_blank"><?php echo $item->useremail; ?></a></div>
<div style="margin: 3px 0;">IP: <?php echo $item->ip; ?> / <?php echo $item->country; ?></div>
<div style="margin: 3px 0;">Время: <?php echo $item->created; ?></div>
<div style="margin: 3px 0;">Обсуждение: <a href="<?php echo $siteName; ?><?php echo $item->url; ?>" target="_blank"><?php echo $item->title; ?></a></div>
<div style="border: 1px solid #ccc; padding: 10px 5px; margin: 12px 0; font: normal 1em Verdana, Arial, Sans-Serif; border-radius: 7px;"><?php echo $item->description; ?></div>

<?php if(!empty($images)): ?>
    <div style="margin: 12px 0;">
        <?php foreach($images as $image): ?>
            <img src="<?php echo $siteName; ?>/images/comments/<?php echo $image->thumb; ?>" style="height: 92px; margin-right: 12px;">
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div>
    <?php if(!empty($item->status)): ?>
        <a href="<?php echo $siteName; ?>/cron/comments?option=com_comments&view=moderation&format=raw&task=unpublish&object_group=<?php echo $item->object_group; ?>&object_id=<?php echo $item->object_id; ?>&item_id=<?php echo $item->id; ?>">Cнять с публикации</a> |
    <?php else: ?>
        <a href="<?php echo $siteName; ?>/cron/comments?option=com_comments&view=moderation&format=raw&task=publish&object_group=<?php echo $item->object_group; ?>&object_id=<?php echo $item->object_id; ?>&item_id=<?php echo $item->id; ?>">Опубликовать</a> |
    <?php endif; ?>
    <a href="<?php echo $siteName; ?>/cron/comments?option=com_comments&view=moderation&format=raw&task=remove&object_group=<?php echo $item->object_group; ?>&object_id=<?php echo $item->object_id; ?>&item_id=<?php echo $item->id; ?>">Удалить</a> |
    <a href="<?php echo $siteName; ?>/cron/comments?option=com_comments&view=moderation&format=raw&task=blacklist&object_group=<?php echo $item->object_group; ?>&object_id=<?php echo $item->object_id; ?>&item_id=<?php echo $item->id; ?>">Заблокировать IP</a> |
</div>
