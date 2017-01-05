<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/8
 * Time: 上午11:08
 */
namespace Admin\Model\Employee;

use Think\Model;

/**
 * 组和功能模块之间的关系表
 * Class AuthGroupModuleModel
 * @package Admin\Model\Employee
 */
class AuthGroupModuleModel extends Model
{
    // protected $tableName=''
    /**
     * 根据功能key值查询信息
     * @param $key
     * @return mixed
     */
    public function getInfosByModuleKey($key)
    {
        return $this->where(array('module_key' => $key))->select();
    }

    /**
     * 根据组ids查询对应的功能模块信息
     * @param array $groupIds
     * @return mixed
     */
    public function getModuleKeyByGroupIds(array $groupIds)
    {
        return $this->where(array('group_id' => array('in', $groupIds)))->distinct(true)->field('module_key')->select();
    }

    /**
     * 根据groupid查询数据
     * @param $groupId
     * @return mixed
     */
    public function getAllByGroupId($groupId)
    {
        return $this->where(array('group_id' => $groupId))->select();
    }

    /**
     * 更新组所对应的权限信息
     * @param $moduleKeys
     * @param $groupId
     * @return bool
     */
    public function updateAuthGroupMuduleByGroupId($moduleKeys, $groupId)
    {
        if (!empty($moduleKeys)) {
            M()->startTrans();
            $flag = $this->where(array('group_id' => $groupId))->delete();
            if ($flag == 0) {
                $flag = true;
            }
            $data = [];
            foreach ($moduleKeys as $k => $mk) {
                $data[$k]['group_id'] = $groupId;
                $data[$k]['module_key'] = $mk;
            }
            $flag1 = $this->addAll($data);
            if ($flag && $flag1) {
                M()->commit();
                return true;
            } else {
                M()->rollback();
                return false;
            }
        } else {
            $flag = $this->where(array('group_id' => $groupId))->delete();
            if ($flag == 0 || $flag) {
                return true;
            } else {
                return false;
            }
        }

    }
}