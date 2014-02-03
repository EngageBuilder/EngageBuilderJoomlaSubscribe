<?php 
error_reporting(1);
require_once('EbSDK.class.php');

$eb = new EbSDK('NTJlODhiOTcwYjcxMQ');

$result = $eb->validateKey();

var_dump($result);
