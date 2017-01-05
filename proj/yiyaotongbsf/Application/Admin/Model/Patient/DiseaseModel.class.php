<?php
/**
 * 疾病库管理
 * Created by PhpStorm.
 * User: jiaolele
 */
namespace Admin\Model\Patient;
use Think\Model;
use Think\Page;

class DiseaseModel extends Model
{
    private $size = 10;

    /**
     * 得到分页数据
     * @param array $where
     * @return array
     */
    public function getAdminList($where = array()){
        //得到分页信息
        $count = $this->where($where)->count();
        $pageTool = new Page($count, $this->size);
        //设置分页信息
        $page_config = C('PAGE_CONFIG');
        foreach($page_config as $k=>$v){
            $pageTool->setConfig($k,$v);
        }
        $pageHtml = $pageTool->show();
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $this->size;
        }
        //查询数据
        $rows = $this->alias('a')->field('a.*,b.type_name as keshi,c.type_name as renqun')->order('a.id desc')->where($where)->join(' LEFT JOIN __DISEASE_CATEGARY__ b on a.section_office_id=b.id')->join(' LEFT JOIN __DISEASE_CATEGARY__ c on a.crowd_id=c.id')->limit($first, $pageTool->listRows)->select();
        //返回结果
       return array('rows' => $rows, 'pageHtml' => $pageHtml);
    }

    /**
     * 删除数据
     * @param $id
     * @param $file string 数据库中的文件字段名,多个逗号分隔
     * @return bool
     */
    public function remove($id,$file=''){
        //删除文件
        if($file){
            $rst = $this->field($file)->where(array('id'=>array('in',$id)))->select();
            if($rst){
                //组建文件数组
                $keys = explode(',',$file);
                $arr = array();
                foreach($rst as $v){
                    foreach($keys as $item){
                        if(isset($v[$item])) $arr[] = $v[$item];
                    }
                }
                if($arr) deleteFile(1,$arr);
            }
        }
        //删除信息
        $rst = $this->delete($id);
        if($rst===false){
            $this->error = '删除失败';
            return false;
        }
        return true;
    }


    /**
     * 添加数据
     * @param $requestData
     * @return bool
     */
    public function addDisease($requestData){
        //创建时间
        $requestData['date'] = NOW_TIME;
        $rst = $this->add($requestData);
        if($rst===false){
            $this->error = '添加失败';
            return false;
        }
        return true;
    }

    /**
     * 编辑数据
     * @param $requestData
     * @return bool
     */
    public function saveDisease($requestData){

        $rst = $this->save($requestData);
        if($rst===false){
            $this->error = '编辑失败';
            return false;
        }
        return true;
    }


}