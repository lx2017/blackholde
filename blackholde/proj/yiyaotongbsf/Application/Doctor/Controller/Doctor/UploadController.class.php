<?php
/**
 * 上传处理
 * Created by PhpStorm.
 * User: dudongwei
 * Date: 2016/10/29
 * Time: 11:00
 */
namespace Doctor\Controller\Doctor;
use Think\Controller;
use Think\Upload;
class UploadController extends Controller
{
    public $error = '';

    /**
     * 处理图片上传: 返回值 状态码(0为成功),msg信息
     * @param int $patern 模式为1是,返回文件全信息,默认仅路径信息
     */
    public function image($patern=0){
        $this->handleUpload(1,$patern);
    }

    /**
     * 处理文件上传: 返回值 状态码(0为成功),msg信息
     * @param int $patern 模式为1是,返回文件全信息,默认仅路径信息
     */
    public function file($patern=0){
        $this->handleUpload(2,$patern);
    }

    /**
     * 处理上传
     * @param int $type 1图片,2文件
     * @param int $patern
     */
    private function handleUpload($type=1,$patern=0){
        //保存到缓存文件夹中
        if($type==1){
            $config = C('UPLOADIFY_IMG_CONF');
        }else{
            $config = C('UPLOADIFY_FILE_CONF');
        }
        //设置上传
        $upload = new Upload($config);
        $rst = $upload->uploadOne($_FILES['file']);
        if($rst!==false){
            if($patern){
                $this->ajaxReturn(array('code'=>0,'msg'=>$rst));
            }else{
                $this->ajaxReturn(array('code'=>0,'msg'=>$rst['savepath'].$rst['savename']));
            }
        }else{
            $this->ajaxReturn(array('code'=>1,'msg'=>'上传失败:'.$upload->getError()));
        }
    }

    /**
     * 移动临时文件到指定二级目录
     * @param $file string 文件名
     * @param string $second 二级目录名
     * @param int $type 1图片,2文件
     * @return bool|string false失败,文件路径
     */
    public function moveUpload($file,$second='',$type=1){
        //得到旧文件路径
        if($type==1){
            $config = C('UPLOADIFY_IMG_CONF');
        }else{
            $config = C('UPLOADIFY_FILE_CONF');
        }
        $old = $config['rootPath'].$file;
        if(is_file($old)==false){
            $this->error = '文件不存在';
            return false;
        }
        //得到新文件根路径
        $root = $config['truePath'];
        //二级目录
        if($second){
            $root .= ($second.'/');
        }
        $new = $root.date('Ymd').'/';
        //创建目录
        $rst = $this->mkdir($new);
        if(!$rst) return false;
        //移动文件
        $new = $new.$file;
        $rst = rename($old,$new);
        if($rst){
            return ltrim($new,'.');
        }else{
            $this->error = '移动文件失败';
            return false;
        }
    }

    /**
     * 创建目录
     * @param $dir
     * @return bool
     */
    private function mkdir($dir){
        if(is_dir($dir)){
            return true;
        }
        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            $this->error = "目录 {$dir} 创建失败！";
            return false;
        }
    }

    /**
     * 得到错误信息
     * @return string
     */
    public function getError(){
        return $this->error;
    }

}