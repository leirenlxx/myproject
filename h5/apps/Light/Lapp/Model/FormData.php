<?php
/**
 * 轻应用-表单数据
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: FormData.php 2016-04-23T13:25:10+0800
 */
namespace Lapp\Model;

use Think\Model;

class FormData extends Model
{
    /**
     * 记录数据
     * @author   lixiaoxian
     * @datetime 2016-04-19T09:13:05+0800
     * @param    string        $uid  微信uid
     * @param    int           $appid  应用id
     * @param    array         $info 表单信息
     * @return   int                      id
     */
    public function recordsData($uid, $appid, $formid, $info)
    {
        $data['uid']       = $uid;
        $data['app_id']    = $appid;
        $data['form_id']   = $formid;
        $data['created']   = NOW_TIME;
        $data['createdby'] = $uid;
        foreach ($info as $key => $value) {
            $data['name']    = $key;
            $data['content'] = $value;
            $result          = $this->add($data);
        }
        return $result;
    }

    /**
     * 检查数据是否重复
     * @author   lixiaoxian
     * @datetime 2016-04-19T09:59:46+0800
     * @param    string      $appid  appid
     * @param    string        $uid  微信uid
     * @param    string      $formid formid
     * @return   array               表单数据信息
     */
    public function checkData($appid, $uid, $formid)
    {
        $map['app_id']  = $appid;
        $map['uid']     = $uid;
        $map['form_id'] = $formid;
        return $this->where($map)->field('app_id,uid,name,content,created')->select();
    }

    /**
     * 修改数据
     * @author   lixiaoxian
     * @datetime 2016-04-20T16:26:36+0800
     * @param    string        $uid  微信uid
     * @param    int         $appid  应用id
     * @param    array        $info  表单数据
     * @return   int                 数据id
     */
    public function editData($uid, $appid, $formid, $info)
    {
        $map['uid']     = $uid;
        $map['app_id']  = $appid;
        $map['form_id'] = $formid;

        $data['modified']   = NOW_TIME;
        $data['modifiedby'] = $uid;

        foreach ($info as $key => $value) {
            $map['name']     = $key;
            $data['content'] = $value;
            $result          = $this->where($map)->save($data);
        }
        return $result;
    }

    /**
     * 统计总人数
     * @author   lixiaoxian
     * @datetime 2016-05-06T17:47:15+0800
     * @param    int         $appid  appid
     * @param    int        $formid  formid
     * @return   array               查询结果
     */
    public function getCount($appid, $formid)
    {
        $map['app_id']  = $appid;
        $map['form_id'] = $formid;
        return $this->where($map)->field('count(uid)')->group('uid')->select();
    }

    /**
     * 获取各选项结果总数
     * @author   lixiaoxian
     * @datetime 2016-05-11T11:58:08+0800
     * @param    int         $appid  appid
     * @param    int        $formid  formid
     * @param    string       $name  type名
     * @param    int           $val  值
     * @return   array               查询结果
     */
    public function getData($appid, $formid, $name, $val)
    {
        $map['app_id']  = $appid;
        $map['form_id'] = $formid;
        $map['name']    = $name;
        //单选类型
        if (!empty(strstr($name, 'radio'))) {
            $map['content'] = $val;
            return $this->where($map)->field('count(content) as count')->select();
        } else {
            //字符串作为条件
            $map['_string'] = 'FIND_IN_SET(' . $val . ',content)';
            return $this->where($map)->field('count(content) as count')->select();
        }
    }

    /**
     * 取各选项总数
     * @author   lixiaoxian
     * @datetime 2016-05-10T15:51:15+0800
     * @param    int         $appid  appid
     * @param    int        $formid  formid
     * @param    string       $name  type名
     * @return   array               查询结果
     */
    public function getAll($appid, $formid, $name)
    {
        $map['app_id']  = $appid;
        $map['form_id'] = $formid;
        $map['name']    = $name;
        return $this->where($map)->count('uid');
    }
}
