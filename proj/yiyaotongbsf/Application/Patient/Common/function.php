<?php
use JPush\Client as JPush;
/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1) {
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string $category 分类ID
 * @param  integer $status 数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1) {
    static $count;
    if (!isset($count[$category])) {
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id) {
    static $count;
    if (!isset($count[$id])) {
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url) {
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;
        default:
            $url = U($url);
            break;
    }
    return $url;
}

/**
 * 是否数组
 * @param $params
 * @return boolean
 * @author LCR
 */
function check_array($params) {
    return (!is_array($params) || !count($params)) ? false : true;
}

/**
 * 
 * 返回数组中某一元素为主键的一个新数组
 * @param  array $array 数组
 * @param string $key 元素键
 * @return array
 * @author LCR
 */
function set_array_key($result, $field) {
    if (!isArray($result))
        return array();
    $list = array();
    foreach ($result as $key => $val) {
        $list[$val[$field]] = $val;
    }
    return $list;
}

/**
 * 验证字符串是否是date格式
 * @param  string $str 字符值
 * @param string $format 格式
 * @return boolean
 * @author LCR
 */
function is_date($str, $format = "Y-m-d") {
    $strArr = explode("-", $str);
    if (empty($strArr)) {
        return false;
    }
    foreach ($strArr as $val) {
        if (strlen($val) < 2) {
            $val = "0" . $val;
        }
        $newArr[] = $val;
    }
    $str = implode("-", $newArr);
    $unixTime = strtotime($str);
    $checkDate = date($format, $unixTime);
    if ($checkDate == $str) {
        return true;
    } else {
        return false;
    }
}

/**
 * 
 * @param type $lat1 纬度1
 * @param type $lng1    经度1
 * @param type $lat2    纬度2
 * @param type $lng2    经度2
 * @return type
 */
function get_distance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6367000; //approximate radius of earth in meters

    /*
      Convert these degrees to radians
      to work with the formula
     */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
      Using the
      Haversine formula

      http://en.wikipedia.org/wiki/Haversine_formula

      calculate the distance
     */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}

function mbSub( $conect , $min = 0 , $leng = 100 , $en='utf-8')
{
    $output = mb_substr($conect , $min , $leng , $en);
    if(mb_strlen($conect,$en) > $leng ){
        $output .= "...";
    }
    return $output;
}





function distance( $lat2, $lng2, $miles = true)
{

//    $lat1=lat;
//    $lng1=lng;
  $lat1=40.05685561073758;
   $lng1=116.30775539540981;
    $pi80 = M_PI / 180;
    $lat1 *= $pi80;
    $lng1 *= $pi80;
    $lat2 *= $pi80;
    $lng2 *= $pi80;
    $r = 6372.797; // mean radius of Earth in km
    $dlat = $lat2 - $lat1;
    $dlng = $lng2 - $lng1;
    $a = sin($dlat/2)*sin($dlat/2)+cos($lat1)*cos($lat2)*sin($dlng/2)*sin($dlng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $km = $r * $c;
    $miles=number_format(((float)$miles ? ($km * 0.621371192) : $km));
    return $miles;
}




/**
+----------------------------------------------------------
 * 对查询结果集进行排序
+----------------------------------------------------------
 * @access public
+----------------------------------------------------------
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param string $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
+----------------------------------------------------------
 * @return array
+----------------------------------------------------------
 */
function list_sort($list,$field, $sortby='asc') {
    if(is_array($list)){
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc':// 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ( $refer as $key=> $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}
/**
 * 将数据先转换成json,然后转成array
 */
function json_array($result){
    $result_json = json_encode($result);
    return json_decode($result_json,true);
}


/**
 * 向特定设备推送消息
 * @param array $regid 特定设备的设备标识
 * @param string $message 需要推送的消息
 */
function sendNotifySpecial($regid,$message){
    require_once "./vendor/autoload.php";
    $config = C('JPUSH_CONFIG');
    $app_key = $config['key'];
    $master_secret = $config['secret'];
    $client = new \JPush\Client($app_key, $master_secret);
    $result = $client->push()->setPlatform('all')->addRegistrationId($regid)->setNotificationAlert($message)->send();
    return json_array($result);
}

/**
 * 得到各类统计数据
 * @param array $msgIds 推送消息返回的msg_id列表
 */
function reportNotify($msgIds){
    require_once "./vendor/autoload.php";
    $config = C('JPUSH_CONFIG');
    $app_key = $config['key'];
    $master_secret = $config['secret'];
    $client = new \JPush\Client($app_key, $master_secret);
    $response = $client->report()->getReceived($msgIds);
    return json_array($response);
}