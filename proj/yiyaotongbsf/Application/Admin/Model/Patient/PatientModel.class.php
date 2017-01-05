<?php
namespace Admin\Model\Patient;
use Think\Model;
use Think\Page;
use Admin\Model\User\UsersModel;

class PatientModel extends Model
{
    private $size = 10;
    /**
     * 得到后台列表数据
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
        $rows = $this->order('id desc')->where($where)->order('id desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows, 'pageHtml' => $pageHtml);
    }


    /**
     * 添加数据
     * @param $requestData
     * @return bool
     */
    public function addPatient($requestData){
        //生成家庭编号
        if($requestData['family_id']){
        }else{
            $requestData['family_id'] = get_unique_number();
        }
        $requestData['password'] = think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY);//密码
        $rst = $this->add($requestData);
        if($rst===false){
            $this->error = '添加失败';
            return false;
        }
        //添加环信账户
        $info = getHxUser($rst,3);
        D('Huanxin')->createUser($info['username'],$info['password']);
        return $rst;
    }


    /**
     * 编辑数据
     * @param $requestData
     * @return bool
     */
    public function savePatient($requestData){
        //生成家庭编号
        if($requestData['family_id']==null){
            $requestData['family_id'] = get_unique_number();
        }
        $rst = $this->save($requestData);
        if($rst===false){
            $this->error= '编辑失败';
            return false;
        }
        return $rst;
    }

    /**
     * 删除数据
     * @param $id
     *  @param $file string 数据库中的文件字段名,多个逗号分隔
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
        //删除环信账户
        $ids = explode(',',$id);
        foreach($ids as $item){
            if($item){
                $info = getHxUser($item,3);
                D('Huanxin')->deleteUser($info['username']);
            }
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
                    'mobile'=>$item[1],
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
                    $info = getHxUser($id,3);
                    D('Huanxin')->createUser($info['username'],$info['password']);
                }
            }
            if(count($error)>0){
                //写入到错误excel报表中
                $conf = array(
                    'name'=>'患者姓名',
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
        $where = array('mobile'=>$mobile);
        if($type){
            $where['id'] = array('neq',$type);
        }
        $count = $this->where($where)->count();
        return $count?false:true;
    }


}