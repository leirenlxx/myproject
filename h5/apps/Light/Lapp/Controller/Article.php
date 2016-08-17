<?php

namespace Lapp\Controller;

use Think\Controller;

class Article extends Controller
{
    public function index()
    {
        $this->display();
    }

    /**
     * 添加文章
     * @author   lixiaoxian
     * @datetime 2016-05-24T16:58:08+0800
     */
    public function addArticle()
    {
        $uid = is_login();
        //是否登录
        if (!$uid) {
            $this->redirect('login');
        }
        if (IS_POST && $uid) {
            $title    = I('post.title', '', 'trim');
            $content  = I('post.content');
            $Article  = D('Article');
            $username = get_nickname($uid);
            if (!empty($title)) {
                if (!empty($content)) {
                    $result = $Article->add($uid, $title, $content, $username);
                    if ($result) {
                        $re['status'] = 'success';
                        $re['msg']    = '成功';
                    } else {
                        $re['status'] = 'fail';
                        $re['msg']    = '失败';
                    }
                } else {
                    $re['status'] = 'fail';
                    $re['msg']    = '内容不能为空';
                }
            } else {
                $re['status'] = 'fail';
                $re['msg']    = '标题不能为空';
            }
        }
        $this->ajaxReturn($re);
    }

    /**
     * 编辑文章
     * @author   lixiaoxian
     * @datetime 2016-05-25T10:38:55+0800
     * @return   [type]                   [description]
     */
    public function editArticle()
    {
        $uid = is_login();
        //是否登录
        if (!$uid) {
            $this->redirect('login');
        }
        $article = D('article');
        $info    = $article->getInfo($id);
        if (IS_POST && $uid) {
            $aid     = I('get.aid', '', intval);
            $content = I('post.content');
            if ($info['content'] == $content) {
                $re['status'] = 'fail';
                $re['msg']    = '文章未修改';
            } else {
                //存入数据
                $result = $article->edit($id, $content);
                if ($result) {
                    $re['status'] = 'success';
                    $re['msg']    = '修改成功';
                } else {
                    $re['status'] = 'fail';
                    $re['msg']    = '请重试';
                }
            }
        } else {
            $this->assign('info', $info);
            $this->display();
        }
        $this->ajaxReturn($re);
    }

    /**
     * 删除文章
     * @author   lixiaoxian
     * @datetime 2016-05-26T17:24:55+0800
     * @return   [type]                   [description]
     */
    public function delArticle()
    {
        $uid = is_login();
        //是否登录
        if (!$uid) {
            $this->redirect('login');
        }
        $aid     = I('get.aid');
        $article = D('article');
        $aids    = $article->getField('id', true);
        if (!in_array($aid, $aids)) {
            $this->error('请勿修改相关数据');
        }
        if ($article->delete()) {
            $re['status'] = 'success';
            $re['msg']    = '删除成功';
        } else {
            $re['status'] = 'fail';
            $re['msg']    = '请重试';
        }
        $this->ajaxReturn($re);
    }

    /**
     * 查看我的文章
     * @author   lixiaoxian
     * @datetime 2016-05-26T17:28:04+0800
     * @return   [type]                   [description]
     */
    public function myArticle()
    {
        $uid = is_login();
        //是否登录
        if (!$uid) {
            $this->redirect('login');
        }
        $Article = D('Article');
        //我的文章总数
        $count = $Article->getMyCount($uid);
        //页码
        $p       = I('get.p');
        $pageNum = ceil($count / 9);
        if (null != $p) {
            if (intval($p) <= 0) {
                $page = 1;
            } elseif (intval($p) > $pageNum) {
                $page = $pageNum;
            } else {
                $page = intval($p);
            }
        } else {
            $page = 1;
        }

        // 进行分页数据查询
        $list = $Article->where('uid=' . $uid . ',status=1')->order('created')->page($page . ',9')->select();
        $this->assign('list', $list); // 赋值数据集
        $count = $User->where('status=1')->count(); // 查询满足要求的总记录数
        $Page  = new \Think\Page($count, 9); // 实例化分页类 传入总记录数和每页显示的记录数
        $show  = $Page->show(); // 分页显示输出
        $this->assign('page', $show); // 赋值分页输出
        $this->display(); // 输出模板

    }

    /**
     * 点赞
     * @author   lixiaoxian
     * @datetime 2016-05-27T11:13:10+0800
     * @return   [type]                   [description]
     */
    public function like()
    {
        $uid = is_login();
        //是否登录
        if (!$uid) {
            $this->redirect('login');
        }
        if (IS_AJAX) {
            $data['uid'] = $uid;
            $data['aid'] = I('post.aid', 0, 'intval');
            $Article     = D('Article');
            $count       = M('like')->where($data)->count();
            if ($count) {
                $re['msg'] = '您已经点过赞';
            } else {
                if (M('like')->add($data)) {
                    if ($Article->like($data['aid'])) {
                        //此处可添加发送点赞信息
                        $re['msg'] = '点赞成功';
                    } else {
                        $re['msg'] = '请重试';
                    }
                }
            }
        }
        $this->ajaxReturn($re);
    }
}
