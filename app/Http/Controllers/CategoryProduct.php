<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CategoryProduct extends Controller
{
    //Admin page
    public function authLogin()
    {
        $admin_id = Session::get('admin_id');
        if ($admin_id){
            return Redirect::to('admin.dashboard');
        } else {
            return Redirect::to('admin')->send();
        }
    }
    public function add_category_product()
    {
        $this->authLogin();
        return view('admin.add_category_product');
    }

    public function all_category_product()
    {
        $this->authLogin();
        $all_category_product = DB::table('tbl_category_product')->get();
        $manager_category_products = view('admin.all_category_product')->with('all_category_product', $all_category_product);
        return view('admin_layout')->with('admin.all_category_product', $manager_category_products);

    }

    public function save_category_product(Request $request)
    {
        $this->authLogin();
        $data = array();
        $data['category_name'] = $request->category_product_name;
        $data['category_desc'] = $request->category_product_desc;
        $data['category_status'] = $request->category_product_status;

        DB::table('tbl_category_product')->insert($data);
        Session::put('message', 'Thêm danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');
    }

    public function unactive_category_product($category_product_id)
    {
        $this->authLogin();
        DB::table('tbl_category_product')->where('category_id', $category_product_id)->update(['category_status'=>1]);
        Session::put('message', 'Không kích hoạt danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');

    }
    public function active_category_product($category_product_id)
    {
        $this->authLogin();
        DB::table('tbl_category_product')->where('category_id', $category_product_id)->update(['category_status'=>0]);
        Session::put('message', 'Kích hoạt danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');
    }

    public function edit_category_product($category_product_id)
    {
        $this->authLogin();
        $edit_category_product = DB::table('tbl_category_product')->where('category_id', $category_product_id)->get();
        $manager_category_products = view('admin.edit_category_product')->with('edit_category_product', $edit_category_product);
        return view('admin_layout')->with('admin.edit_category_product', $manager_category_products);

    }

    public function update_category_product(Request $request, $category_product_id)
    {
        $this->authLogin();
        $data = array();
        $data['category_name'] = $request->category_product_name;
        $data['category_desc'] = $request->category_product_desc;
        DB::table('tbl_category_product')->where('category_id', $category_product_id)->update($data);
        Session::put('message', 'Cập nhật danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');
    }

    public function delete_category_product($category_product_id)
    {
        $this->authLogin();
        DB::table('tbl_category_product')->where('category_id', $category_product_id)->delete();
        Session::put('message', 'Xóa danh mục sản phẩm thành công');
        return Redirect::to('all-category-product');
    }

    //User page
    public function show_category_home($category_id)
    {
        $cate_product = DB::table('tbl_category_product')->where('category_status','=','0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('tbl_brand_product')->where('brand_status','=','0')->orderBy('brand_id', 'desc')->get();

        $categoy_by_id = DB::table('tbl_product')
            ->join('tbl_category_product','tbl_product.category_id','=','tbl_category_product.category_id')
            ->where('tbl_product.category_id','=',$category_id)->get();

        $category_name = DB::table('tbl_category_product')->where('tbl_category_product.category_id','=', $category_id)->limit(1)->get();

        return view('pages.category.show_category')
            ->with('category',$cate_product)
            ->with('brand',$brand_product)
            ->with('category_by_id',$categoy_by_id)
            ->with('category_name',$category_name);
    }

}
