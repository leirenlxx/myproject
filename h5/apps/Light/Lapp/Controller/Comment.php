<?php

namespace Lapp\Controller;

use Think\Controller;

class Comment extends Controller
{
    /**
     * 模板渲染
     * @author   lixiaoxian
     * @datetime 2016-05-12T11:24:49+0800
     * @return   [type]                   [description]
     */
    public function index()
    {
        //已登录
        if (session('userinfo.uid')) 
        {
        $Comment = D('Comment');
            //$comment_id   = I('get.comment_id', 0, 'intval');
            
            $replyList   = $Comment->getReplyList(4);
            $commentList = $Comment->getCommentList();
            foreach ($commentList as $key => $value) {
        $commentList[$key]['created'] = date('H:i:s', $value['created']);
            }
            foreach ($replyList as $k => $v) {
                $replyList[$k]['created'] = date('H:i:s', $value['created']);
            }
            $this->assign('replylist', $replyList);
            $this->assign('commentlist', $commentList);
            $this->display('index');
        } else {
            $this->login();
        }
    }

    /**
     * 登录界面
     * @author   lixiaoxian
     * @datetime 2016-05-17T14:35:41+0800
     * @return   [type]                   [description]
     */
    public function login()
    {
        if (session('userinfo.uid')) {
            $this->index();
        } else {
            //var_dump(session('userinfo.uid'));
            $this->display('login');
        }
    }

    /**
     * 注册页面
     * @author   lixiaoxian
     * @datetime 2016-05-17T14:35:34+0800
     * @return   [type]                   [description]
     */
    public function reg()
    {
        if (session('userinfo.uid')) {
            $this->index();
        } else {
            $this->display('reg');
        }
    }

    /**
     * 添加评论
     * @author   lixiaoxian
     * @datetime 2016-05-12T09:46:49+0800
     * @return   [type]                   [description]
     */
    public function addComment()
    {
        // $uid     = I('get.uid', '', 'trim');
        $content = I('post.comment');
        // $type    = I('get.type', 1, 'intval');
        $Comment = D('Comment');
        $uid     = $this->isLogin();

        //防止重复点击提交
        $this->clickLimit();

        if (!empty($content)) {
            //$result = $this->_checkContent(4, $uid['uid'], $content);
            //if ($result) {
            $re = $Comment->addComment($uid['uid'], $content);
            if ($re) {
                $return['status'] = 'success';
                $return['msg']    = '评论成功';
                session('userinfo.addtime', NOW_TIME);
            } else {
                $return['status'] = 'fail';
                $return['msg']    = '请重试';
            }
            // } else {
            //     $return['status'] = 'fail';
            //     $return['msg']    = '勿重复提交';
            // }
        } else {
            $return['status'] = 'fail';
            $return['msg']    = '内容不能为空';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 删除评论
     * @author   lixiaoxian
     * @datetime 2016-05-12T10:08:42+0800
     * @return   [type]                   [description]
     */
    public function delComment()
    {
        //$uid       = I('get.uid', 0, 'intval');
        $commentId = I('post.comment_id', 0, 'intval');
        $Comment   = D('Comment');
        $result    = $Comment->checkInfo($commentId);
        $uid       = $this->isLogin();
        if ($uid['uid'] == $result['uid']) {
            if ($commentId > 0) {
                $re = $Comment->delComment($commentId, $uid['uid']);
                if ($re) {
                    $return['status'] = 'success';
                    $return['msg']    = '删除成功';
                } else {
                    $return['status'] = 'fail';
                    $return['msg']    = '请重试';
                }
            } else {
                $return['status'] = 'fail';
                $return['msg']    = '参数错误';
            }
        } else {
            $return['status'] = 'fail';
            $return['msg']    = 'uid有误';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 编辑评论
     * @author   lixiaoxian
     * @datetime 2016-05-12T10:25:03+0800
     * @return   [type]                   [description]
     */
    public function editComment()
    {
        //$uid       = I('get.uid', 0, 'intval');
        $commentId = I('post.comment_id', 0, 'intval');
        $content   = I('post.content');
        $result    = $Comment->checkInfo($commentId);
        $uid       = $this->isLogin();
        if ($uid['uid'] == $result['uid']) {
            if ($commentId == 0) {
                $re = D('Comment')->editComment($commentId, $content);
                if (isset($re)) {
                    $return['status'] = 'success';
                    $return['msg']    = '编辑成功';
                } else {
                    $return['status'] = 'fail';
                    $return['msg']    = '请重试';
                }
            } else {
                $return['status'] = 'fail';
                $return['msg']    = '参数错误';
            }
        } else {
            $return['status'] = 'fail';
            $return['msg']    = 'uid参数错误';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 添加回复
     * @author   lixiaoxian
     * @datetime 2016-05-12T09:56:55+0800
     * @return   [type]                   [description]
     */
    public function sendReply()
    {
        //$uid        = I('get.uid');
        $content   = I('post.info');
        $commentId = I('post.comment_id', 0, 'intval');
        //$reply_id   = I('get.reply_id', 0, 'intval');
        //$type    = I('get.type', 2, 'intval');
        $Comment = D('Comment');
        //是否已经登录
        $uid = $this->isLogin();
        //防止重复点击提交
        $this->clickLimit();

        if (!empty($content) && is_array($content)) {
            foreach ($content as $k => $v) {
            }
            $result = $this->_checkContent(4, $uid['uid'], $v);
            if ($result) {
                //写入数据库
                $Comment->addReply($uid['uid'], $v, $commentId);
                // //获取at人信息
                // $nickname = $this->_atNickname($v);
                // //发送at信息
                // if (!empty($nickname)) {
                //     foreach ($nickname as $key => $value) {
                //         $this->_sendMessage($value);
                //     }
                // }
                $return['status'] = 'success';
                $return['msg']    = '回复成功';
            } else {
                $return['status'] = 'fail';
                $return['msg']    = '勿重复提交';
            }
        } else {
            $return['status'] = 'fail';
            $return['msg']    = '回复内容不能为空';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 删除回复
     * @author   lixiaoxian
     * @datetime 2016-05-12T17:09:00+0800
     * @return   [type]                   [description]
     */
    public function delReply()
    {
        //$uid     = I('get.uid', '', 'trim');
        $replyId = I('post.reply_id', 0, 'intval');
        $uid     = $this->isLogin();
        if (empty($uid)) {
            $return['status'] = 'fail';
            $return['msg']    = '请先登录';
            $this->ajaxReturn($return);
        }
        if ($replyId != 0) {
            $re = D('Comment')->delReply($uid, $replyId);
            if ($re) {
                $return['status'] = 'success';
                $return['msg']    = '删除成功';
            } else {
                $return['status'] = 'fail';
                $return['msg']    = '请重试';
            }
        } else {
            $return['status'] = 'fail';
            $return['msg']    = '呵呵';
        }
        $this->ajaxReturn($return);
    }

    /**
     * 登录
     * @author   lixiaoxian
     * @datetime 2016-05-16T10:14:51+0800
     * @return   [type]                   [description]
     */
    public function signIn()
    {
        if (IS_POST) {
            $data['username'] = I('post.username', '', 'trim');
            $data['password'] = I('post.password', '', 'trim');
            $data['verify']   = I('post.verify');
            //验证码
            $checkResult = $this->checkVerify($data['verify']);
            if ($checkResult) {
                $userinfo = D('User')->getUserInfo($data['username']);
                if (!empty($userinfo)) {
                    if (md5($data['password']) == $userinfo['password']) {
                        if ($userinfo['status'] == 0) {
                            $return['status'] = 'fail';
                            $return['msg']    = '账号被禁';
                        } else {
                            $this->autoLogin($userinfo);
                        }
                    } else {
                        $return['status'] = 'fail';
                        $return['msg']    = '密码错误';
                    }
                } else {
                    $return['status'] = 'fail';
                    $return['msg']    = '用户不存在,请注册';
                }
            } else {
                $return['status'] = 'fail';
                $return['msg']    = '验证码错误';
            }
        }
        $this->ajaxReturn($return);
    }

    /**
     * 注册
     * @author   lixiaoxian
     * @datetime 2016-05-16T10:23:48+0800
     * @return   [type]                   [description]
     */
    public function signUp()
    {
        $rules = array(
            array('verify', 'checkVerify', '验证码有误', 'callback', 3),
            array('verify', 'require', '验证码必须'),
            array('username', '', '帐号名称已经存在！', 0, 'unique', 1), // 在新增的时候验证username字段是否唯一
            //array('repassword', 'password', '确认密码不正确', 0, 'confirm'), // 验证确认密码是否和密码一致
            //array('password', 'checkPwd', '密码格式不正确', 0, 'function'), // 自定义函数验证密码格式
            array('email', 'require', '邮箱必须！'),
            array('mobile', 'require', '手机号必须'),
        );

        if (IS_POST) {
            $data['username'] = I('post.username', '', 'trim');
            $data['password'] = I('post.password', '', 'trim');
            $data['email']    = I('post.email', '', 'trim');
            $data['mobile']   = I('post.mobile', '', 'trim');
            $data['verify']   = I('post.verify', 0, 'intval');
            //实例化User
            $User = D('User');
            //$userinfo = $User->checkUser($data['username']);
            // if (!empty($userinfo)) {
            //     $return['status'] = 'fail';
            //     $return['msg']    = '已注册';
            // } else {
            //var_dump($data['verify']);
            //var_dump($User->validate($rules)->create());
            //自动验证
            if (!$User->validate($rules)->create()) {
                $return['status'] = 'fail';
                $return['msg']    = $User->getError();
            } else {
                //$id = $User->addUser($data);
                if ($id) {
                    $return['status'] = 'success';
                    $return['msg']    = '注册成功';
                } else {
                    $return['status'] = 'fail';
                    $return['msg']    = '请重试';
                }
            }
            // if ($data['password'] != '') {
            //     if ($data['email'] != '') {

            //     } else {
            //         $return['status'] = 'fail';
            //         $return['msg']    = '邮箱不能为空';
            //     }
            // } else {
            //     $return['status'] = 'fail';
            //     $return['msg']    = '密码不能为空';
            // }
            // }
        }
        $this->ajaxReturn($return);
    }

    /**
     * 忘记密码
     * @author   lixiaoxian
     * @datetime 2016-05-16T10:27:29+0800
     * @return   [type]                   [description]
     */
    public function lostPwd()
    {
        if (IS_POST) {
            $data['username'] = I('post.username', '', 'trim');
            $data['email']    = I('post.email', '', 'trim');
            $User             = D('User');
            $userinfo         = $User->getUserInfo($data['username']);
            if ($userinfo) {
                if ($data['email'] == $userinfo['email']) {
                    $password = I('post.password');
                    if ($password != '') {
                        $re = $User->updatePwd($data, $password);
                        if ($re) {
                            $return['status'] = 'success';
                            $return['msg']    = '修改成功';
                        } else {
                            $return['status'] = 'fail';
                            $return['msg']    = '请重试';
                        }
                    } else {
                        $return['status'] = 'fail';
                        $return['msg']    = '密码不能为空';
                    }
                } else {
                    $return['status'] = 'fail';
                    $return['msg']    = '请输入正确邮箱';
                }
            } else {
                $return['status'] = 'fail';
                $return['msg']    = '用户不存在';
            }
        }
        $this->ajaxReturn($return);
    }

    /**
     * 自动登录
     * @author   lixiaoxian
     * @datetime 2016-05-16T10:23:54+0800
     * @param    [type]                   $user [description]
     * @return   [type]                         [description]
     */
    public function autoLogin($user)
    {
        $auth = array(
            'uid'      => $user['uid'],
            'username' => $user['username'],
            'role'     => 'user',
        );
        //记录登录session
        session('userinfo', $auth);
        D('User')->autoLogin($user);
        $return['status'] = 'success';
        $return['msg']    = '登录成功';
        $this->ajaxReturn($return);
    }

    /**
     * 退出登录
     * @author   lixiaoxian
     * @datetime 2016-05-16T15:53:21+0800
     * @return   [type]                   [description]
     */
    public function logout()
    {
        if (IS_POST) {
            if (session('userinfo.uid')) {
                session('userinfo', null);
                session('[destroy]');
                $return['status'] = 'success';
                $return['msg']    = '退出成功';
                $this->ajaxReturn($return);
            } else {
                $this->redirect('login');
            }
        }
    }

    /**
     * 生成验证码
     * @author   lixiaoxian
     * @datetime 2016-05-17T16:05:35+0800
     * @return   [type]                   [description]
     */
    public function verify()
    {
        $Verify           = new \Think\Verify();
        $Verify->fontSize = 16;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->codeSet  = '0123456789';
        $Verify->imageW   = 160;
        $Verify->imageH   = 50;
        $Verify->entry();
    }

    /**
     * 连续点击限制
     * @author   lixiaoxian
     * @datetime 2016-05-18T17:40:30+0800
     * @return   [type]                   [description]
     */
    public function clickLimit()
    {
        if (NOW_TIME - session('userinfo.addtime') < 10) {
            $return['status'] = 'fail';
            $return['msg']    = '10秒后重试';
            $this->ajaxReturn($return);
        }
    }

    /**
     * 检查验证码
     * @author   lixiaoxian
     * @datetime 2016-05-17T16:19:02+0800
     * @param    [type]                   $code [description]
     * @param    string                   $id   [description]
     * @return   [type]                         [description]
     */
    public function checkVerify($code, $id = '')
    {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    /**
     * 获取at人的昵称
     * @author   lixiaoxian
     * @datetime 2016-05-16T16:45:00+0800
     * @param    [type]                   $content [description]
     * @return   [type]                            [description]
     */
    private function _atNickname($content)
    {
        //@正则
        $pattern = '/\@\[[A-Za-z0-9_\x{4e00}-\x{9fa5}\x80-\xff]+\]/u';
        preg_match_all($pattern, $content, $userinfo);
        $nickname = array();
        foreach ($userinfo as $key => $value) {
            $nickname[] = substr($value, 2);
        }
        return array_unique($nickname);
    }

    /**
     * 发送回复信息
     * @author   lixiaoxian
     * @datetime 2016-05-16T16:47:05+0800
     * @return   [type]                   [description]
     */
    private function _sendAtMessage($nickname)
    {
        D('reply')->sendMessage($nickname);
    }

    /**
     * 检查内容
     * @author   lixiaoxian
     * @datetime 2016-05-13T16:32:55+0800
     * @param    [type]                   $appid   [description]
     * @param    [type]                   $uid     [description]
     * @param    [type]                   $content [description]
     * @return   [type]                            [description]
     */
    private function _checkContent($appid, $uid, $content)
    {
        $Comment = D('Comment');
        $data    = $Comment->getInfo($appid, $uid, $content);
        if (empty($data)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 是否已登录
     * @author   lixiaoxian
     * @datetime 2016-05-18T14:15:03+0800
     * @return   boolean                  [description]
     */
    private function isLogin()
    {
        $uid = session('userinfo');
        if (empty($uid)) {
            $return['status'] = 'fail';
            $return['msg']    = '请先登录';
            $this->ajaxReturn($return);
        } else {
            return $uid;
        }
    }
}
