<?php
use Beanbun\Beanbun;
use Beanbun\Lib\Db;

require_once(__DIR__ . '../../../vendor/autoload.php');

// 数据库配置
Db::$config['zhihu'] = [
    'server' => '127.0.0.1',
    'port' => '3306',
    'username' => 'root',
    'password' => '123456789',
    'database_name' => 'douban',
    'charset' => 'utf8',
];

function getProxies($beanbun) {
    $client = new \GuzzleHttp\Client();
    $beanbun->proxies = [];
    $pattern = '/<tr><td>(.+)<\/td><td>(\d+)<\/td>(.+)(HTTP|HTTPS)<\/td><td><div class=\"delay (fast_color|mid_color)(.+)<\/tr>/isU';

    for ($i = 1; $i < 5; $i++) {
        $res = $client->get("http://www.mimiip.com/gngao/$i");
        $html = str_replace(['  ', "\r", "\n"], '', $res->getBody());
        preg_match_all($pattern, $html, $match);
        foreach ($match[1] as $k => $v) {
            $proxy = strtolower($match[4][$k]) . "://{$v}:{$match[2][$k]}";
            echo "get proxy $proxy ";
            try {
                $client->get('http://mail.163.com', [
                    'proxy' => $proxy,
                    'timeout' => 6
                ]);
                $beanbun->proxies[] = $proxy;
                echo "success.\n";
            } catch (\Exception $e) {
                echo "error.\n";
            }
        }
    }
}

$beanbun = new Beanbun;
$beanbun->name = 'douban';
$beanbun->count = 5;
$beanbun->interval = 4;
$beanbun->seed = [
    'https://www.douban.com/group/pudongzufang/discussion?start=0',
    'https://www.douban.com/group/pudongzufang/discussion?start=25',
    'https://www.douban.com/group/pudongzufang/discussion?start=50',
    'https://www.douban.com/group/pudongzufang/discussion?start=75',
    'https://www.douban.com/group/pudongzufang/discussion?start=100',
];
$beanbun->urlFilter = [
    '/https:\/\/www.douban.com\/group\/topic\/(\d*)\//'
];

// 设置队列
$beanbun->setQueue('memory', [
    'host' => '127.0.0.1',
    'port' => '2207'
]);

$beanbun->logFile = __DIR__ . '/douban_access.log';

//if ($argv[1] == 'start') {
//    getProxies($beanbun);
//}

//$beanbun->startWorker = function($beanbun) {
//    // 每隔半小时，更新一下代理池
//    Beanbun::timer(1800, 'getProxies', $beanbun);
//};

$beanbun->beforeDownloadPage = function ($beanbun) {
    // 在爬取前设置请求的 headers
    $beanbun->options['headers'] = [
        'Host' => 'www.douban.com',
        'Connection' => 'keep-alive',
        'Cache-Control' => 'max-age=0',
        'Upgrade-Insecure-Requests' => '1',
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
        'Accept' => 'application/json, text/plain, */*',
        'Accept-Encoding' => 'gzip, deflate, sdch, br',
        'authorization' => 'oauth c3cef7c66a1843f8b3a9e6a1e3160e20',
    ];

    if (isset($beanbun->proxies) && count($beanbun->proxies)) {
        $beanbun->options['proxy'] = $beanbun->proxies[array_rand($beanbun->proxies)];
    }
};

$beanbun->afterDownloadPage = function ($beanbun) {
    file_put_contents(__DIR__ . '/file/' . md5($beanbun->url), $beanbun->page);
};

$beanbun->discoverUrl = function(){};

$beanbun->start();