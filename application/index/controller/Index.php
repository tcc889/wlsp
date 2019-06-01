<?php
namespace app\index\controller;

use app\admin\model\article;
use app\admin\model\category;
use think\Controller;

class Index extends Controller
{
    public function zbpt()
    {
        $category = $this->categoryList(1);
        //接收id 如果没有id 传输默认为0
        $id = $this->request->param('id',0);


        $this->assign('id',$id);
        //查出新闻中心的所有子分类信息


        $categories = [];
        foreach ($category as $v){
            $categories[] = $v['id'];
        }

        if ($id){
            //当前分类信息
            $categoryInfo = category::where('id',$id)->find();
            $this->assign('categoryInfo',$categoryInfo);
            //文章列表
            $list = article::where('category_id',$id)
                ->where('status',1)
                ->order('create_time desc')
                ->paginate(5);
        }else{
            $this->assign('categoryInfo','');
            $list = article::where('category_id','in', $categories)
                ->where('status', 1)
                ->order('create_time desc')
                ->paginate(10);
        }

        $this->assign('list',$list);

        return $this->fetch();
    }


    protected function categoryList($id){



        $category = category::where('pid', $id)->select();
        $this->assign('category', $category);
        return $category;
    }

//    引入文章
    public function detail()
    {

        $category = $this->categoryList(1);

        $id = $this->request->param('id');

        $info=article::get($id);
        $this->assign('info',$info);

        $this->assign('info',$info);
        //阅读量
        $info->setInc('hits');
        return $this->fetch();



    }






}










