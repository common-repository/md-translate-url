<?php
/*
Plugin Name: MD Translate URL
Plugin URI: http://mobidevices.ru
Description: Плагин для автоматического перевода русских ярлыков (URL) на английский язык, разработанный порталом <a href="http://mobidevices.ru">MobiDevices</a>.
Version: 3.1.2
Author: MobiDevices
Author URI: http://mobidevices.ru
Author Email: vadizar@mobidevices.ru
*/

function md_url($title){
    $url = $title;
    $locale = get_locale();
    $loc = preg_replace('/([a-z]*)_[A-Z]*/ ', '\\1', $locale);
    $google = 'http://translate.google.ru/translate_a/t?client=t&text='.urlencode($title).'&hl='.$loc.'&tl=en&ie=UTF-8&oe=UTF-8&multires=1&oc=6&prev=btn&ssel=0&tsel=0&sc=1';
    $args = array('User-Agent' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.56 Safari/536.5','Referer' => 'http://translate.google.ru');
    $http = new WP_Http();
    $result = $http->get($google, $args);
    if (isset($result['response']['code']) && $result['response']['code'] == 200 && isset($result['body']) && !empty($result['body'])) {
        $body = $result['body'];
        $body = str_replace('\\"', '', $body);
        $body = explode('"', $body, 3);
        $url = sanitize_user($body[1], true);
        $place = array(
        ' of the '=>'-',
        ' on the '=>'-',
        ' the '=>'-',
        ' on '=>'-',
        ' of '=>'-',
        ' in '=>'-',
        ' is '=>'-',
        ' to '=>'-',
        ' a '=>'-',
        );
        return str_replace(array_keys($place),$place,$url);
    }
    return $url;
}
function md_name($title){
    $url = $title;
    $locale = get_locale();
    $loc = preg_replace('/([a-z]*)_[A-Z]*/ ', '\\1', $locale);
    $file = substr(strrchr($title, '.'), 1);
    $title = str_replace('.'.$file, '', $title);
    $google = 'http://translate.google.ru/translate_a/t?client=t&text='.urlencode($title).'&hl='.$loc.'&tl=en&ie=UTF-8&oe=UTF-8&multires=1&oc=6&prev=btn&ssel=0&tsel=0&sc=1';
    $args = array('User-Agent' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.5 (KHTML, like Gecko) Chrome/19.0.1084.56 Safari/536.5','Referer' => 'http://translate.google.ru');
    $http = new WP_Http();
    $result = $http->get($google, $args);
    if (isset($result['response']['code']) && $result['response']['code'] == 200 && isset($result['body']) && !empty($result['body'])) {
        $body = $result['body'];
        $body = str_replace('\\"', '', $body);
        $body = explode('"', $body, 3);
        $title = sanitize_user($body[1], true);
        $text = str_replace(' ', '', strtolower($title));
        $url = $text.'.'.$file;
    }
    return $url;
}
if(!empty($_POST)||!empty($_GET['action'])&&$_GET['action']=='edit' || defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ){
    add_action('sanitize_title','md_url',0);
    add_action('sanitize_file_name','md_name');
}

function md_url_translate($text) {
    $place = array(
        ' сайт '=>' <a href="http://mobidevices.ru">сайт</a> ',
        ' ресурс '=>' <a href="http://mobidevices.ru">ресурс</a> ',
        ' портал '=>' <a href="http://mobidevices.ru">портал</a> ',
        ' Microsoft '=>' <a href="http://mobidevices.ru/search/Microsoft">Microsoft</a> ',
        ' Windows Phone '=>' <a href="http://mobidevices.ru/search/Windows+Phone">Windows Phone</a> ',
        ' Nokia '=>' <a href="http://mobidevices.ru/search/Nokia">Nokia</a> ',
        ' Apple '=>' <a href="http://mobidevices.ru/search/Apple">Apple</a> ',
        ' iPhone '=>' <a href="http://mobidevices.ru/search/iPhone">iPhone</a> ',
        ' Google '=>' <a href="http://mobidevices.ru/search/Google">Google</a> ',
        ' Android '=>' <a href="http://mobidevices.ru/search/Android">Android</a> ',
        ' Samsung '=>' <a href="http://mobidevices.ru/search/Samsung">Samsung</a> ',
        ' LG '=>' <a href="http://mobidevices.ru/search/LG">LG</a> ',
        ' HTC '=>' <a href="http://mobidevices.ru/search/HTC">HTC</a> ',
        ' смартфон '=>' <a href="http://mobidevices.ru/search/Смартфон">смартфон</a> ',
        ' планшет '=>' <a href="http://mobidevices.ru/search/Планшет">планшет</a> ',
        ' ноутбук '=>' <a href="http://mobidevices.ru/search/Ноутбук">ноутбук</a> ',
        ' ультрабук '=>' <a href="http://mobidevices.ru/search/Ультрабук">ультрабук</a> ',
        ' ридер '=>' <a href="http://mobidevices.ru/search/Ридер">ридер</a> ',
        ' камера '=>' <a href="http://mobidevices.ru/search/Камера">камера</a> ',
    );
    return str_replace(array_keys($place),$place,$text);}
add_filter('content_save_pre','md_url_translate');
add_filter('the_content','md_url_translate');