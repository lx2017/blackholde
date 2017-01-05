<?php
/**
 * 医生-患者管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Model\Doctor;
use Think\Model;
use Think\Page;

class DoctorPatientModel extends Model
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
//        $rows = $this->order('id desc')->where($where)->limit($first, $pageTool->listRows)->select();
        $rows = $this->alias('a')->field('a.*,b.name as clinic,b.address')->order('a.id desc')->where($where)->join(' LEFT JOIN __CLINIC__ b on a.clinic_id=b.id')->limit($first, $pageTool->listRows)->select();
        //得到咨询量,预约,诊断,
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
        //生成编号
        $requestData['number'] = get_unique_number();
        //处理标签
        $requestData['label'] = encodeLabel($requestData['labels']);
        //处理时间
        $requestData['application_time'] = strtotime($requestData['application_time']);
        $requestData['public_time'] = strtotime($requestData['public_time']);
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
    public function saveData($requestData){
        //处理标签
        $requestData['label'] = encodeLabel($requestData['labels']);
        //处理时间
        $requestData['application_time'] = strtotime($requestData['application_time']);
        $requestData['public_time'] = strtotime($requestData['public_time']);
        $rst = $this->save($requestData);
        if($rst===false){
            $this->error = '编辑失败';
            return false;
        }
        return true;
    }

    /**
     * 检测mobile的唯一性
     * @param $mobile
     * @param $type int 非0为id,0则表示添加
     * @return bool
     */
    public function checkMobile($mobile,$type){
        $where = array('mobile'=>$mobile);
        if($type){
            $where['id'] = array('neq',$type);
        }
        $count = $this->where($where)->count();
        return $count?false:true;
    }

}