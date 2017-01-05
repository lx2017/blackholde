<?php
/**
 * 诊所管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Model\Clinic;
use Think\Model;
use Think\Page;
use Admin\Model\Doctor\DoctorModel;

class ClinicModel extends Model
{
    private $size = 10;

    /**
     * 得到分页数据
     * @param array $where
     * @return array
     */
    public function pageList($where = array()){
        $where['a.is_delete'] = 1;//正常账户
        //得到分页信息
        $count = $this->alias('a')->where($where)->count();
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
        $where['b.is_manager'] = 1;//负责人信息
        $where['b.status'] = 0;
        //查询数据
        $rows = $this->alias('a')->field('a.*,b.name as manager_name,b.mobile as manager_mobile')->order('a.id desc')->where($where)->join(' LEFT JOIN __DOCTOR__ b on a.id=b.clinic_id')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows, 'pageHtml' => $pageHtml);
    }

    /**
     * 修改数据状态值
     * @param $id int 主键id
     * @param $value mixed 修改后的值
     * @param string $key 修改的字段名,默认status
     * @return bool
     */
    public function changeStatus($id,$value,$key='status'){
        $rst = $this->where(array('id'=>array('in',$id)))->setField($key,$value);
        if($value==0){//删除账户时,删除医生
            //得到诊所的所有医生
            $ids = M('Doctor')->where(array('clinic_id'=>array('in',$id),'status'=>array('neq',2)))->field('id')->select();
            if($ids){
                $ids = implode(',',array_column($ids,'id'));
                $model = new DoctorModel();
                $model->changeStatus($ids,2);//删除医生
            }
        }
        return $rst;
    }

    /**
     * 添加数据
     * @param $requestData
     * @return bool
     */
    public function addData($requestData){
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
        //修改信息
        $rst = $this->save($requestData);
        if($rst===false){
            $this->error = '编辑失败';
            return false;
        }
        return true;
    }

    /**
     * 处理excel信息
     * @param $file
     * @return bool
     */
    public function excel($file){
        $excel = new \Admin\Service\ExcelService();
        $res = $excel->read($file);
        if($res){
            $error = $temp = array();
            foreach($res as $item){
                $temp = array(
                    'clinic_name'=>$item[0],
                    'clinic_phone'=>$item[1],
                    'clinic_address'=>$item[2],
                    'clinic_specialty'=>$item[3],
                    'clinic_introduction'=>$item[4],
                );
                //添加到数据库中
                if(($id=$this->add($temp))===false){
                    $temp['note'] = '保存数据失败';
                    $error[] = $temp;
                    continue;
                }
            }
            if(count($error)>0){
                //写入到错误excel报表中
                $conf = array(
                    'clinic_name'=>'诊所名',
                    'clinic_phone'=>'诊所电话',
                    'clinic_address'=>'诊所地址',
                    'clinic_specialty'=>'诊所擅长',
                    'clinic_introduction'=>'诊所简介',
                    'note'=>'失败原因',
                );
                $file = $excel->push($error,$conf);
                $this->error = '处理表格信息失败';
                return array('code'=>3,'msg'=>'处理表格信息失败','count'=>count($error),'path'=>$file);
            }else{
                return array('code'=>0);
            }
        }else{
            return array('code'=>0);
        }
    }

}