<?php
/**
 * 轻应用-表单数据
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: Form.php 2016-04-23T13:25:10+0800
 */
namespace Lapp\Model;

use Think\Model;

class Form extends Model
{
    /**
     * 表单列表
     * @author   lixiaoxian
     * @datetime 2016-04-23T14:54:54+0800
     * @param    int          $appid 应用id
     * @param    int         $formId 表单id
     * @return   array                 查询数据
     */
    public function getFormList($appid, $formid)
    {
        $map['app_id'] = $appid;
        $map['id']     = $formid;
        $map['status'] = 1;
        return $this->where($map)->field('starttime,endtime,status')->find();
    }
}
