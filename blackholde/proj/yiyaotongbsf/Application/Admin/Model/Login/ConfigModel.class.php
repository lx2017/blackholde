<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/14
 * Time: 下午2:23
 */
namespace Admin\Model\Login;

use Think\Model;

/**
 * 配置模型
 * Class ConfigModel
 * @package Admin\Model\Login
 */
class ConfigModel extends Model
{
    /**
     * 获取配置信息
     * @return array
     */
    public function lists()
    {
        $map = array('status' => 1);
        $data = $this->where($map)->field('type,name,value')->select();

        $config = array();
        if ($data && is_array($data)) {
            foreach ($data as $value) {
                $config[$value['name']] = $this->parse($value['type'], $value['value']);
            }
        }
        return $config;
    }

    /**
     * 根据配置类型解析配置
     * @param $type 配置类型
     * @param $value 配置值
     * @return array
     */
    private function parse($type, $value)
    {
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if (strpos($value, ':')) {
                    $value = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k] = $v;
                    }
                } else {
                    $value = $array;
                }
                break;
        }
        return $value;
    }
}