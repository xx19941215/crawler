<?php
use Beanbun\Beanbun;
use Beanbun\Lib\Helper;

require_once(__DIR__ . '/../../vendor/autoload.php');

$beanbun = new Beanbun;
$beanbun->name = 'qiubai';
$beanbun->count = 5;
$beanbun->seed = 'http://www.qiushibaike.com/';
$beanbun->max = 30;
$beanbun->logFile = __DIR__ . '/qiubai_access.log';
$beanbun->urlFilter = [
    '/http:\/\/www.qiushibaike.com\/article/(\d*)/'
];
// 设置队列
$beanbun->setQueue('memory', [
    'host' => '127.0.0.1',
     'port' => '2207'
 ]);
$beanbun->afterDownloadPage = function($beanbun) {
    file_put_contents(__DIR__ . '/file/' . md5($beanbun->url), $beanbun->page);
};
$beanbun->start();