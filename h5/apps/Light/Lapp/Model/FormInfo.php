<?php
/**
 * 轻应用-表单信息
 * @author lixiaoxian <lixiaoxian@ijovo.com>
 * @version $Id: FormInfo.php 2016-04-23T14:25:10+0800
 */
namespace Lapp\Model;

use Think\Model;

class FormInfo extends Model
{

    /**
     * 获取表单信息
     * @author   lixiaoxian
     * @datetime 2016-04-18T14:19:19+0800
     * @param    int         $appid  活动id
     * @param    int        $formid  formid
     * @return   array               查询结果
     */
    public function getFormInfo($appid, $formid)
    {
        // JOIN查询成员列表,按照orderlist排序
        $map['app_id']      = $appid;
        $map['form_id']     = $formid;
        $map['info.status'] = 1;
        return $this->where($map)->alias('info')->join('LEFT JOIN jovo_form_conf conf ON info.conf_id = conf.id')->field('info.id,info.label,info.placeholder,info.options,info.name,info.skipto,info.maxlength,info.minlength,info.require,conf.type,conf.reg')->order('orderlist')->select();

        // return $this->query('SELECT info.id,info.label,info.placeholder,info.options,info.markname,info.maxlength,info.minlength,info.require,conf.type FROM __PREFIX__forms_info info LEFT JOIN __PREFIX__forms_conf conf ON info.cid = conf.id WHERE info.pid=' . $appid);
    }
}
