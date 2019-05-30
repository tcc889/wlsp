<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/20
 * Time: 下午3:12
 */

namespace app\admin\controller;

use app\admin\model\category;
use think\Controller;

class Article extends Controller
{

    /*
     * 添加
     */

    public function bj(){
        return $this->fetch();
    }

    public function xg(){
        //判断是否为GET请求
        if ($this->request->isGet()) {
            //获取url穿过来的id值
            $id = $this->request->param('id');
            //获取一条记录并把它转成数组
            $list = \app\admin\model\article::get($id)->toArray();
            //自定义标签
            $this->assign('list', $list);
            return $this->fetch();
        }
        //判断是否为POST请求
        if ($this->request->isPost()){
            $id = $this->request->param('id');
            $data = $this->request->only(['title','content','author']);
            //验证
            $rule = [
                'title' => 'require|length:1,50',
                'author' =>'length:2,10',
                'content' => 'require|length:10,65535',
            ];
            $msg=[
                'title.require' => '标题为必填项',
                'title.length:1,50' => '标题长度过长或过短',
                'author.length' => '署名长度应在2-10之间',
                'content.require' => '文章内容为必填项',
                'content.length' =>'文章内容长度过长或者过短'
            ];
            $check = $this->validate($data,$rule,$msg);
            if ($check!== true){
                $this->error('错误');
            }
            $a = \app\admin\model\article::get($id);
            if ($a->content == $data['content']){
                $this->error('您的内容必须修改');
            }
            if ($a->save($data)){
                $this->success('成功',url('admin/Article/lists'));
            }else{
                $this->error('失败');
            }
    }
        }

    public function add(){

        $re = $this->request;

        if  ($re->isPost()){

            $data = $re->only(['title','category_id','author','content','status','thumb','minthumb']);

            $rule = [
                'title'=>'require|length:1,50',
                'category_id' => 'require|min:1',
                'author' => 'length:2,10',
                'content' => 'require|length:10,65535',
                'status' => 'in:0,1'
            ];
            $msg = [
                'title.require' => '文章标题为必填项',
                'title.length' => '文章标题长度应在1-50个字符之间',
                'category_id.min' => '请选择正确的分类信息',
                'author.length' => '署名长度应在2-10个字符之间',
                'content.require' => '文章内容为必填项',
                'content.length' => '文章内容过短或者过长',
                'status.in' => '文章状态有误'
            ];
            $check = $this->validate($data,$rule,$msg);
            if ($check !== true){
                $this->error($check);
            }

            $data['aid'] = session('adminLoginInfo')->id;

            if (\app\admin\model\article::create($data)){
                $this->success('添加成功', url('admin/Article/lists'));
            }else{
                $this->error('添加失败');
            }
        }

        if ($re->isGet()){

            $all = category::where('pid',0)->all();

            $this->assign('all', $all);
            return $this->fetch();
        }


    }
/**
* 使用ajax获取文章分类
* @return \think\response\Json
* @throws \think\db\exception\DataNotFoundException
* @throws \think\db\exception\ModelNotFoundException
* @throws \think\exception\DbException
*/
    public function ajaxCategory()
    {
        $pid = $this->request->param('id', 0);
        $data = category::where('pid', $pid)->select();
        return json($data);
    }


    /**
     * 文章列表
     */
    public function lists()
    {
        $list = \app\admin\model\article::with('category')->order('create_time DESC')->paginate(4);
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function changeStatus()
    {
        $id = $this->request->param('id');
        if (empty($id)) {
            return $this->error('非法操作');
        }

        $obj = \app\admin\model\article::get($id);
        if (empty($obj)) {
            return $this->error('非法操作');
        }

        $obj->status = abs($obj->status - 1);

        if ($obj->save()) {
            return $this->success('成功', '', $obj->status);
        } else {
            return $this->error('失败');
        }
    }

        //文章删除
        public function del()
        {
            //获取ajax传来的id
            $id = $this->request->param('id');

            $data = \app\admin\model\article::get($id);

            if ($data->delete()){
                $this->success('成功');
            }else{
                $this->error('失败');
            }

    }

    /*
     * 图片上传服务的
     */
    public function uploadImage()
    {
        $image = $this->request->file('file');
        $res = $image->validate(['size' => 1048576, 'ext' => 'jpg,peg,gif,jpeg'])->move('static/upload/');

        if ($res){
            $path = $res->getPathname();
            $min = $res->getPath().'/min'.$res->getFilename();

            $im = \think\Image::open($path);

            $im->thumb(60,60,\think\Image::THUMB_CENTER)->save($min);
            return json(['code'=>1, 'thumb'=> $path, 'min'=> $min]);
        }else{
            return json(['code'=>0, 'info'=>$image->getError()]);
        }
    }


    public function ueUpload()
    {
        $configData = file_get_contents("static/ui/library/ue/php/config.json");
        $config = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $configData), true);

        if ($this->request->isGet()){
            return json_encode($config);
        }

        if ($this->request->isPost()){
            $image = $this->request->file('upfile');
            $allow = 'png,jpg,jpeg,gif,bmp,flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid,rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md';
            $res = $image->validate(['size'=>1048576, 'ext'=>$allow])->move('static/upload/');
            if ($res){
                $info =  [
                    "originalName" => $res->getFilename() ,
                    "name" => $res->getSaveName() ,
                    "url" => '/'.$res->getPathname() ,
                    "size" => $res->getSize() ,
                    "type" => $res->getExtension() ,
                    "state" => 'SUCCESS'
                ];
                return json_encode($info);
            }
        }else{
            return [
                'state' => 'ERROR'
            ];
        }
    }


}