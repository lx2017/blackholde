<?php

/**
 *
 * User: 卖女孩的小火柴
 * Date: 16/11/3
 * Time: 下午2:58
 *
 */
namespace Home\Controller\WorkSummary;

use Home\Controller\BaseController;
use Home\Model\WorkSummary\ClinicModel;
use Home\Model\WorkSummary\DoctorModel;
use Home\Model\WorkSummary\SalemanapplyModel;
use Home\Model\WorkSummary\SalemanModel;
use Home\Model\WorkSummary\SalemansignModel;
use Home\Model\WorkSummary\SalemanorderModel;

class SaleSummaryController extends BaseController
{
    private $doctorModel;
    private $clinicModel;
    private $salemanModel;
    private $salemanSignModel;
    private $salemanApplyModel;
    private $sale_id;
    public function _initialize()
    {
        parent::_initialize();
        $this->doctorModel = new DoctorModel();
        $this->clinicModel = new ClinicModel();
        $this->salemanSignModel = new SalemansignModel();
        $this->salemanApplyModel = new SalemanapplyModel();
        $this->salemanOrderModel = new SalemanorderModel();
        $this->salemanModel = new SalemanModel();
    }
    public function getYear()
    {

    }
    /*
     *
     * 业务员工作汇总
     *
     * */
    public function index()
    {
        $sale_id = I('get.saleId');
        $this->sale_id = $sale_id = think_ucenter_decrypt($sale_id);
        $data=$this->clinicModel->getYear($sale_id);
        
        $my_data=(explode("?",$_SERVER['REQUEST_URI']));
        
        $this->assign('year_data',$data);
                //$this->sale_id = 217;///////////////////////////////////测试用
        //$this->sale_id = 223;///////////////////////////////////测试用 县总
        //$this->sale_id = 223;///////////////////////////////////测试用 县总
        //$this->sale_id = 279;///////////////////////////////////测试用 地总
        list($a,$year) = explode("=",$my_data[1]);

        if( !$year )
        {
            $year = 2016;
        }
        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($this->sale_id);

        if( $role == 5 )
        {
            $clinic_ids = $this->clinicModel->getClinicidsBySalemanID($this->sale_id,$year);//当年普通诊所数量,返回的是id
            $doctorNum = $this->doctorModel->getCount($clinic_ids);//医生数量
            $clinicNum = $this->clinicModel->getCount($this->sale_id,0,$year);//诊所数量
            $clinicSumNum = $this->clinicModel->getCount($this->sale_id,1,$year);//目标诊所数量
            $salmanVisitNum = $this->salemanSignModel->getVisitCountBySaleId($this->sale_id,$year);//拜访次数
            $salemanApplyNum = $this->salemanApplyModel->getApplyCountBySaleId($this->sale_id,$year);
            $salemanOrderNum = $this->salemanOrderModel->getClinicPriceBySaleId($this->sale_id,$year);//销售是  诊所订的货就是业务员的销售
            $salemanPrice = $this->salemanOrderModel->getSalePriceBySaleId($this->sale_id,$year);//订货数量 是针对业务员自己的
            $quitePrice = $this->salemanOrderModel->getQuiteSalePriceBySaleId($this->sale_id,$year);

            $this->assign('doctorNum',$doctorNum);
            $this->assign('clinicNum',$clinicNum);
            $this->assign('clinicSumNum',$clinicSumNum);
            $this->assign('salmanVisitNum',$salmanVisitNum);
            $this->assign('quitePrice',$quitePrice);
            $this->assign('salemanApplyNum',$salemanApplyNum);
            $this->assign('salemanOrderNum',$salemanOrderNum);
            $this->assign('salemanPrice',$salemanPrice);
        }
        if( $role == 4 )//县总
        {

            $salemanNum = $this->salemanModel->getSalemanCountByXianZong($this->sale_id,$year);//业务是数量
            $purposeClinicNum = $this->salemanModel->getPurposeClinicCountByXianZong($this->sale_id,$year);//目标诊所数量
            $clinicNum = $this->salemanModel->getClinicCountByXianZong($this->sale_id,$year);
            $doctorNum = $this->doctorModel->getDoctorCountByXianZong($this->sale_id,$year);
            $salmanVisitNum = $this->salemanSignModel->getVisitCountByXianZong($this->sale_id,$year);//拜访次数
            $salemanApplyNum = $this->salemanApplyModel->getApplyCountByXianZong($this->sale_id,$year);
            $salemanOrderNum = $this->salemanOrderModel->getClinicPriceByXianZong($this->sale_id,$year);//销售
            $quitePrice = $this->salemanOrderModel->getQuitePriceByXianZong($this->sale_id,$year);
            $salemanPrice = $this->salemanOrderModel->getSalePriceByXianZong($this->sale_id,$year);

            $this->assign('salemanNum',$salemanNum);//业务员数量
            $this->assign('clinicNum',$clinicNum);//诊所
            $this->assign('clinicSumNum',$purposeClinicNum);//目标诊所
            $this->assign('salmanVisitNum',$salmanVisitNum);
            $this->assign('salemanApplyNum',$salemanApplyNum);
            $this->assign('doctorNum',$doctorNum);
            $this->assign('salemanOrderNum',$salemanOrderNum);
            $this->assign('quitePrice',$quitePrice);
            $this->assign('salemanPrice',$salemanPrice);

        }

        if( $role == 3 )//地总
        {
            $countryCount = $this->salemanModel->getCountryCountByDiZong($this->sale_id,$year);
            $salemanNum = $this->salemanModel->getSalemanCountByDiZong($this->sale_id,$year);
            $purposeClinicNum = $this->salemanModel->getPurposeClinicCountByDiZong($this->sale_id,$year);//目标诊所数量
            $clinicNum = $this->salemanModel->getClinicCountByDiZong($this->sale_id,$year);
            $doctorNum = $this->doctorModel->getDoctorCountByDiZong($this->sale_id,$year);
            $salmanVisitNum = $this->salemanSignModel->getVisitCountByDiZong($this->sale_id,$year);//拜访次数
            $salemanApplyNum = $this->salemanApplyModel->getApplyCountByDiZong($this->sale_id,$year);
            $salemanOrderNum = $this->salemanOrderModel->getClinicPriceByDiZong($this->sale_id,$year);//销售
            $quitePrice = $this->salemanOrderModel->getQuitePriceByDiZong($this->sale_id,$year);
            $salemanPrice = $this->salemanOrderModel->getSalePriceByDiZong($this->sale_id,$year);

            $this->assign('countryCount',$countryCount);
            $this->assign('salemanNum',$salemanNum);
            $this->assign('clinicNum',$clinicNum);//诊所
            $this->assign('clinicSumNum',$purposeClinicNum);//目标诊所
            $this->assign('doctorNum',$doctorNum);
            $this->assign('salmanVisitNum',$salmanVisitNum);
            $this->assign('salemanApplyNum',$salemanApplyNum);
            $this->assign('salemanOrderNum',$salemanOrderNum);
            $this->assign('quitePrice',$quitePrice);
            $this->assign('salemanPrice',$salemanPrice);

        }

        if( $role == 2 )//省总
        {
            //地总数量
            $areaCount = $this->salemanModel->getChildCountBySaleId($this->sale_id,$year);
            $this->assign('areaCount',$areaCount);
        }

        $this->assign('role',(int)$role);

        $this->assign('sale_id',$this->sale_id);
      
        $this->assign('year',$year);
        $this->display();
    }

    /*
     *
     * 县总业务员列表
     *
     * */
    public function salelist()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');
        $role = $this->salemanModel->getRoleBySaleId($sale_id);
        if( $role == 4)//县总
        {
            $salelists = $this->salemanModel->getSalemanListsBySaleId($sale_id,$year);
            //var_dump( $salelists );die;
        }
        if( $role == 3 )//地总
        {
            $salemanNum = $this->salemanModel->getSalemanCountByDiZong($sale_id,$year);
            $salelists = $this->salemanModel->getSalemanByDiZong($sale_id,$year);
            $count = $salemanNum;
        }

        if( $role == 2 )
        {

        }
        $this->assign('salelists',$salelists);
        $this->assign('count',$count);
        $this->display();
    }

    /*
     *
     * 地总中县总列表
     *
     * */
    public function countrylist()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');
        $countrylists = $this->salemanModel->getXianZongByDiZong($sale_id,$year);
        $this->assign('countrylists',$countrylists);
        $this->assign('count',count($countrylists));
        $this->display();
    }

    /*
     *
     * 省总中的地总列表
     *
     * */
    public function arealist()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');
        //地总数量
        $areaCount = $this->salemanModel->getChildCountBySaleId($sale_id,$year);
        $this->assign('areaCount',$areaCount);
        $this->display();
    }

    /*
     * 
     * 目标诊所
     * 
     * */
    public function clinicsum()
    {
        $year = I('get.year');
        $sale_id = I('get.sale_id');

        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);
        //业务员
        if( $role == 5 )
        {
            $clinicSumNum = $this->clinicModel->getCount($sale_id,1,$year);
            $clinics = $this->clinicModel->getClinicByMonth($sale_id,$year);
            $clinic_str = '['.implode(',',$clinics).']';
        }
        //县总
        if( $role == 4 )
        {
            $purposeClinicNum = $this->salemanModel->getPurposeClinicCountByXianZong($sale_id,$year);//目标诊所数量
            $clinics = $this->clinicModel->getClinicByXianZong($sale_id,$year);
            $clinic_str = '['.implode(',',$clinics).']';
            $clinicSumNum = $purposeClinicNum;
        }
        //地总
        if( $role == 3 )
        {
            $purposeClinicNum = $this->salemanModel->getPurposeClinicCountByDiZong($sale_id,$year);//目标诊所数量
            $clinics = $this->clinicModel->getClinicByDiZong($sale_id,$year);
            //var_dump($clinics);die;
            $clinic_str = '['.implode(',',$clinics).']';
            $clinicSumNum = $purposeClinicNum;
        }

        $this->assign('clinicSumNum',$clinicSumNum);
        $this->assign('clinic_str',$clinic_str);
        $this->display();
    }
    
    
    /*
     *
     * 业务员下诊所详情
     *
     * */
    public function allclinic()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');

        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )//业务员
        {
            $clinics = $this->clinicModel->getClinicsBySalemanID($sale_id,$year);
        }

        if( $role == 4 )//县总下的诊所
        {
            $clinics = $this->clinicModel->getClinicsByXianZong($sale_id,$year);
        }

        if( $role == 3) //地总下的诊所
        {
            $clinics = $this->clinicModel->getClinicsByDiZong($sale_id,$year);

        }
        $this->assign('clinics',$clinics);
        $this->display();
    }

    /*
     *
     * 业务员下的诊所中医生
     *
     * */
    public function alldoctor()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');

        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )//业务员下的医生
        {
            $doctors = $this->getAllDoctorsBySalemanID($sale_id,$year);
        }

        if( $role == 4 )//县总下的医生
        {
            $doctors = $this->doctorModel->getAllDoctorsByXianZong($sale_id,$year);
        }

        if( $role == 3 )//地总下的医生
        {
            $doctors = $this->doctorModel->getAllDoctorsByDiZong($sale_id,$year);
        }
        $this->assign('doctors',$doctors);
        $this->display();
    }
    
    
    /*
     * 
     * 拜访次数
     * 
     * */
    public function visitalltimes()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');

        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )//业务员
        {
            $salmanVisitNum = $this->salemanSignModel->getVisitCountBySaleId($sale_id,$year);
            $salmanVisitMonth = $this->salemanSignModel->getVisitDetailBySaleId($sale_id,$year);
        }

        if( $role == 4 )//县总
        {

        }

        if( $role == 3)//地总
        {
            $salmanVisitNum = $this->salemanSignModel->getVisitCountByDiZong($sale_id,$year);
            $salmanVisitMonth = $this->salemanSignModel->getVisitDetailByDiZong($sale_id,$year);

        }
        $this->assign('salmanVisitNum',$salmanVisitNum);
        $this->assign('salmanVisitMonth',$salmanVisitMonth);
        $this->assign('year',$year);
        $this->assign('sale_id',$sale_id);
        $this->display();
    }

    /*
     *
     * 拜访详情
     *
     * */
    public function visitdetail()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');
        $month = I('get.month');
        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )
        {
            $data = $this->salemanSignModel->getMonthVisitDetailBySaleId($sale_id,$year,$month);
        }

        if( $role == 3 )
        {
            $data = $this->salemanSignModel->getMonthVisitDetailByDiZong($sale_id,$year,$month);
        }
        $this->assign('year',$year);
        $this->assign('month',$month);
        $this->assign('data',$data);
        $this->assign('sale_id',$sale_id);
        $this->display();
    }
    

    /*
     *
     * 申请会议
     *
     * */
    public function applicatemeet()
    {
        $sale_id = I('get.sale_id');
        $type = I('get.type');
        $year = I('get.year');
        if( !$type )
        {
            $type = 1;
        }
        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )
        {
            $lists = $this->salemanApplyModel->getApplyDetailBySaleId($sale_id,$type,$year);
            $content = array(1=>'圆桌会议',2=>'普通会议',3=>'讲坛会议',4=>'培训会议',5=>'群英会议');
            $status = array('待回复','同意','拒绝');
            foreach( $lists as &$list )
            {
                $list['reply_status'] = $status[$list['reply_status']];
                $list['type'] = $content[$type];
            }
        }

        if( $role == 4 )
        {
            $lists = $this->salemanApplyModel->getApplyDetailByXianZong($sale_id,$type,$year);
            $content = array(1=>'圆桌会议',2=>'普通会议',3=>'讲坛会议',4=>'培训会议',5=>'群英会议');
            $status = array('待回复','同意','拒绝');
            foreach( $lists as &$list )
            {
                $list['reply_status'] = $status[$list['reply_status']];
                $list['type'] = $content[$type];
            }
        }


        if( $role == 3 )
        {
            $lists = $this->salemanApplyModel->getApplyDetailByDiZong($sale_id,$type,$year);
            $content = array(1=>'圆桌会议',2=>'普通会议',3=>'讲坛会议',4=>'培训会议',5=>'群英会议');
            $status = array('待回复','同意','拒绝');
            foreach( $lists as &$list )
            {
                $list['reply_status'] = $status[$list['reply_status']];
                $list['type'] = $content[$type];
            }
        }
        $this->assign( 'lists', $lists);
        $this->assign( 'sale_id', $sale_id);
        $this->assign( 'type', $type);
        $this->display();
    }

    /*
     *
     * 销售统计
     *
     * */
    public function salesumary()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');
        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )
        {
            $orders = $this->salemanOrderModel->getClinicListBySaleId($sale_id,$year);
        }

        if( $role == 4 )
        {
            $orders = $this->salemanOrderModel->getClinicListByXianZong($sale_id,$year);
        }

        if( $role == 3 )
        {
            $orders = $this->salemanOrderModel->getClinicListByDiZong($sale_id,$year);
        }

        $this->assign('orders',$orders);
        $this->assign('year',$year);
        $this->assign('sale_id',$sale_id);
        $this->display();
    }

    /*
     *
     * 根据诊所id来确定每单的详情
     *
     * */
    public function orderdetail()
    {
        $year = I('get.year');
        $sale_id = I('get.sale_id');
        $clinic_id = I('get.clinic_id');
        $order_id = I('get.order_id');
        $orders = $this->salemanOrderModel->getOrdersListsBySendId($clinic_id,$year);
        $this->assign('orders',$orders);
        $this->display();
    }

    /*
     *
     * 订货统计
     *
     * */
    public function orderlist()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');
        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )
        {
            $orders = $this->salemanOrderModel->getSalemanOrderListBySaleId($sale_id,$year);
        }

        if( $role == 4 )
        {
            $orders = $this->salemanOrderModel->getSalemanOrderListByXianZong($sale_id,$year);
        }

        if( $role == 3 )
        {
            $orders = $this->salemanOrderModel->getSalemanOrderListByDiZong($sale_id,$year);
        }

        $num = $orders['num'] ? $orders['num'] : 0;
        $price = $orders['price'] ? $orders['price'] : 0;
        unset($orders['num']);
        unset($orders['price']);
        $this->assign('orders',$orders);
        $this->assign('num',$num);
        $this->assign('price',$price);
        $this->assign('year',$year);
        $this->assign('sale_id',$sale_id);
        $this->display();
    }

    /*
     *
     * 退货统计
     *
     * */
    public function quitsale()
    {
        $sale_id = I('get.sale_id');
        $year = I('get.year');
        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )
        {
            $orders = $this->salemanOrderModel->getQuiteSaleClinicListBySaleId($sale_id,$year);
        }

        if( $role == 4 )
        {
            $orders = $this->salemanOrderModel->getQuiteSaleClinicListByXianZong($sale_id,$year);
        }

        if( $role == 3 )
        {
            $orders = $this->salemanOrderModel->getQuiteSaleClinicListByDiZong($sale_id,$year);
        }
        $this->assign('orders',$orders);
        $this->assign('year',$year);
        $this->assign('sale_id',$sale_id);
        $this->display();
    }

    /*
     * 
     * 退货统计详情
     * 
     * */
    public function quitsaledetail()
    {
        $year = I('get.year');
        $sale_id = I('get.sale_id');
        $clinic_id = I('get.clinic_id');
        $orders = $this->salemanOrderModel->getQuiteClinicListsByClinicId($clinic_id,$year);
        $this->assign('orders',$orders);
        $this->display();
    }
    /*
     *
     * 业务员下诊所地区显示
     *
     * */
    public function salemap()
    {
        $year = I('get.year');
        $sale_id = I('get.sale_id');
        //获取角色  角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        $role = $this->salemanModel->getRoleBySaleId($sale_id);

        if( $role == 5 )
        {
            $positions = $this->clinicModel->getPositionBySalemanID($sale_id,$year);
        }

        if( $role == 4 )
        {
            $positions = $this->clinicModel->getPositionByXianZong($sale_id,$year);
        }
        $positions = json_encode($positions);
        $this->assign('positions',$positions);
        $this->display();
    }

    /*
     *
     * 获取所有医生
     *
     * */
    public function getAllDoctorsBySalemanID($sale_id,$year)
    {
        $clinic_ids = $this->clinicModel->getClinicidsBySalemanID($sale_id,$year);
        if( !$clinic_ids )
        {
            return false;
        }
        $doctors = $this->doctorModel->getAllDoctorsByIds($clinic_ids);
        foreach( $doctors as &$doctor )
        {
            $doctor['clinic_name'] = $this->clinicModel->getClinicNameById($doctor['clinic_id']);
        }
        return $doctors;
    }


    /*
     *
     *
     * 删除医生
     *
     * */
    public function removedDoctors()
    {
        $doctors = $this->doctorModel->getRemovedDoctors();
        var_dump($doctors);
    }
}