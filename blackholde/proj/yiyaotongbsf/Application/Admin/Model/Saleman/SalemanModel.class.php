<?php
/**
 * 业务员管理
 */
namespace Admin\Model\Saleman;
use Think\Model;
use Think\Page;

class SalemanModel extends Model
{
    private $size = 10;

    /**
     * 得到分页数据
     * @param array $where
     * @return array
     */
    public function pageList($where = array()){
        $where['a.status'] = array('neq',2);//正常账户
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
        $rows = $this->alias('a')->field('a.*,b.name as clinic,b.address')->order('a.id desc')->where($where)->join(' LEFT JOIN __CLINIC__ b on a.clinic_id=b.id')->limit($first, $pageTool->listRows)->select();
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
        //保存图片
        if($requestData['licence']){
            $uploader = new \Admin\Controller\Common\UploadController();
            $rst = $uploader->moveUpload($requestData['licence'],'doctor');
            if($rst===false){
//                $this->error = '保存图片失败';
                $this->error = $uploader->getError();
                return false;
            }else{
                $requestData['licence'] = $rst;
            }
        }
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
        //保存图片
        if($requestData['licence']){
            //判断是否新上传的
            if(stripos($requestData['licence'],'doctor')===false){
                //从临时目录中移到保存目录中
                $uploader = new \Admin\Controller\Common\UploadController();
                $rst = $uploader->moveUpload($requestData['licence'],'doctor');
                if($rst===false){
                    $this->error = '保存图片失败';
                    return false;
                }else{
                    //删除以前的
                    $old = $this->where(array('id'=>$requestData['id']))->getField('licence');
                    if($old) unlink('.'.$old);
                    //当前
                    $requestData['licence'] = $rst;
                }
            }
        }
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
                    'name'=>$item[0],
                    'clinic_id'=>$item[1],
                    'intro'=>$item[2],
                    'mobile'=>$item[3],
                );
                //监测电话号码是否存在
                if($this->checkMobile($temp['mobile'])==false){
                    $temp['note'] = '电话号码已存在';
                    $error[] = $temp;
                    continue;
                }
                //添加到数据库中
                if($this->add($temp)===false){
                    $temp['note'] = '保存数据失败';
                    $error[] = $temp;
                    continue;
                }
            }
            if(count($error)>0){
                //写入到错误excel报表中
                $conf = array(
                    'name'=>'医生名字',
                    'clinic_id'=>'诊所id',
                    'intro'=>'简介',
                    'mobile'=>'电话号码',
                    'note'=>'失败原因',
                );
                $file = $excel->push($error,$conf);
                $this->error = '处理表格信息失败';
                return array('code'=>3,'msg'=>'处理表格信息失败','count'=>count($error),'path'=>$file);
            }else{
                return array('code'=>0);
            }
        }
    }

    /**
     * 检测mobile的唯一性
     * @param $mobile
     * @param $type int 非0为id,0则表示添加
     * @return bool
     */
    public function checkMobile($mobile,$type=0){
        $where = array('mobile'=>$mobile);
        if($type){
            $where['id'] = array('neq',$type);
        }
        $count = $this->where($where)->count();
        return $count?false:true;
    }

    /**
     * 更新医生表相关统计
     * @param $type 1-诊断量 2-预约量 3-咨询量 4-评分
     * @param $id
     * @return bool
     */
    public function putToDoctor($type,$id){
        if(!$type || !$id) return false;
        switch ($type){
            case 1:
                $this->where(array('id'=>$id))->setInc('theat_num');//诊断量
                break;
            case 2:
                $this->where(array('id'=>$id))->setInc('appoint_num');//预约量
                break;
            case 3:
                $this->where(array('id'=>$id))->setInc('consult_num');//咨询量
                break;
            case 4:
                //评分量
                    //得到所有的对该医生的评分
                $rst = M('DoctorAssess')->where(array('doctor_id'=>$id))->field('score')->select();

                if($rst){
                    $scores = array_column($rst,'score');
                    $count = count($scores);
                    $sum = array_sum($scores);
                    $score = round($sum*2/$count ,2);
                    $this->where(array('id'=>$id))->setField('score',$score);
                }
                break;
        }
        return true;
    }

}