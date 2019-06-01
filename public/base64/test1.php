<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/28
 * Time: 下午3:00
 */

$data = explode(',',$_POST['data']);
if (file_put_contents('1.jpg',base64_decode(end($data)))){
    echo '成功';
}else{
    return '失败';
}