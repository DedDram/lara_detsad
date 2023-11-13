<div style="margin: 3px 0;">Имя: <span style="color: #3c452d; font: bold 1em Verdana, Arial, Sans-Serif;"><?php echo $user->name; ?></span> / <a href="mailto:<?php echo $user->email; ?>" target="_blank"><?php echo $user->email ?></a></div>
<div style="margin: 3px 0;">Садик: <a href="<?php echo $siteName; ?><?php echo $urlSadik->url; ?>" target="_blank"><?php echo $urlSadik->name; ?></a></div>

<div>
    <a href="<?php echo $siteName; ?>/agent?activation=1&user_id=<?php echo $user->id; ?>">Активировать</a> |
    <a href="<?php echo $siteName; ?>/agent?delete=1&user_id=<?php echo $user->id; ?>">Удалить</a>
</div>
