<?php

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
    if (!check_array($result))
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

/**
 * 生成uid
 * @param unknown $prefix
 * @return string
 */
function guid($prefix) {
	$str = md5(uniqid(mt_rand(), true));
	$uuid = substr($str, 0, 8) . '-';
	$uuid .= substr($str, 8, 4) . '-';
	$uuid .= substr($str, 12, 4) . '-';
	$uuid .= substr($str, 16, 4) . '-';
	$uuid .= substr($str, 20, 12);
	return $prefix . $uuid;
}
