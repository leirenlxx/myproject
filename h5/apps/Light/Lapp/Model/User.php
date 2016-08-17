<?php
namespace Lapp\Model;

use Think\Model;

class User extends Model
{

    // 用户模型自动验证
    protected $_validate = array(
        /* 验证用户名 */
        array('username', '1,30', '用户名长度不符', self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
        array('username', '', '用户名被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
        // 验证密码
        array('password', '6,30', '密码长度不符', self::EXISTS_VALIDATE, 'length'), //密码长度不合法
        //验证邮箱
        array('email', 'email', '邮箱格式不正确', self::EXISTS_VALIDATE),
        array('email', '1,32', '邮箱长度不符', self::EXISTS_VALIDATE, 'length'),
        array('email', '', '邮箱被占用', self::EXISTS_VALIDATE, 'unique'), //邮箱被占用
        // 验证手机号码
        array('mobile', '//', '手机格式不正确', self::EXISTS_VALIDATE), //手机格式不正确
    );

    //自动完成规则
    protected $_auto = array(
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('created', NOW_TIME, self::MODEL_INSERT),
        array('status', 1, self::MODEL_INSERT),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 获取用户信息
     * @author   lixiaoxian
     * @datetime 2016-05-16T10:42:17+0800
     * @param    [type]                   $data [description]
     * @return   [type]                         [description]
     */
    public function getUserInfo($data)
    {
        $map['username'] = $data['username'];
        $map['password'] = $data['password'];
        $map['status']   = 1;
        return $this->where($map)->field('uid,email,username,password,status')->find();
    }

    /**
     * 注册用户
     * @author   lixiaoxian
     * @datetime 2016-05-23T11:36:52+0800
     * @param    array         $data 用户数据
     * @return   mixed               添加信息
     */
    public function register($data)
    {
        $info = array(
            'username' => $data['username'],
            'password' => md5($data['password']),
            'email'    => $data['email'],
            'mobile'   => $data['mobile'],
        );

        //验证手机是否有值
        if (empty($info['mobile'])) {
            unset($info['mobile']);
        }

        //添加用户
        if ($this->create($info)) {
            $uid = $this->add($info);
            return $uid ? $uid : 0; //大于0-注册成功
        } else {
            return $this->getError(); //错误提示
        }
    }

    /**
     * 修改密码
     * @author   lixiaoxian
     * @datetime 2016-05-16T11:00:50+0800
     * @param    [type]                   $data     [description]
     * @param    [type]                   $password [description]
     * @return   [type]                             [description]
     */
    public function updatePwd($uid, $password)
    {
        $map['uid']          = $uid;
        $data['password']    = md5($password);
        $data['update_time'] = NOW_TIME;
        return $this->where($map)->save($data);
    }

    /**
     * 登录信息
     * @author   lixiaoxian
     * @datetime 2016-05-23T17:31:39+0800
     * @param    [type]                   $username [description]
     * @return   [type]                             [description]
     */
    public function login($username)
    {
        $map['username']         = $username;
        $data['last_login_ip']   = get_client_ip(1);
        $data['last_login_time'] = NOW_TIME;
        return $this->where($map)->save($data);
    }
}
