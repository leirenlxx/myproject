<?php
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测是否登录
 * @author   lixiaoxian
 * @datetime 2016-06-12T11:51:09+0800
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @author   lixiaoxian
 * @datetime 2016-06-12T14:20:44+0800
 * @param    [type]                   $data [description]
 * @return   [type]                         [description]
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array) $data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 验证码检测
 * @author   lixiaoxian
 * @datetime 2016-06-12T11:53:55+0800
 * @param    [type]                   $code [description]
 * @param    integer                  $id   [description]
 * @return   [type]                         [description]
 */
function check_verify($code, $id = 1)
{
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}
