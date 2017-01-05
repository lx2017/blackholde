<?php
/**
 * 活动管理
 * Created by PhpStorm.
 * User: jiaolele
 */
namespace Admin\Model\Patient;
use Think\Model;
use Think\Page;

class ActivityModel extends Model
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
        $rows = $this->order('id desc')->where($where)->order('id desc')->limit($first, $pageTool->listRows)->select();
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
    public function addDate($requestData){
        $img=$requestData['image'];
        //保存图片
        if($requestData['image']){
            $temp = array();
            $uploader = new \Admin\Controller\Common\UploadController();

            $rst = $uploader->moveUpload($img,'information');
            if($rst===false){
                $this->error = $uploader->getError();
                return false;
            }else{
                $temp= $rst;
            }

            $requestData['image'] = $temp;
        }
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
    public function saveDate($requestData){
//保存图片
        $temp = array();
        if($requestData['image']){
            $uploader = new \Admin\Controller\Common\UploadController();
            $img=$requestData['image'];

            //判断是否新上传的
            if(stripos($img,'information')===false){
                $rst = $uploader->moveUpload($img,'information');
                if($rst===false){
                    $this->error = '保存图片失败';
                    return false;
                }else{
                    $temp= $rst;
                }
            }else{
                $temp = $img;
            }

            $requestData['image'] =$temp;
        }else{
            $requestData['image'] = '';
        }
        $rst = $this->save($requestData);
        if($rst===false){
            $this->error = '编辑失败';
            return false;
        }
        return true;
    }


}