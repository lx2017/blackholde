<?php
/**
 * 医生管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Controller\Doctor;

use Admin\Model\Doctor\DoctorConsultModel;
use Admin\Controller\AdminController;
class ConsultController extends AdminController{

    /**
     * 医生-咨询列表
     */
    public function index()
    {
        //添加查询条件
        $name = I('get.name');
        $where = array();
        if($name!==''){
            $where['name'] = array('like','%'.$name.'%');
        }
        //查询列表信息
        $DoctorConsultModel = new DoctorConsultModel();
        $list = $DoctorConsultModel->pageList($where);
        //记录用户当前页面
        $this->getAuth();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('list', $list['rows']);
        $this->assign('page',$list['pageHtml']);
        $this->assign('upload_conf',C('UPLOADIFY_EXCEL_CONF'));
        $this->display();
    }


    /**
     * 新增数据
     */
    public function add(){
        $DoctorConsultModel = new DoctorConsultModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $DoctorConsultModel->addData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$DoctorConsultModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'CONSULTLIST'))));
            }
        }else{
            $this->display();
        }
    }

    /**
     * 编辑数据
     */
    public function edit($id){
        $DoctorConsultModel = new DoctorConsultModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $DoctorConsultModel->saveData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$DoctorConsultModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $DoctorConsultModel->find($id);
            $this->assign($case);
            $this->display('add');
        }
    }


    /**
     * 删除数据(真删除)
     * @param $id
     * @param string $file 数据库中的文件字段名,多个逗号分隔
     */
    public function remove($id,$file = '')
    {
        $DoctorConsultModel = new DoctorConsultModel();
        $rst = $DoctorConsultModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $DoctorConsultModel->getError()));
        }
    }

    /**
     * 修改数据状态(可用于伪删除)
     * @param $id int
     * @param $value mixed 修改后的状态值
     */
    public function changeStatus($id,$value){
        $DoctorConsultModel = new DoctorConsultModel();
        $rst = $DoctorConsultModel->changeStatus($id,$value);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $DoctorConsultModel->getError()));
        }
    }
}