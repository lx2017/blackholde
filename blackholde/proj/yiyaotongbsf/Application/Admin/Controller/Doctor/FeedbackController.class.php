<?php
/**
 * 医生-反馈管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Controller\Doctor;

use Admin\Model\Doctor\FeedbackModel;
use Admin\Controller\AdminController;
class FeedbackController extends AdminController{

    /**
     * 反馈列表
     */
    public function index()
    {
        //添加查询条件
        $where = array();
        //查询列表信息
        $feedbackModel = new FeedbackModel();
        $list = $feedbackModel->pageList($where);
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
        $DoctorTreatmentModel = new FeedbackModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $DoctorTreatmentModel->addData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$DoctorTreatmentModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'TREATMENTLIST'))));
            }
        }else{
            $this->display();
        }
    }

    /**
     * 编辑数据
     */
    public function edit($id){
        $DoctorTreatmentModel = new FeedbackModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $DoctorTreatmentModel->saveData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$DoctorTreatmentModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $DoctorTreatmentModel->find($id);
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
        $DoctorTreatmentModel = new FeedbackModel();
        $rst = $DoctorTreatmentModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $DoctorTreatmentModel->getError()));
        }
    }

    /**
     * 修改数据状态(可用于伪删除)
     * @param $id int
     * @param $value mixed 修改后的状态值
     */
    public function changeStatus($id,$value){
        $DoctorTreatmentModel = new FeedbackModel();
        $rst = $DoctorTreatmentModel->changeStatus($id,$value);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $DoctorTreatmentModel->getError()));
        }
    }
}