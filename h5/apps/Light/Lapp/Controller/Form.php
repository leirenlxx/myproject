<?php
/**
 * 轻应用-表单
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Form.php 2016-04-13T17:19:49+0800
 */
namespace Lapp\Controller;

use Think\Controller;

class Form extends Controller
{
    /**
     * index页面
     * @author   lixiaoxian
     * @datetime 2016-04-13T17:19:49+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        // 渲染表单
        $appid  = I('get.appid', 0, 'intval');
        $formid = I('get.formid', 0, 'intval');
        $forms  = D('FormInfo')->getFormInfo($appid, $formid);
        foreach ($forms as $key => $value) {
            if ($value['options'] != '') {
                $forms[$key]['options'] = explode(",", $value['options']);
            }
            if ($value['skipto'] != '') {
                $forms[$key]['skipto'] = explode(",", $value['skipto']);
            }
        }
        $this->assign('forms', $forms);
        $this->display();
    }

    /**
     * 表单模板
     * @author   lixiaoxian
     * @datetime 2016-04-22T16:00:45+0800
     * @return   [type]                   [description]
     */
    public function form()
    {
        // 渲染表单
        $appid  = I('get.appid', 0, 'intval');
        $formid = I('get.formid', 0, 'intval');
        $forms  = D('FormInfo')->getFormInfo($appid, $formid);
        foreach ($forms as $key => $value) {
            if ($value['options'] != '') {
                $forms[$key]['options'] = explode(",", $value['options']);
            }
            if ($value['skipto'] != '') {
                $forms[$key]['skipto'] = explode(",", $value['skipto']);
            }
        }
        $this->assign('forms', $forms);
        $this->display();
    }

    /**
     * 跳题模式
     * @author   lixiaoxian
     * @datetime 2016-04-27T14:52:43+0800
     * @return   [type]                   [description]
     */
    public function mode2Form()
    {
        $info = I('post.info');
        // $appid    = I('get.app_id', 0, 'intval');
        // $uid    = I('get.uid', '', 'trim');
        $appid  = 4;
        $uid    = 'wx_963';
        $formid = 1;

        $FormInfo = D('FormInfo');
        $getInfo  = $FormInfo->getFormInfo($appid, $formid);

        $data     = array();
        $subIndex = 0; //从0开始，第1题为0。
        $noSkip   = 0; //从0开始，要跳过的题

        //判断需要接收的$info数据，存入$data
        foreach ($getInfo as $k => $v) {
            if ($subIndex == $k) {
                //是否必填
                if ($getInfo[$k]['require'] == 1) {
                    //是否有输入
                    if (!empty($info[$v['name']])) {
                        //是否有选项
                        if (!empty($v['options'])) {
                            $options = explode(',', $v['options']);
                            //是否为数组
                            if (is_array($info[$v['name']])) {
                                foreach ($info[$v['name']] as $key => $value) {
                                    if ($value > count($options)) {
                                        $re['msg'] = ($subIndex + 1) . '没有这个选项';
                                        $this->ajaxReturn($re);
                                    }
                                }
                                //是否存在跳题
                                if ($v['skipto'] != 0) {
                                    $arr   = explode(',', $v['skipto']);
                                    $index = $info[$v['name']] - 1;
                                    if ($index == max(array_keys($arr))) {
                                        $noSkip   = 0;
                                        $subIndex = $arr[$index] - 1;
                                    } else {
                                        $noSkip   = $arr[$index + 1] - 1;
                                        $subIndex = $noSkip - 1;
                                    }
                                } else {
                                    if ($noSkip != 0) {
                                        $subIndex = $noSkip + 1;
                                        $noSkip   = 0;
                                    } else {
                                        $subIndex++;
                                    }
                                }
                            } else {
                                //选项总数
                                if ($info[$v['name']] <= count($options)) {
                                    if ($v['skipto'] != 0) {
                                        $arr   = explode(',', $v['skipto']);
                                        $index = $info[$v['name']] - 1;
                                        if ($index == max(array_keys($arr))) {
                                            $noSkip   = 0;
                                            $subIndex = $arr[$index] - 1;
                                        } else {
                                            $noSkip   = $arr[$index + 1] - 1;
                                            $subIndex = $noSkip - 1;
                                        }
                                    } else {
                                        if ($noSkip != 0) {
                                            $subIndex = $noSkip + 1;
                                            $noSkip   = 0;
                                        } else {
                                            $subIndex++;
                                        }
                                    }
                                } else {
                                    $re['msg'] = ($subIndex + 1) . '没有这个选项';
                                    $this->ajaxReturn($re);
                                }
                            }
                        } else {
                            $subIndex++;
                        }
                        $data[$v['name']] = $info[$v['name']];
                    } else {
                        $re['msg'] = ($subIndex + 1) . '为必填项';
                        $this->ajaxReturn($re);
                    }
                } else {
                    //是否有输入
                    if (!empty($info[$v['name']])) {
                        //是否有选项
                        if (!empty($v['options'])) {
                            $options = explode(',', $v['options']);
                            //是否为数组
                            if (is_array($info[$v['name']])) {
                                foreach ($info[$v['name']] as $key => $value) {
                                    if ($value >= count($options)) {
                                        $re['msg'] = ($subIndex + 1) . '没有这个选项';
                                        $this->ajaxReturn($re);
                                    }
                                }
                                //是否存在跳题
                                if ($v['skipto'] != 0) {
                                    $arr   = explode(',', $v['skipto']);
                                    $index = $info[$v['name']] - 1;
                                    if ($index == max(array_keys($arr))) {
                                        $noSkip   = 0;
                                        $subIndex = $arr[$index] - 1;
                                    } else {
                                        $noSkip   = $arr[$index + 1] - 1;
                                        $subIndex = $noSkip - 1;
                                    }
                                } else {
                                    if ($noSkip != 0) {
                                        $subIndex = $noSkip + 1;
                                        $noSkip   = 0;
                                    } else {
                                        $subIndex++;
                                    }
                                }
                            } else {
                                //选项总数
                                if ($info[$v['name']] <= count($options)) {
                                    if ($v['skipto'] != 0) {
                                        $arr   = explode(',', $v['skipto']);
                                        $index = $info[$v['name']] - 1;
                                        if ($index == max(array_keys($arr))) {
                                            $noSkip   = 0;
                                            $subIndex = $arr[$index] - 1;
                                        } else {
                                            $noSkip   = $arr[$index + 1] - 1;
                                            $subIndex = $noSkip - 1;
                                        }
                                    } else {
                                        if ($noSkip != 0) {
                                            $subIndex = $noSkip + 1;
                                            $noSkip   = 0;
                                        } else {
                                            $subIndex++;
                                        }
                                    }
                                } else {
                                    $re['msg'] = ($subIndex + 1) . '没有这个选项';
                                    $this->ajaxReturn($re);
                                }
                            }
                        } else {
                            $subIndex++;
                        }
                        $data[$v['name']] = $info[$v['name']];
                    } else {
                        $subIndex++;
                    }
                }
            }
        }

        //验证数据
        $newdata = $this->_checkData($data, $appid, $formid, $getInfo);
        // print_r($info);
        // print_r($data);
        // print_r($newdata);

        //验证通过，记录数据
        $FormData = D('FormData');
        $check    = $FormData->checkData($appid, $uid, $formid);
        if (empty($check['0']['uid'])) {
            $FormData->recordsData($uid, $appid, $formid, $newdata);
            $reArr['status'] = 'success';
            $reArr['msg']    = '提交成功';
            $reArr['code']   = '101';
        } else {
            $reArr['status'] = 'fail';
            $reArr['msg']    = '你已经提交过';
            $reArr['code']   = '201';
        }
        $this->ajaxReturn($reArr);

    }

    /**
     * 表单信息
     * @author   lixiaoxian
     * @datetime 2016-04-18T09:56:06+0800
     * @return   [type]                   [description]
     */
    public function mode1Form()
    {
        $info = I('post.info');
        // $appid    = I('get.app_id', 0, 'intval');
        // $uid    = I('get.uid', '', 'trim');
        // $formid=I('get.formid','','intval');
        $appid  = 4;
        $uid    = 'wx_123';
        $formid = 2;

        //字段校验
        $FormData = D('FormData');
        $check    = $FormData->checkData($appid, $uid, $formid, $getInfo);
        foreach ($check as $key => $value) {
            $content[$value['name']] = $value['content'];
        }

        $FormInfo = D('FormInfo');
        $forminfo = $FormInfo->getFormInfo($appid, $formid);
        foreach ($forminfo as $k => $v) {
            $fieldInfo[$v['name']] = $v['label'];
        }

        $diff1 = array_diff_key($info, $fieldInfo);
        if (!empty($diff1)) {
            $reArr['status'] = 'fail';
            $reArr['msg']    = '数据错误';
            $reArr['code']   = '206';
        } else {
            //数据类型校验
            $newdata = $this->_checkData($info, $appid, $formid);
            $diff2   = array_diff_key($newdata, $fieldInfo);
            if ($value['uid'] == null && empty($diff2)) {
                $FormData->recordsData($uid, $appid, $formid, $newdata);
                $reArr['status'] = 'success';
                $reArr['msg']    = '提交成功';
                $reArr['code']   = '101';
            } elseif ($value['uid'] == $uid && $content == $newdata) {
                $reArr['status'] = 'fail';
                $reArr['msg']    = '请勿重复提交';
                $reArr['code']   = '201';
            } else {
                $dif = array_diff_assoc($newdata, $content);
                $FormData->editData($uid, $appid, $formid, $dif);
                $reArr['status'] = 'success';
                $reArr['msg']    = '修改成功';
                $reArr['code']   = '102';
            }
        }
        $this->ajaxReturn($reArr);
    }

    /**
     * 统计选项
     * @author   lixiaoxian
     * @datetime 2016-05-09T17:14:14+0800
     * @return   [type]                   [description]
     */
    public function count()
    {
        $appid    = 4;
        $formid   = 1;
        $getInfo  = D('FormInfo')->getFormInfo($appid, $formid);
        $FormData = D('FormData');

        //大数据量会导致内存不够
        //$data    = D('FormData')->getData($appid, $formid);
        //print_r($data);
        //print_r(D('FormData')->getLastSql());exit;
        //$count = array();
        // foreach ($data as $key => $value) {
        //     foreach ($getInfo as $k => $v) {
        //         if ($value['name'] == $v['name']) {
        //             if (!empty(strstr($value['name'], 'radio'))) {
        //                 //选项总数
        //                 $count[$v['label']]['count']++;
        //                 if (!empty($v['options'])) {
        //                     $options = explode(',', $v['options']);
        //                     foreach ($options as $k1 => $v1) {
        //                         //对应选项
        //                         switch ($value['content']) {
        //                             case $k1 + 1:
        //                                 $count[$v['label']][$v1]++;
        //                                 break;
        //                             default:
        //                                 break;
        //                         }
        //                     }
        //                 }
        //             }
        //             if (!empty(strstr($value['name'], 'checkbox'))) {
        //                 //选项总数
        //                 $count[$v['label']]['count']++;
        //                 if (is_string($value['content'])) {
        //                     //选项值
        //                     $checkbox = explode(',', $value['content']);
        //                     if (!empty($v['options'])) {
        //                         $opt = explode(',', $v['options']);
        //                         foreach ($opt as $k3 => $v3) {
        //                             foreach ($checkbox as $k2 => $v2) {
        //                                 //对应选项
        //                                 switch ($v2) {
        //                                     case $k3 + 1:
        //                                         $count[$v['label']][$v3]++;
        //                                         break;
        //                                     default:
        //                                         break;
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
        $return = array();
        $count  = array();
        foreach ($getInfo as $k => $v) {
            //选择题类型
            if (!empty(strstr($v['name'], 'radio')) || !empty(strstr($v['name'], 'checkbox'))) {
                //总数
                $re = $FormData->getAll($appid, $formid, $v['name']);
                if (!empty($v['options'])) {
                    $options = explode(',', $v['options']);
                    foreach ($options as $k1 => $v1) {
                        //选项统计
                        $return = $FormData->getData($appid, $formid, $v['name'], $k1+'1');
                        foreach ($return as $k2 => $v2) {
                            $All[$v1]                  = $v2;
                            $count[$v['label']][$v1]   = $All[$v1]['count'];
                            $count[$v['label']]['all'] = $re;
                        }
                    }
                }
            }
        }
        print_r($count);
    }

    /**
     * 数据验证
     * @author   lixiaoxian
     * @datetime 2016-05-09T13:25:45+0800
     * @param    array        $data  字段校验后的数据
     * @param    int         $appid  appid
     * @param    int        $formid  formid
     * @param    array     $getInfo  查询结果
     * @return   mixed|array         返回结果
     */
    private function _checkData($data, $appid, $formid, $getInfo)
    {
        foreach ($getInfo as $kk => $val) {
            //分别验证每一条数据
            foreach ($data as $key => $value) {
                if ($key == $val['name']) {
                    //复选框类型
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            $this->_check($v, $val['label'], $val['reg'], $appid, $key, $formid, $getInfo);
                        }
                        $newdata[$key] = implode(',', $value);
                    } else {
                        $this->_check($value, $val['label'], $val['reg'], $appid, $key, $formid, $getInfo);
                        if (!empty(strstr($key, 'date'))) {
                            $data[$key] = strtotime($value);
                        }
                        $newdata[$key] = $data[$key];
                    }
                }
            }
        }
        return $newdata;
    }

    /**
     * 数据验证
     * @author   lixiaoxian
     * @datetime 2016-05-09T12:28:20+0800
     * @param    mixed         $val  值
     * @param    string      $label  标签
     * @param    string        $reg  正则
     * @param    int         $appid  appid
     * @param    string       $name  字段名
     * @param    int        $formid  表id
     * @param    array     $getInfo  查询结果
     * @return   json|mixed          返回结果
     */
    private function _check($val, $label, $reg, $appid, $name, $formid, $getInfo)
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

        if (!empty(strstr($name, 'date'))) {
            $date    = strstr($name, 'date');
            $datelen = strtotime($val);
        }

        $length = strlen($val);
        //表单信息
        foreach ($getInfo as $key => $value) {
            //字段对比
            if ($value['name'] == $name) {
                if ($value['require'] == 1) {
                    //验证是否有值
                    if (isset($val)) {
                        //是否为时间格式
                        if (!empty($date)) {
                            //验证数据长度
                            if ($value['maxlength'] < $datelen || $datelen < $value['minlength']) {
                                $reArr['status'] = 'fail';
                                $reArr['msg']    = $label . '时间长度不符';
                                $reArr['code']   = '204';
                                $this->ajaxReturn($reArr);
                            }
                        } else {
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
                                $reArr['msg']    = $label . '数据长度不符';
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
                    } else {
                        $reArr['status'] = 'fail';
                        $reArr['msg']    = $label . '不能为空';
                        $reArr['code']   = '202';
                        $this->ajaxReturn($reArr);
                    }
                } else {
                    //验证是否有值
                    if (isset($val)) {
                        if (!empty($date)) {
                            //验证数据长度
                            if ($value['maxlength'] < $datelen || $datelen < $value['minlength']) {
                                $reArr['status'] = 'fail';
                                $reArr['msg']    = $label . '时间长度不符';
                                $reArr['code']   = '204';
                                $this->ajaxReturn($reArr);
                            }
                        } else {
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
                                $reArr['msg']    = $label . '数据长度不符';
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
    }
}
