<?php
/**
 * 轻应用-招商表单
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: JoyyouthInvest.php 2016-04-23T13:22:40+0800
 */
namespace Lapp\Controller;

use Think\Controller;

class JoyyouthInvest extends Controller
{

    //状态变量
    private $_status;

    /**
     * 初始化
     * @author   lixiaoxian
     * @datetime 2016-04-19T09:42:50+0800
     */
    public function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
        return $this->_status = D('Lapp')->checkApp('JoyyouthInvest');
    }

    /**
     * 默认模板
     * @author   lixiaoxian
     * @datetime 2016-04-18T16:14:49+0800
     */
    public function index()
    {
        $code = I('get.code', '', 'trim');
        $pid  = 3;
        Vendor('phpRPC.phprpc_client');
        $client = new \PHPRPC_Client('http://10.161.228.164/wechat/api/service/webgrant');
        if (!session('userinfo.uid')) {
            //tp3.2
            $data = array(
                'app'     => 'jxb',
                'code'    => $code,
                'domain'  => 'h5.ijovo.com',
                'openid'  => '',
                'unionid' => '',
            );
            $res = $client->user($data);
            //判断错误
            if (is_object($res)) {
                exit('error');
            }
            if (!$code) {
                /**
                 * :r 当前页面地址 ,全部URL
                 * :app 公众号简写 [域名不一致时需要填写]
                 * @var string
                 */
                $uri = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $url = str_replace(':r', urlencode($uri), $res['content']['url']);
                $url = str_replace(':app', 'jxb', $url);
                redirect($url);
            }
            session('userinfo', $res['content']);
            $url1  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $jssdk = $this->_sdk('jxb', $url1);
            $this->assign('jssdk', $jssdk);
            $this->assign('wxinfo', $res['content']);
            $this->assign('pid', $pid);
            $this->display();
        } else {
            $url1  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $jssdk = $this->_sdk('jxb', $url1);
            $this->assign('jssdk', $jssdk);
            $this->assign('wxinfo', session('userinfo'));
            $this->assign('pid', $pid);
            $this->display();
        }
    }

    /**
     * 表单页面
     * @author   lixiaoxian
     * @datetime 2016-04-19T18:22:42+0800
     */
    public function form()
    {
        if ($this->_status) {
            $uid    = I('get.uid', '', 'trim');
            $appid  = 3;
            $formid = 1;

            $url1  = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $jssdk = $this->_sdk('jxb', $url1);
            $this->assign('jssdk', $jssdk);

            if ($appid && !empty($uid)) {
                $data = D('FormData')->checkData($appid, $uid, $formid);
                foreach ($data as $k => $v) {
                    $list[$v['name']] = $v;
                }
                // 模板赋值
                $this->assign('list', $list);
            } else {
                $this->assign('list', '');
            }
            $this->display();
        } else {
            // $this->display('error');
        }
    }

    /**
     * 表单数据
     * @author   lixiaoxian
     * @datetime 2016-04-18T16:15:02+0800
     * @return   [type]                   [description]
     */
    public function formData()
    {
        if ($this->_status) {
            if (IS_POST) {
                $info  = I('post.info');
                $uid   = I('get.uid', '', 'trim');
                $appid = I('get.pid', 0, 'intval');

                $formid = 1;
                $forms  = D('Form')->getFormList($appid, $formid);
                if ($forms['status'] == 1) {
                    if ($info['radio1'] == 1) {
                        $info['cname'] = '个人';
                        $this->_check($info['cname'], '企业名称', 'cname', 'nameReg');
                    } else {
                        $this->_check($info['cname'], '企业名称', 'cname', 'nameReg');
                    }
                    $this->_check($info['phone'], '手机号', 'phone', 'phoneReg');
                    $this->_check($info['name'], '申请人姓名', 'name', 'nameReg');
                    $this->_check($info['email'], '邮箱', 'email', 'emailReg');
                    $this->_check($info['qq'], 'QQ', 'qq', 'qqReg');
                    if ($info['sina'] != '') {
                        $this->_check($info['sina'], '新浪微博', 'sina', 'nameReg');
                    }
                    $this->_check($info['wechat'], '微信号', 'wechat', 'weChatReg');
                    $this->_check($info['radio1'], '企业类型', 'radio1', 'numReg');
                    $this->_check($info['radio2'], '纳税人资格', 'radio2', 'numReg');
                    $this->_check($info['address'], '办公室地址', 'address', 'nameReg');
                    $this->_check($info['tel'], '办公室电话', 'tel', 'telReg');
                    $this->_check($info['num1'], '直接餐饮终端', 'num1', 'numReg');
                    $this->_check($info['num2'], '中小型零售终端', 'num2', 'numReg');
                    $this->_check($info['num3'], '直供商超系统', 'num3', 'numReg');
                    $this->_check($info['num4'], '门店', 'num4', 'numReg');
                    $this->_check($info['num5'], '长期稳定合作的城区二批商户', 'num5', 'numReg');
                    $this->_check($info['num6'], '仓库', 'num6', 'numReg');
                    $this->_check($info['num7'], '总面积', 'num7', 'numReg');
                    $this->_check($info['num8'], '小面包', 'num8', 'numReg');
                    $this->_check($info['num9'], '小货车', 'num9', 'numReg');
                    $this->_check($info['num10'], '大货车', 'num10', 'numReg');
                    $this->_check($info['num11'], '管理干部', 'num11', 'numReg');
                    $this->_check($info['num12'], '一线销售人员', 'num12', 'numReg');
                    $this->_check($info['num13'], '财务后勤及配送人员', 'num13', 'numReg');

                    $FormData = D('FormData');
                    $check    = $FormData->checkData($appid, $uid, $formid);
                    foreach ($check as $key => $value) {
                        $content[$value['name']] = $value['content'];
                    }

                    $formsinfo = D('FormInfo')->getFormInfo($appid, $formid);
                    foreach ($formsinfo as $k => $v) {
                        $fieldInfo[$v['name']] = $v['label'];
                    }

                    //字段校验
                    $diff = array_diff_key($info, $fieldInfo);
                    if (!empty($diff)) {
                        $reArr['status'] = 'fail';
                        $reArr['msg']    = '数据错误';
                        $reArr['code']   = '206';
                    } else {
                        $diff1 = array_diff_key($info, $fieldInfo);
                        if ($value['uid'] == null && empty($diff1)) {
                            //返回id
                            $getid = $FormData->recordsData($uid, $appid, $formid, $info);
                            if (!empty($getid)) {
                                $this->sendmail($uid, $appid, $formid, 0);
                                $reArr['status'] = 'success';
                                $reArr['msg']    = '提交成功';
                                $reArr['code']   = '101';
                            }
                        } elseif ($value['uid'] == $uid && $content == $info) {
                            $reArr['status'] = 'fail';
                            $reArr['msg']    = '请勿重复提交';
                            $reArr['code']   = '201';
                        } else {
                            $dif     = array_diff_assoc($info, $content);
                            $getdata = $FormData->editData($uid, $appid, $formid, $dif);
                            if (!empty($getdata)) {
                                $this->sendmail($uid, $appid, $formid, 1);
                                $reArr['status'] = 'success';
                                $reArr['msg']    = '修改成功';
                                $reArr['code']   = '102';
                            }
                        }
                    }
                } else {
                    $reArr['status'] = 'fail';
                    $reArr['msg']    = '表单状态异常';
                }
            }
        } else {
            $reArr['status'] = 'fail';
            $reArr['msg']    = '活动已结束';
        }
        $this->ajaxReturn($reArr);
    }

    /**
     * 验证数据
     * @author   lixiaoxian
     * @datetime 2016-04-18T17:55:33+0800
     * @param    string       $name  表单数据
     * @param    string       $label 标签
     * @param    string       $reg   验证方法
     * @return   json|mixed          返回数据
     */
    private function _check($val, $label, $name, $reg, $appid = 3, $formid = 1)
    {
        //正则验证方法
        $nameReg    = '/^[a-z\x7f-\xff]+$/i';
        $emailReg   = '/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/';
        $phoneReg   = '#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#';
        $qqReg      = '/^\d{5,13}$/';
        $numReg     = '/^\d{0,9}$/';
        $telReg     = '/(\(\d{3,4}\)|\d{3,4}-|\s)?\d{8}/';
        $weChatReg  = '/^[a-zA-Z\d_]{5,}$/';
        $errcharReg = '/\/|\~|\!|\#|\\$|\%|\^|\&|\*|\+|\{|\}|\:|\<|\>|\?|\[|\]|\/|\;|\'|\`|\-|\=|\\\|\|/';

        $length  = strlen($val);
        $getInfo = D('FormInfo')->getFormInfo($appid, $formid);

        //表单信息
        foreach ($getInfo as $key => $value) {
            if ($value['name'] == $name) {
                if ($value['require'] == 1) {
                    //验证是否有值
                    if (isset($val)) {
                        // 验证格式
                        if (!preg_match($$reg, $val)) {
                            $reArr['status'] = 'fail';
                            $reArr['msg']    = '请输入正确的' . $label;
                            $reArr['code']   = '203';
                            $this->ajaxReturn($reArr);
                        }
                        //验证数据长度
                        if ($value['maxlength'] < $length || $length < $value['minlength']) {
                            $reArr['status'] = 'fail';
                            $reArr['msg']    = $label . '输入长度不符';
                            $reArr['code']   = '204';
                            $this->ajaxReturn($reArr);
                        } else {
                            if (preg_match($errcharReg, $val)) {
                                $reArr['status'] = 'fail';
                                $reArr['msg']    = $label . '含有非法字符';
                                $reArr['code']   = '205';
                                $this->ajaxReturn($reArr);
                            }
                        }
                    } else {
                        $reArr['status'] = 'fail';
                        $reArr['msg']    = $label . '不能为空';
                        $reArr['code']   = '202';
                        $this->ajaxReturn($reArr);
                    }
                } else {
                    //验证是否有值
                    if (!empty($val)) {
                        // 验证格式
                        if (!preg_match($$reg, $val)) {
                            $reArr['status'] = 'fail';
                            $reArr['msg']    = '请输入正确的' . $label;
                            $reArr['code']   = '203';
                            $this->ajaxReturn($reArr);
                        }
                        //验证数据长度
                        if ($value['maxlength'] < $length || $length < $value['minlength']) {
                            $reArr['status'] = 'fail';
                            $reArr['msg']    = $label . '输入长度不符';
                            $reArr['code']   = '204';
                            $this->ajaxReturn($reArr);
                        } else {
                            if (preg_match($errcharReg, $val)) {
                                $reArr['status'] = 'fail';
                                $reArr['msg']    = $label . '含有非法字符';
                                $reArr['code']   = '205';
                                $this->ajaxReturn($reArr);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 发送邮件
     * @author   lixiaoxian
     * @datetime 2016-05-06T17:26:15+0800
     * @param    string      $uid    微信uid
     * @param    integer     $formid 表单id
     * @param    integer     $appid  应用id
     * @param    int         $stat   修改状态，1为修改过
     * @return   [type]                           [description]
     */
    public function sendmail($uid, $appid = 3, $formid = 1, $stat)
    {
        vendor('Smtp.PHPMailer');
        $mail = new \PHPMailer(true);

        $FormData = D('FormData');
        $count    = $FormData->getCount($appid, $formid);
        $info     = $FormData->checkData($appid, $uid, $formid);
        foreach ($info as $k => $v) {
            $value[$v['name']] = $v['content'];
        }
        switch ($value['radio1']) {
            case '1':
                $value['radio1'] = '个体户';
                break;
            case '2':
                $value['radio1'] = '有限公司';
                break;
            default:
                $value['radio1'] = '其他';
        }
        switch ($value['radio2']) {
            case '1':
                $value['radio2'] = '小规模纳税人';
                break;
            default:
                $value['radio2'] = '一般纳税人';
        }
        $emailText = '<div style="width:100%;color:grey;font-size:16px;">当前有新的客户申请成为<span style="color:#09F;font-size:16px;">【江小白】</span><span style="color: #000; font-weight: bold;">第' . count($count) . '位</span>申请客户</div>
            <div style=" margin: 0 auto; width: 500px;">
            <table cellpadding="0" cellspacing="0" border="thin solid" style="text-align: center; margin-top:20px;margin-bottom:20px;">
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">申请时间</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . date('Y-m-d H:i:s', $v['created']) . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">申请代理区域</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['area1'] . $value['area2'] . $value['area3'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">企业名称</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['cname'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">申请人姓名</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['name'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">手机</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['phone'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">邮箱</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['email'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">Q Q</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['qq'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">新浪微博</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['sina'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">微信号</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['wechat'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">企业类型</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['radio1'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">纳税人资格</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['radio2'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">办公室地址</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['address'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">办公室电话</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['tel'] . '</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">销售网络</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num1'] . '家</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">直供餐饮终端</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num2'] . '家</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">中小型零售终端</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num3'] . '家</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">门店</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num4'] . '个</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">长期稳定合作的城区二批商户</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num5'] . '家</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">仓库</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num6'] . '间</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">总面积</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num7'] . 'm²</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">小面包</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num8'] . '辆</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">小货车</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num9'] . '辆</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">大货车</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num10'] . '辆</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">管理干部</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num11'] . '人</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">一线销售人员</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num12'] . '人</th>
                </tr>
                <tr style="height: 24px; line-height: 24px; text-align:left;">
                    <th width="220" style="font-size:12px; color:#004499; padding: 0px 10px;">财务后勤及配送人员</th>
                    <th width="220" style="font-size:12px; font-weight: bord; color:#3c5412; padding: 0px 10px;">' . $value['num13'] . '人</th>
                </tr>
            </table>
            </div>';
        $mail->IsSMTP();
        $mail->CharSet  = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = true; //开启认证
        $mail->Port     = 25; //SMTP服务器端口号
        $mail->Host     = C("EMAIL_HOST"); //SMTP服务器
        $mail->Username = C("EMAIL_USERNAME"); //用户名
        $mail->Password = C("EMAIL_PWD"); //密码
        $mail->AddReplyTo(C("EMAIL_REPLYTO"), C("EMAIL_REPLYTONAME")); //回复地址
        $mail->From     = C("EMAIL_FROM"); //发件人
        $mail->FromName = C("EMAIL_FROMNAME"); //发件人姓名
        $mail->AddAddress('jxbzs@ijovo.com', C("EMAIL_NICKNAME")); //收件人昵称
        // $mail->Username = 'leirenlxx@163.com'; //用户名
        // $mail->Password = 'LiXIaoXIan1991'; //密码
        // $mail->AddReplyTo('leirenlxx@163.com', 'lxx'); //回复地址
        // $mail->From     = 'leirenlxx@163.com'; //发件人
        // $mail->FromName = 'lxx'; //发件人姓名
        // $mail->AddAddress('649207432@qq.com', 'lxx'); //收件人昵称
        $mail->Subject = C("EMAIL_SUBJECT");
        if ($stat == 1) {
            $mail->Body = '此数据是用户' . $value['name'] . '修改后的数据，修改如下' . $emailText;
        } else {
            $mail->Body = $emailText . '首次提交数据';
        }
        $mail->IsHTML(true);
        if (!$mail->Send()) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * sdk
     * @author   lixiaoxian
     * @datetime 2016-07-04T21:10:02+0800
     * @param    [type]                   $app [description]
     * @param    [type]                   $url [description]
     * @return   [type]                        [description]
     */
    protected function _sdk($app = 'jxb', $url)
    {
        Vendor('phpRPC.phprpc_client');
        $client = new \PHPRPC_Client('http://10.161.228.164/wechat/api/service/webgrant');

        $data = array(
            'app' => $app,
            'url' => $url,
        );
        $res = $client->sdk($data);
        //判断错误
        if (is_object($res)) {
            exit('error');
        }
        return $res['content'];
    }
}
