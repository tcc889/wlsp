<?php
namespace app\index\controller;

use app\admin\model\article;
use think\Controller;

class Index extends Controller
{
    public function zbpt()
    {
        $id = $this->request->param('id',0);
        $this->assign('id',$id);
        //查出新闻中心的所有子分类信息
        $category = $this->categoryList(1);

        $categories = [];
        foreach ($category as $v){
            $categories[] = $v['id'];
        }

        return $this->fetch();
    }



    protected function categoryList($id){

        $category = category::where('pid', $id)->select();
        $this->assign('category', $category);
        return $category;
    }





    public function detail()
    {
        $category = $this->categoryList(1);

        $id = $this->request->param('id');

        $info = article::get($id);

        $this->assign('info',$info);

        $info->seInc('hists');

        return $this->fetch();

    }

}