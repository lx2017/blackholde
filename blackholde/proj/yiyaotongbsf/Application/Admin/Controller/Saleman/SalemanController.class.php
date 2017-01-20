<?php

/**
 * 业务员管理
 */

namespace Admin\Controller\Saleman;

use Admin\Common\SalemanHelper;
use Think\Upload;
use Admin\Controller\Saleman\BaseController;

class SalemanController extends BaseController {

    /**
     * 列表
     */
    public function lists() {
        //查询列表信息
        $salemanModel = M('saleman');

        $where = array(
            'role_id' => array('eq', 5),
            'status' => array('in', array(0, 1))
        );
        //得到分页信息
        $count = $salemanModel->where($where)->count();
        $pageTool = new \Think\Page($count, 10); //分页
        //设置分页信息
        $page_config = C('PAGE_CONFIG');
        foreach ($page_config as $k => $v) {
            $pageTool->setConfig($k, $v);
        }
        $pageHtml = $pageTool->show();
        //查询数据
        $order = array(
            'id' => 'desc'
        );
        $list = $salemanModel->where($where)->order($order)->limit($pageTool->firstRow, $pageTool->listRows)->select();

        $this->assign(array(
            'page' => $pageHtml,
            'list' => $list,
            '_total' => $count,
        ));
        $this->display();
    }

    /**
     * 新增数据
     */
    public function add() {

        if (IS_POST) {

            $phone = $_POST['phone'];
            $real_name = $_POST['real_name'];
            $card_number = $_POST['card_number'];
            $sex = $_POST['sex'];
            $age = $_POST['age'];
            $superior_id = $_POST['superior_id'];
            $manage_locations = $_POST['manage_locations'];
            $role_id = $_POST['role_id'];
            $work_event = $_POST['work_event'];
            $status = $_POST['status'];
            if (empty($phone) || !preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $phone)) {
                responseJson(0, '手机号码无效');
                exit;
            }
            if (empty($real_name)) {
                responseJson(0, '真实姓名无效');
                exit;
            }
            if (empty($card_number)) {
                responseJson(0, '身份证无效');
                exit;
            }
            if (!isset($sex) || !in_array($sex, array(1, 2))) {
                responseJson(0, '性别无效');
                exit;
            }
            if (!is_numeric($age) || intval($age) <= 0) {
                responseJson(0, '年龄无效');
                exit;
            }
            if (empty($superior_id)) {
                responseJson(0, '上级县总无效');
                exit;
            }
            if (empty($manage_locations)) {
                responseJson(0, '所属县无效');
                exit;
            }
            if (empty($role_id)) {
                $role_id = 5;
            }
            if (empty($work_event)) {
                responseJson(0, '工作事项无效');
                exit;
            }
            if (!isset($status) || !in_array($status, array(0, 1))) {
                $status = 0;
            }

            //查询手机号码是否已存在
            $salemanModel = M('saleman');
            $where = array(
                'phone' => array('eq', $phone),
                'status' => array('neq', 2)
            );
            $saleman = $salemanModel->where($where)->find();
            if (check_array($saleman)) {
                responseJson(0, '手机号码已被已存在');
                exit;
            }

            //查询上级是否存在
            $where = array(
                'id' => array('eq', $superior_id),
                'status' => array('neq', 2)
            );
            $superior = $salemanModel->where($where)->find();
            if (!check_array($superior)) {
                responseJson(0, '上级不存在');
                exit;
            }

            //组装密码
            $pwd = think_ucenter_md5(C('SALE_MANAGER.INIT_PASSWORD'), UC_AUTH_KEY);

            $data = array(
                'phone' => $phone,
                'password' => $pwd,
                'real_name' => $real_name,
                'card_number' => $card_number,
                'sex' => $sex,
                'age' => $age,
                'superior_id' => $superior_id,
                'manage_locations' => $manage_locations,
                'role_id' => $role_id,
                'add_time' => time(),
                'work_event' => $work_event,
                'status' => $status
            );
            $re = SalemanHelper::upload(); //附件上传
            if ($re) {
                foreach ($re as $key => $url) {
                    $data[$key] = $url;
                }
            }
            $excNum = $salemanModel->data($data)->add();
            if ($excNum <= 0) {
                responseJson(0, '业务员添加失败');
                exit;
            }

            responseJson(1, '业务员添加成功');
            exit;
        }

        //查询业务员的上级
        $salemanModel = M('saleman');
        $where = array(
            'role_id' => array('eq', 4)
        );
        $supers = $salemanModel->where($where)->select();

        $this->assign(
                array(
                    'supers' => $supers
                )
        );
        $this->display();
    }

    /**
     * 编辑数据
     */
    public function edit() {
        if (IS_POST) {
            $id = $_POST['id'];
            $phone = $_POST['phone'];
            $real_name = $_POST['real_name'];
            $card_number = $_POST['card_number'];
            $sex = $_POST['sex'];
            $age = $_POST['age'];
            $superior_id = $_POST['superior_id'];
            $manage_locations = $_POST['manage_locations'];
            $role_id = $_POST['role_id'];
            $work_event = $_POST['work_event'];
            $status = $_POST['status'];
            if (empty($phone) || !preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $phone)) {
                responseJson(0, '手机号码无效');
                exit;
            }
            if (empty($real_name)) {
                responseJson(0, '真实姓名无效');
                exit;
            }
            if (empty($card_number)) {
                responseJson(0, '身份证无效');
                exit;
            }
            if (!isset($sex) || !in_array($sex, array(1, 2))) {
                responseJson(0, '性别无效');
                exit;
            }
            if (!is_numeric($age) || intval($age) <= 0) {
                responseJson(0, '年龄无效');
                exit;
            }
            if (empty($superior_id)) {
                responseJson(0, '上级县总无效');
                exit;
            }
            if (empty($manage_locations)) {
                responseJson(0, '所属县无效');
                exit;
            }
            if (empty($role_id)) {
                $role_id = 5;
            }
            if (empty($work_event)) {
                responseJson(0, '工作事项无效');
                exit;
            }
            if (!isset($status) || !in_array($status, array(0, 1))) {
                $status = 0;
            }

            //查询手机号码是否已存在
            $salemanModel = M('saleman');
            $where = array(
                'id' => array('neq', $id),
                'phone' => array('eq', $phone),
                'status' => array('neq', 2)
            );
            $saleman = $salemanModel->where($where)->find();
            if (check_array($saleman)) {
                responseJson(0, '手机号码已被已存在');
                exit;
            }

            //查询上级是否存在
            $where = array(
                'id' => array('eq', $superior_id),
                'status' => array('neq', 2)
            );
            $superior = $salemanModel->where($where)->find();
            if (!check_array($superior)) {
                responseJson(0, '上级不存在');
                exit;
            }

            $data = array(
                'phone' => $phone,
                'real_name' => $real_name,
                'card_number' => $card_number,
                'sex' => $sex,
                'age' => $age,
                'superior_id' => $superior_id,
                'manage_locations' => $manage_locations,
                'role_id' => $role_id,
                'work_event' => $work_event,
                'status' => $status
            );
            $re = SalemanHelper::upload(); //附件上传
            if ($re) {
                foreach ($re as $key => $url) {
                    $data[$key] = $url;
                }
            }
            $where = array('id' => array('eq', $id));
            $excNum = $salemanModel->data($data)->where($where)->save();
            if ($excNum <= 0) {
                responseJson(0, '业务员修改失败');
                exit;
            }

            responseJson(1, '业务员修改成功');
            exit;
        }

        //查询业务员
        $salemanModel = M('saleman');
        $id = $_GET['id'];
        $where = array(
            'id' => array('eq', $id)
        );
        $saleman = $salemanModel->where($where)->find();

        //查询业务员的上级
        $where = array(
            'role_id' => array('eq', 4)
        );
        $supers = $salemanModel->where($where)->select();

        $this->assign(
                array(
                    'sale' => $saleman,
                    'supers' => $supers
                )
        );
        $this->display();
    }

    /**
     * 删除业务员
     */
    public function del() {
        if (IS_AJAX) {
            $id = $_GET['id'];
            if (empty($id)) {
                responseJson(0, '缺少参数');
                exit;
            }

            //逻辑删除
            $data = array(
                'status' => 2
            );
            $salemanModel = M('saleman');
            $where = array(
                'id' => array('eq', $id)
            );
            $excNum = $salemanModel->where($where)->data($data)->save();
            if ($excNum <= 0) {
                responseJson(0, '业务员删除失败');
                exit;
            }

            responseJson(1, '业务员删除成功');
            exit;
        }
    }

    public function up() {

        if (IS_POST) {
            $REQUEST = (array) I('request.');
            $warningList = array();
            if (!isset($_FILES ["excel"]) || $_FILES ["excel"] ["error"] != 0) {
                responseJson(0, '导入表格失败', $warningList);
                exit;
            }

            $setting = C('UPLOADIFY_EXCEL_CONF');
            /* 调用文件上传组件上传文件 */
            $uploader = new Upload($setting, 'Local');
            $info = $uploader->upload($_FILES);
            if (!$info) {
                responseJson(0, '导入表格无效', $warningList);
                exit;
            }


            vendor("PHPExcel.PHPExcel");
            $file_name = $setting ['rootPath'] . $info ['excel'] ['savepath'] . $info ['excel'] ['savename'];
            if (!file_exists($file_name)) {
                die('no file!');
            }
            $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if ($extension == 'xlsx') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            } else if ($extension == 'xls') {
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            } else if ($extension == 'csv') {
                $objReader = \PHPExcel_IOFactory::createReader('CSV');
            }
            $obj = $objReader->load($file_name, $encode = 'utf-8');
            $currentSheet = $obj->getSheet(0); // 读取excel文件中的第一个工作表
            $excelList = $currentSheet->toArray();
            if (!check_array($excelList) || count($excelList) <= 1) {
                responseJson(0, '导入表格数据无效', $warningList);
                exit;
            }

            unset($excelList[0]);
            $salemanPhoneList = array(); //业务员手机号集合
            $superiorPhoneList = array(); //业务员上级手机号集合
            $data = array();
            foreach ($excelList as $key => $value) {
                //验证手机
                $phone = $value[0];
                if (empty($phone) || !preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $phone)) {
                    $warningList[$key]['msg'].="手机号无效、";
                } else {
                    if (!in_array($phone, $salemanPhoneList)) {
                        $salemanPhoneList[] = $phone;
                    }
                    $data[$key]['phone'] = $phone;
                }

                //验证姓名
                $real_name = $value[1];
                if (empty($real_name)) {
                    $warningList[$key]['msg'].="真实姓名无效、";
                } else {
                    $data[$key]['real_name'] = $real_name;
                }

                //验证身份证
                $card_number = $value[2];
                if (empty($card_number)) {
                    $warningList[$key]['msg'].="身份证无效、";
                } else {
                    $data[$key]['card_number'] = $card_number;
                }

                //验证性别
                $sex = $value[3];
                if (empty($sex) || !in_array($sex, array('男', '女'))) {
                    $warningList[$key]['msg'].="性别只能填男或女、";
                } else {
                    $data[$key]['sex'] = $sex == '男' ? 1 : 2;
                }

                //验证年龄
                $age = $value[4];
                if (empty($age) || intval($age) <= 0) {
                    $warningList[$key]['msg'].="年龄无效、";
                } else {
                    $data[$key]['age'] = $age;
                }

                //验证所属地区【所属地区没有强制要求】
                $data[$key]['manage_locations'] = $value[5];

                //验证工作事项【工作事项没有强制要求】
                $data[$key]['work_event'] = $value[6];

                //验证上级手机号码
                $superiorPhone = $value[7];
                if (empty($superiorPhone) || !preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $superiorPhone)) {
                    $warningList[$key]['msg'].="上级手机号无效、";
                } else {
                    if (!in_array($superiorPhone, $superiorPhoneList)) {
                        $superiorPhoneList[] = $superiorPhone;
                    }
                    $data[$key]['superior_id'] = $superiorPhone;
                }

                //验证用户是否有效
                $status = $value[8];
                if (empty($status) || !in_array($status, array('是', '否'))) {
                    $warningList[$key]['msg'].="业务员状态只能填是或者否、";
                } else {
                    $data[$key]['status'] = $status == '是' ? 0 : 1;
                }

                //添加必要数据
                $data['password'] = think_ucenter_md5(C('SALE_MANAGER.INIT_PASSWORD'), UC_AUTH_KEY);
                $data['role_id'] = 5; //业务员角色
                $data['add_time'] = time();
            }

            if (check_array($warningList)) {
                responseJson(0, 'excel数据错误', $warningList);
                exit;
            }

            $salemanModel = M('saleman');
            //查询业务员手机号
            $where = array(
                'phone' => array('in', $salemanPhoneList),
                'status' => array('neq', 2)
            );
            $salemanList = $salemanModel->where($where)->field(array('id', 'phone'))->select();
            if (check_array($salemanList)) {
                $phoneStr = implode('##', array_keys(set_array_key($salemanList, 'phone')));
                $warningList[0]['msg'] = $phoneStr;
                responseJson(0, '业务员手机号在系统中已存在', $warningList);
                exit;
            }

            //查询上级用户集合
            $where = array(
                'phone' => array('in', $superiorPhoneList),
                'status' => array('neq', 2),
                'role_id' => array('eq', 4)
            );
            $superiorSalemanList = $salemanModel->where($where)->field(array('id', 'phone'))->select();
            $superiorSalemanList = set_array_key($superiorSalemanList, 'phone');
            if (count($superiorSalemanList) != count($superiorPhoneList)) {
                $superiorSalemanPhoneList = array_keys($superiorSalemanList);
                $diffPhoneList = array_diff($superiorSalemanPhoneList, $superiorPhoneList);

                $phoneStr = implode('##', $diffPhoneList);
                $warningList[0]['msg'] = "以下上级手机号码不存在或不是县总：" . $phoneStr;
                responseJson(0, '上级手机号码有问题', $warningList);
                exit;
            }

            //开启事务
            $salemanModel->startTrans();

            //循环添加上级
//            $data[$key]['superior_id'] = $superiorPhone;
            foreach ($data as $key => $value) {
                $data[$key]['superior_id'] = $superiorSalemanList[$value['superior_id']]['phone'];
                $excNum = $salemanModel->data($data)->add();
                if ($excNum <= 0) {
                    $salemanModel->rollback();
                    $warningList[$key]['msg'] = "添加失败，所有数据回滚到未添加状态";
                    responseJson(0, '上级手机号码有问题', $warningList);
                    exit;
                }
            }

            //提交事务
            $salemanModel->commit();

            //返回
            responseJson(1, '操作成功');
            exit;
        }
        
        $this->display();
    }

}
