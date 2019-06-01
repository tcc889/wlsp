<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/31
 * Time: 下午5:27
 */

namespace app\index\controller;

use app\admin\model\category;
use think\Controller;

class Duye extends Controller
{
    public function xxx()
    {

        /**添数据上传
         */

        $re = $this->request;

        if ($re->isPost()) {
            //获取HTML所取值
            $data = $re->only(['notype', 'companyDesc', 'matnr', 'comtel', 'address', 'suihao', 'image', 'pernr', 'phone', 'email',]);
            //验证获取信息
            $rule = [
                'notype' => 'require',
                'companyDesc' => 'require',
                'matnr' => 'require|length:1,50'
            ];
            $msg = [
                'notype.require' => '必须填写'
            ];
            $check = $this->validate($data, $rule, $msg);

              //数据验证不对 报错

            if ($check !== true) {
                $this->error($check);
            }

            if (\app\admin\model\forms::create($data)) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }

            if ($re->isGet()) {
//                $all = category::where('pid', 0)->all();

//                $this->assign('all', $all);
//                return $this->fetch();
            }
//            return $this->fetch();

        }
        return $this->fetch();

    }

}