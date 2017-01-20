<?php
/**
 * 医生-咨询管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Model\Doctor;
use Think\Model;
use Think\Page;

class DoctorConsultModel extends Model
{
    private $size = 10;

    /**
     * 得到分页数据
     * @param array $where
     * @return array
     */
    public function pageList($where = array()){
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
        $rows = $this->alias('a')->field('a.*,b.name as doctor,c.name as patient')->order('a.id desc')->where($where)->join(' LEFT JOIN __DOCTOR__ b on a.doctor_id=b.id')->join(' LEFT JOIN __PATIENT__ c on a.patient_id=c.id')->limit($first, $pageTool->listRows)->select();
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
     * 修改数据状态值
     * @param $id int 主键id
     * @param $value mixed 修改后的值
     * @param string $key 修改的字段名,默认status
     * @return bool
     */
    public function changeStatus($id,$value,$key='status'){
        return $this->where(array('id'=>$id))->setField($key,$value);
    }

    /**
     * 添加数据
     * @param $requestData
     * @return bool
     */
    public function addData($requestData){
        //判断是否存在
        $where = array('doctor_id'=>$requestData['doctor_id'],'patient_id'=>$requestData['patient_id']);
        $count = $this->where($where)->count();
        if($count){
            //更新
            $rst = M('DoctorConsult')->where($where)->save(array('date'=>date('Y-m-d H:i:s')));
        }else{
            //添加
            $requestData['date2'] = date('Y-m-d H:i:s');
            $rst = $this->add($requestData);
            //更新医生信息
            if($rst){
                D('CommonDoctor')->putToCount(3,$requestData['doctor_id']);
            }
        }
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
    public function saveData($requestData){
        //判断是否存在
        $where = array('doctor_id'=>$requestData['doctor_id'],'patient_id'=>$requestData['patient_id']);
        $count = $this->where($where)->count();
        if($count){
            //更新
            $rst = M('DoctorConsult')->where($where)->save(array('date'=>date('Y-m-d H:i:s')));
        }else{
            //添加
            $requestData['date2'] = date('Y-m-d H:i:s');
            unset($requestData['id']);//不要主键
            $rst = $this->add($requestData);
            //更新医生信息
            if($rst){
                D('CommonDoctor')->putToCount(3,$requestData['doctor_id']);
            }
        }
        if($rst===false){
            $this->error = '添加失败';
            return false;
        }
        return true;
    }
}