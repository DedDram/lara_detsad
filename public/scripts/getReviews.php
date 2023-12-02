<?php
//ini_set('log_errors', 'On');
//ini_set('error_log', '/var/www/med/components/com_vuz/views/item/tmpl/test.txt');


ini_set("memory_limit", "1000M");
set_time_limit(0);
$database = 'detsad_new'; // имя базы данных
$user = 'root'; // имя пользователя
$password = 'Lechis13131'; // пароль

$conn = new PDO("mysql:host=localhost;dbname=" . $database . ";charset=UTF8", $user, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getUserAgent()
{
    $userAgentArray[] = "Mozilla/5.0 (Linux; Android 7.0; SM-A310F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.116 Mobile Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
    $userAgentArray[] = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36 OPR/49.0.2725.47";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36 Edge/15.15063";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 OPR/49.0.2725.64";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/62.0.3202.94 Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0;  Trident/5.0)";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/63.0.3239.84 Chrome/63.0.3239.84 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0;  Trident/5.0)";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0";
    $userAgentArray[] = "Mozilla/5.0 (iPad; CPU OS 11_1_2 like Mac OS X) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0 Mobile/15B202 Safari/604.1";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0";
    $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Safari/604.1.38";
    $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
    $userAgentArray[] = "Mozilla/5.0 (X11; CrOS x86_64 9901.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.97 Safari/537.36";

    $getArrayKey = array_rand($userAgentArray);
    return $userAgentArray[$getArrayKey];

}

function getPageByUrl($url, $refer)
{
    //Инициализируем сеанс
    $curl = curl_init();
    //Указываем адрес страницы
    curl_setopt($curl, CURLOPT_URL, $url);

    //curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));
    //Ответ сервера сохранять в переменную, а не на экран
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //Переходить по редиректам
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    //$agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36';
    //curl_setopt($curl, CURLOPT_USERAGENT, $agent);
    $getUserAgent = getUserAgent();
    curl_setopt($curl, CURLOPT_USERAGENT, $getUserAgent);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
    //если сайт https
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    //cookie
    curl_setopt($curl, CURLOPT_COOKIEJAR,  '/var/www/com/cookie.txt');
    curl_setopt($curl, CURLOPT_COOKIEFILE, '/var/www/com/cookie.txt');
    //смена IP
    //$a = file('/var/www/com/proxy6.txt');
    //$proxy = $a[array_rand($a)];
    //preg_match('~((.*):(.*)):((.*):(.*))~m', $proxy, $proxyMatch);
    //curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    //curl_setopt($curl, CURLOPT_PROXY, trim($proxyMatch[1]));
    //curl_setopt($curl, CURLOPT_PROXYUSERPWD, trim($proxyMatch[4]));

    //http2 если нужно
    //curl_setopt($curl, CURLOPT_HTTP_VERSION, 3);
    curl_setopt($curl, CURLOPT_REFERER, $refer);

    //Выполняем запрос:
    $result = curl_exec($curl);
    //Отлавливаем ошибки подключения
    if ($result === false) {
        echo "Ошибка CURL: " . curl_error($curl);
        return false;
    } else {
        return $result;
    }

}

function image_resize($sourse, $new_image, $width, $height)
{
    $size = GetImageSize($sourse);
    $new_height = $height;
    $new_width = $width;

    if ($size[0] < $size[1])
        $new_width = ($size[0] / $size[1]) * $height;
    else
        $new_height = ($size[1] / $size[0]) * $width;
    $new_width = ($new_width > $width) ? $width : $new_width;
    $new_height = ($new_height > $height) ? $height : $new_height;
    $image_p = @imagecreatetruecolor($new_width, $new_height);
    if ($size[2] == 2) {
        $image_cr = imagecreatefromjpeg($sourse);
    } else if ($size[2] == 3) {
        $image_cr = imagecreatefrompng($sourse);
    } else if ($size[2] == 1) {
        $image_cr = imagecreatefromgif($sourse);
    } else if ($size[2] == 18) {
        $image_cr = imagecreatefromwebp($sourse);
    }
    imagecopyresampled($image_p, $image_cr, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);
    if ($size[2] == 2) {
        imagejpeg($image_p, $new_image, 75);
    } else if ($size[2] == 1) {
        imagegif($image_p, $new_image);
    } else if ($size[2] == 3) {
        imagepng($image_p, $new_image);
    } else if ($size[2] == 18) {
        imagewebp($image_p, $new_image);
    }
}

require_once '/var/www/detsad_new/public/scripts/phpQuery.php';


if (!empty($_POST['item_id']) && !empty($_POST['id_yandex'])) {
    getReviews($_POST['item_id'], $_POST['id_yandex'], $conn);
    $sql4 = "UPDATE `i1il4_detsad_items` SET `sid` = '1' WHERE `i1il4_detsad_items`.`id` = '{$_POST['item_id']}';";
    $conn->query($sql4);
} else {
    $clinicID = $conn->query("SELECT t1.id,t1.section_id,t1.name,t1.vote,t1.rate,t1.preview_src,t1.comments,t2.locality,t1.description
FROM i1il4_detsad_items as t1
INNER JOIN i1il4_detsad_address as t2 ON t2.item_id = t1.id
WHERE  t1.description != ''
and t1.sid = 0
GROUP BY t1.id 
limit 1");
    $result_clinicID = $clinicID->fetchAll(PDO::FETCH_ASSOC);

    if (empty($result_clinicID)) {
        $sql3 = "UPDATE `i1il4_detsad_items` set sid = 0 WHERE `sid` = '1' and description != '' ";
        $conn->query($sql3);
    } else {
        if (empty($_SERVER['REMOTE_ADDR'])) {
            foreach ($result_clinicID as $value) {
                $id_yandex_rr = explode('|', $value['description']);
                getReviews($value['id'], $id_yandex_rr[0], $conn);
                $sql4 = "UPDATE `i1il4_detsad_items` SET `sid` = '1' WHERE `i1il4_detsad_items`.`id` = '{$value['id']}' and description != '';";
                $conn->query($sql4);
            }
        }
    }

}

function clearCache($value)
{
    if (!empty($value)) {
        $data = parse_url($value);
        $filename = md5('GET|detskysad.com|' . $data['path']);
        $patch = '/var/cache/nginx/detsad/' . substr($filename, -1) . '/' . substr($filename, -3, 2) . '/' . $filename;

        if (file_exists($patch)) {
            //file_put_contents('/var/www/med/components/com_vuz/views/item/tmpl/test.txt', $patch. PHP_EOL, FILE_APPEND | LOCK_EX);
            unlink($patch);
            return true;
        }
    }
    return false;
}

function getReviews($item_id, $id_yandex, $conn)
{
    $clinic = $conn->query("SELECT t1.id,t1.description,t1.section_id,t1.name,t1.vote,t1.rate,t1.preview_src,t1.comments,t2.locality
FROM i1il4_detsad_items as t1
INNER JOIN i1il4_detsad_address as t2 ON t2.item_id = t1.id
WHERE t1.id = '{$item_id}'
GROUP BY t1.id ");
    $result_clinic = $clinic->fetchAll(PDO::FETCH_ASSOC);
    $url = "https://yandex.ru/maps-reviews-widget/$id_yandex?comments";
    $refer = 'https://yandex.ru/search/?lr=213&clid=2270453&win=476&text=' . urlencode($url);

    $pars = getPageByUrl($url, $refer);
    $pq = phpQuery::newDocument($pars);
    $names = $pq->find('.comment');
    //$names = $pq->find('.business-review-view__info');
    $count = 0;
    $summ_rate = 0;
    foreach ($names as $name) {

        $namePC = pq($name);
        $author = addslashes($namePC->find('.comment__header')->find('.comment__name')->text());
        //var_dump($author);
        if (empty($author)) {
            $author = 'Гость';
        }
        $star = count($namePC->find('.comment__stars')->find('._empty'));
        switch ($star) {
            case 0:
                $rate = "5";
                break;
            case 1:
                $rate = "4";
                break;
            case 2:
                $rate = "3";
                break;
            case 3:
                $rate = "2";
                break;
            case 4:
                $rate = "1";
                break;
        }
        //var_dump($rate);  2022-03-31 22:06:44
        $dateStr = $namePC->find('.comment__date')->text();
        preg_match('~^\d+.\d+.(\d+)$~m', $dateStr, $match);
        if(empty($match[1])){
            $dateStr = $dateStr.date('Y');
        }
        $dateStr = str_replace(array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"), array(".01.",".02.",".03.",".04.",".05.",".06.",".07.",".08.",".09.",".10.",".11.",".12."), $dateStr);
        $dateStr = str_replace(' ', '', $dateStr);
        $timestamp = strtotime($dateStr);
        $hours = rand(10,23).':'.rand(10,59).':'.rand(10,59);
        $date = date("Y-m-d",$timestamp).' '.$hours;
        //var_dump($date);

        $review = addslashes($namePC->find('.comment__text')->text());

        try {
            if (strlen($review) > 100) {

                $proverka3 = $conn->query("SELECT id FROM `i1il4_comments_items` WHERE `object_group` = 'com_detsad' AND `object_id` = '{$result_clinic[0]['id']}' AND `username` = '{$author}' AND `created` = '{$date}'");
                $result_proverka3 = $proverka3->fetchAll(PDO::FETCH_ASSOC);

                $str100 = mb_substr($review, 0, 100);

                $proverka4 = $conn->query("SELECT * FROM `i1il4_comments_items` WHERE `object_group` = 'com_detsad' AND `object_id` = '{$result_clinic[0]['id']}' AND `description` LIKE '{$str100}%' ORDER BY `i1il4_comments_items`.`id` DESC limit 100");
                $result_proverka4 = $proverka4->fetchAll(PDO::FETCH_ASSOC);


                if (empty($result_proverka3) && empty($result_proverka4)) {
                    $review = str_replace('o', 'о', $review);
                    //добавление в рассылку уведомление для агента клиники если он есть


                    //удаляем левые комменты если есть
                    $sql = "delete FROM `i1il4_comments_items` WHERE `object_group` = 'com_detsad' AND `object_id` = '{$result_clinic[0]['id']}' AND (`ip` = '13.13.13.14' or `ip` = '13.13.13.15');";
                    $conn->query($sql);
                    if(preg_match('~1970~m', $date)){
                        $min = strtotime('01.01.2020');
                        $max = strtotime('01.01.2022');
                        $val = rand($min, $max);
                        $date = date("Y-m-d H:i:s",$val);
                    }
                    $sql = "INSERT INTO `i1il4_comments_items` (`id`, `object_group`, `object_id`, `created`, `ip`, `user_id`, `rate`, `country`, `status`, `username`, `email`, `isgood`, `ispoor`, `description`, `images`)
                    VALUES (NULL, 'com_detsad', '{$result_clinic[0]['id']}', '{$date}', '13.13.13.100', '0', '{$rate}', '{$result_clinic[0]['locality']}', '1', '{$author}', '1313@mail.ru', '0', '0', '{$review}', '0');";

                    $conn->query($sql);
                    $count++;
                    $summ_rate += (int)$rate;

                }
            } else {
                continue;
            }
        } catch (Exception $e) {
            //file_put_contents('/var/www/schools/components/com_vuz/views/item/tmpl/test.txt', $e. PHP_EOL, FILE_APPEND | LOCK_EX);
            continue;
        }
    }

    if (!empty($count)) {
        $today = date("Y-m-d H:i:s");
        $rate = $result_clinic[0]['rate'] + $summ_rate;
        $vote = $result_clinic[0]['vote'] + $count;
        $comments = $result_clinic[0]['comments'] + $count;
        if ($vote != 0) {
            $average = round($rate / $vote, 4);
        } else {
            $average = 0;
        }

        $sql2 = "UPDATE i1il4_detsad_items SET rate = rate + '{$rate}', vote = vote + '{$vote}', average = vote/(vote+10)*rate/vote+10/(vote+10)*3.922, `modified` = '{$today}', comments = (SELECT COUNT(*) FROM i1il4_comments_items WHERE object_id ='{$result_clinic[0]['id']}'  AND status = 1) WHERE id = '{$result_clinic[0]['id']}' LIMIT 1;";
        $conn->query($sql2);
    }


    phpQuery::unloadDocuments();
    gc_collect_cycles();
}

