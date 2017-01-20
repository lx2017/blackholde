<?php
/**
 * 医生管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Model\Doctor;
use Think\Model;
use Think\Page;

class DoctorModel extends Model
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
        //查询数据
        $rows = $this->alias('a')->field('a.*,b.clinic_name as clinic,b.clinic_address as address')->order('a.id desc')->where($where)->join(' LEFT JOIN __CLINIC__ b on a.clinic_id=b.id')->limit($first, $pageTool->listRows)->select();
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
        if($value==2){//删除账户时,删除环信
            //删除环信账户
            $ids = explode(',',$id);
            foreach($ids as $item){
                if($item){
                    $info = getHxUser($item,1);
                    D('Huanxin')->deleteUser($info['username']);
                }
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
        //保存图片
        if($requestData['licence']){
            $temp = array();
            $uploader = new \Admin\Controller\Common\UploadController();
            foreach ($requestData['licence'] as $licence){
                $rst = $uploader->moveUpload($licence,'doctor');
                if($rst===false){
                    $this->error = $uploader->getError();
                    return false;
                }else{
                    $temp[] = $rst;
                }
            }
            $requestData['licence'] = implode(',',$temp);
        }
        $requestData['password'] = think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY);//默认密码
        $rst = $this->add($requestData);
        if($rst===false){
            $this->error = '添加失败';
            return false;
        }
        //添加环信账户
        $info = getHxUser($rst,1);
        D('Huanxin')->createUser($info['username'],$info['password']);
        return true;
    }

    /**
     * 编辑数据
     * @param $requestData
     * @return bool
     */
    public function saveData($requestData){
        //保存图片
        $temp = array();
        if($requestData['licence']){
            $uploader = new \Admin\Controller\Common\UploadController();
            foreach ($requestData['licence'] as $licence){
                //判断是否新上传的
                if(stripos($licence,'doctor')===false){
                    $rst = $uploader->moveUpload($licence,'doctor');
                    if($rst===false){
                        $this->error = '保存图片失败';
                        return false;
                    }else{
                        $temp[] = $rst;
                    }
                }else{
                    $temp[] = $licence;
                }
            }
            $requestData['licence'] = implode(',',$temp);
        }else{
            $requestData['licence'] = '';
        }
        //得到以前的图片信息
        $old = $this->where(array('id'=>$requestData['id']))->getField('licence');
        //修改医生信息
        $rst = $this->save($requestData);
        if($rst===false){
            $this->error = '编辑失败';
            return false;
        }
        //删除不用的图片
        if($old){
            $old = explode(',',$old);
            $res = array_diff($old,$temp);//旧的有,新的无得,删掉
            if($res) deleteFile(1,$res);
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
                $mytemp = $temp;
                $mytemp['password'] = think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY);
                if(($id=$this->add($mytemp))===false){
                    $temp['note'] = '保存数据失败';
                    $error[] = $temp;
                    continue;
                }else{
                    //创建环信账户
                    $info = getHxUser($id,1);
                    D('Huanxin')->createUser($info['username'],$info['password']);
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
        }else{
            return array('code'=>0);
        }
    }

    /**
     * 检测mobile的唯一性
     * @param $mobile
     * @param $type int 非0为id,0则表示添加
     * @return bool
     */
    public function checkMobile($mobile,$type=0){
        $where = array('mobile'=>$mobile,'status'=>array('neq',2));//非删除的医生
        if($type){
            $where['id'] = array('neq',$type);
        }
        $count = $this->where($where)->count();
        return $count?false:true;
    }

}