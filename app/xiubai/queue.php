<?php
require_once(__DIR__ . '/../../vendor/autoload.php');
// 启动队列
\Beanbun\Queue\MemoryQueue::server();