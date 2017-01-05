<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/8
 * Time: 上午10:36
 */
namespace Admin\Model\Employee;

use Think\Model;

/**
 * 员工管理中用到的功能模块模型
 * Class ModuleModel
 * @package Admin\Model\Employee
 */
class ModuleModel extends Model
{
	protected $tableName='module';
    /**
     * 根据url和key查询module数量
     * @param $url 请求的url
     * @param $key 标识
     * @return mixed 返回module数量
     */
    public function getCountByUrlAndKey($url, $key)
    {
        $where = array('url' => $url, 'key' => $key, 'hide' => 0);
        return $this->where($where)->count();
    }

    /**
     * 根据功能标识，查询hide＝0,type=1的菜单信息并按sort升序查找module
     * @param array $moduleKeys 功能模块标识集合
     * @return mixed
     */
    public function getMenuByModuleKeys(array $moduleKeys)
    {
        return $this->where(array('key' => array('in', $moduleKeys), 'hide' => 0, 'type' => 1))->order('sort asc')->field('title,url,key,pid')->select();
    }


    /**
     * 根据功能模块标识集合和父标识查询菜单
     * @param array $moduleKeys 功能模块标识集合
     * @param $parent 父标识
     * @return mixed
     */
    public function getMenuByModuleKeysAndParent(array $moduleKeys, $parent)
    {
        $where = array('key' => array('in', $moduleKeys), 'pid' => $parent, 'hide' => 0, 'type' => 1);
        return $this->where($where)->order('sort asc')->field('title,url,key')->select();
    }

    /**
     * 根据父key查询操作信息
     * @param $parent 父key
     * @return mixed
     */
    public function getActionsByParent($parent)
    {
        return $this->where(array('hide' => 0, 'pid' => $parent, 'type' => 2))->order('sort asc')->field('url,key')->select();
    }

    /**
     * 根据key查询模块信息
     * @param $key
     * @return Model
     */
    public function getModuleByKey($key)
    {
        return $this->where(array('key' => $key, 'hide' => 0))->find();
    }
	
	/**
	 * 
	 *2016-08-05日添加左侧菜单，新增
     * 获得pid=0的顶级菜单
     * @param 
     * @return array
     */
    public function getTop()
    {
        return $this->select();
    }
	
	/**
	 * 将数据按照所属关系封装 
	 * @param $tree 传入的数组
	 * @param $rootId 传入的最顶级id：0
	 * @return mixed 递归返回的所有内容
	 * */  
	public function arr2tree1($tree, $rootId = 0) {  
	    $return = array();
	    foreach($tree as $k=> $leaf) {
	        if($leaf['pid'] == $rootId) {
	            foreach($tree as $kk=> $subleaf) {
	                if($subleaf['pid'] == $leaf['key']) {
	                    $leaf['children'] = $this->arr2tree1($tree, $leaf['key']);  
	                    break;  
	                }  
	            }  
	            $return[] = $leaf;  
	        }  
	    }  
	    return $return;  
	}  
	/**
	 * 与上面的arr2tree1函数共同使用，在页面生成数据树
	 * @param $parent 传入的数组
	 * @param $deep 传入的最顶级id：0
	 * @return mixed 递归返回的所有内容
	 */
	 
	public 	function getChildren($parent,$deep=0) {
	    foreach($parent as $row) {
	        $data[] = array("title"=>$row['title'], "sort"=>$row['sort'],"pid"=>$row['pid'],"tip"=>$row['tip'],"hide"=>$row['hide'],"key"=>$row['key'],"type"=>$row['type'],"url"=>$row['url'],'deep'=>$deep);
	        if ($row['children']) {
	            $data = array_merge($data, $this->getChildren($row['children'], $deep+1));
	        }
	    }
	    return $data;
	}

	/**
	 * 添加模块
	 * @author 阿里
	 * @date 2016/09/20
	 */
	public function addData(){
       return true;
	}
}