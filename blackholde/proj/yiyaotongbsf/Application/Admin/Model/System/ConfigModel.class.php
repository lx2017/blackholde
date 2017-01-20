<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/14
 * Time: 下午5:00
 */
namespace Admin\Model\System;

use Think\Model;

class ConfigModel extends Model
{
    protected $_validate = array(
        array('value', '1,2000', '标识不能为空', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH)
    );

    /**
     * 根据group获取配置信息
     * @param $group
     * @return mixed
     */
    public function getConfigByGroup($group)
    {
        return $this->where(array('status' => 1, 'group' => $group))->field('name,title,extra,value,remark,type')->order('sort')->select();
    }

    /**
     * 更新value值通过name
     * @param $name
     * @param $value
     */
    public function updateValueByName($name, $value)
    {
        $this->where($name)->setField('value', $value);
    }
}