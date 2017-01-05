<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/8
 * Time: 上午11:19
 */
namespace Admin\Model\Employee;

use Think\Model;

/**
 * 员工和组的关系
 * Class AuthGroupAccessModel
 * @package Admin\Model\Employee
 */
class AuthGroupAccessModel extends Model
{
    /**
     * 根据用户id和组ids查询数量
     * @param $uid
     * @param array $groupIds
     */
    public function getCountByUidAndGroupIds($uid, array $groupIds)
    {
        return $this->where(array('uid' => $uid, 'group_id' => array('in', $groupIds)))->count();
    }

    /**
     * 根据uid查询用户所属分组
     * @param $uid
     * @return mixed
     */
    public function getAuthGroupAccessByUid($uid)
    {
        return $this->where(array('uid' => $uid))->select();
    }

    /**
     * 把用户添加到用户组,支持批量添加用户到用户组
     * @author mafengli
     *
     *
     */
    public function addToGroup($uid, $gid)
    {
        $uid_arr = array_diff($uid, array(C('USER_ADMINISTRATOR')));
        $add = array();
        foreach ($uid_arr as $u) {
            $count = $this->getCountByGroupIdAndUid($gid, (int)$u);
            if ($count == 0) {
                $add[] = array('group_id' => $gid, 'uid' => (int)$u);
            }
        }
        $flag = true;
        if (!empty($add)) {
            $flag = $this->addAll($add);
        }
        if ($flag) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 根据groupId和uid进行查询
     * @param $groupId 组id
     * @param $uid 用户id
     */
    public function getCountByGroupIdAndUid($groupId, $uid)
    {
        return $this->where(array('group_id' => $groupId, 'uid' => $uid))->count();
    }

    /**
     * 将用户从用户组中移除
     * @param int|string|array $gid 用户组id
     * @param int|string|array $cid 分类id
     * @author mafengli
     */
    public function removeFromGroup($uid, $gid)
    {
        return $this->where(array('uid' => $uid, 'group_id' => $gid))->delete();
    }
}
