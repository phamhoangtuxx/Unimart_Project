<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Dashboard;
use Illuminate\Support\Facades\DB;
use stdClass;

class AdminProductController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            session(['module_active' => 'product']);
            return $next($request);
        });
    }
    //
    public function list(Request $request)
    {
        $keyword = "";
        if ($request->input('keyword')) {
            $keyword = $request->input('keyword');
        }


        $status = "";
        //Name của select box 
        $selectBox = $request->input('select_status');

        //Name o checkbox
        $checkbox = $request->input('check_box');
        // dd($checkbox);

        //Name danh sách hiện có và xóa tạm thời
        $status = $request->input('status');

        $active_paginate = $request->input('active_paginate');


        $active = DB::table('products')->whereNull('deleted_at')->count();
        $totalScore = Product::onlyTrashed()->count();

        $options = [
            'active' => 'Trạng thái còn hàng',
            'no_active' => 'Trạng thái hết hàng',
            'deleted' => 'Chuyển về kho',
        ];


        $product = Product::where('name', 'LIKE', '%' . $keyword . '%')->paginate(2);

        if ($checkbox) {
            if ($selectBox == $options['active']) {
                DB::table('products')->whereIn('id', $checkbox)->update(['status' => 'Còn hàng',]);
                return redirect('admin/product/list')->with('message', 'Cập nhât trạng thái thành công  ');
            }


            if ($selectBox == $options['no_active']) {
                DB::table('products')->update(['status' => 'hết hàng',]);
                return redirect('admin/product/list')->with('message', 'Cập nhât trạng thái thành công  ');
            }


            //Xóa tạm thời
            if ($selectBox == $options['deleted']) {
                Product::destroy($checkbox);
                return redirect('admin/product/list')->with('message', 'Sản phẩm đã được chuyển về kho');
            }
            $options = [
                'Permanently_deleted' => 'Xóa vĩnh viễn',
                'restore' => 'Sản phẩm kiểm duyệt'
            ];
            if ($selectBox == $options['restore']) {
                Product::withTrashed()->whereIn('id', $checkbox)->restore();
                return redirect('admin/product/list')->with('message', 'Sản phẩm đã có mặt tai cua hang');
            }

            if ($selectBox == $options['Permanently_deleted']) {
                DB::table('products')->whereIn('id', $checkbox)->delete();
                return redirect('admin/product/list')->with('message', 'Sản phẩm không tồn tại');
            }
        }


        //Danh sách vs số lượng còn hàng vs hết hàng 
        if ($status == 'Stocking') {
            $product = DB::table('products')->whereNull('deleted_at')->paginate(5);
        } else if ($status == 'deleted') {
            $options = [
                'Permanently_deleted' => 'Xóa vĩnh viễn',
                'restore' => 'Sản phẩm kiểm duyệt'
            ];
            $product = Product::onlyTrashed()->paginate(5);
        }



        $user_name = Product::join('users', 'products.user_id', '=', 'users.id')
            ->select('users.name', 'users.phone') // Chọn các cột bạn muốn lấy từ cả hai bảng
            ->get('name');

        $user = DB::table('users')->get('id');

        //Đặt sản phẩm






        return view('admin.product.list', compact('product', 'status', 'options', 'checkbox', 'active', 'totalScore', 'user_name'));
    }
    //Kiểm tra id trong form để làm chức năng checkbox

    public function add()
    {
        $product = Product::all();
        return view('admin.product.add', compact('product'));
    }

    public function store(Request $request)
    {

        //Xác định hình ảnh 
        $user = DB::table('users')->get('id');
        foreach ($user as $users) {
        }
        $product = new Product();
        if ($request->has('images')) {
            $file = $request->images;
            $file_name = $file->getClientoriginalName();
            $get_image = $file->move(public_path('uploads_image_product'), $file_name);
        }
        // $upload = me
        $input = $request->all();
        $product->user_id = intval($input['userId']);
        $product->thumbnail = $file_name;
        $product->name    = $input['name'];
        $product->price = $input['price'];
        $product->categories = "";
        $product->cat_id = '1';




        $product->save();
        return redirect('admin/product/list')->with('message', 'Bài viết  được thêm thành công');
    }



    public function edit($id)
    {
        $product = Product::find($id);
        return view('admin/product/edit', compact('product'));
    }
    public function update(Request $request, $id)
    {


        // $post = new Product();
        if ($request->has('images')) {
            $file = $request->images;
            $file_name = $file->getClientoriginalName();
            $get_image = $file->move(public_path('uploads_image_product'), $file_name);
        }
        // $upload = me
        $input = $request->all();
        // dd($file_name);
        // $product = DB::table('products')->get('id');

        $product = DB::table('products')
            ->where('id', $id)
            ->update([
                'thumbnail' => $file_name,
                'name'      => $input['name'],
                'price'      => $input['price'],
                'categories'    => 'Điện thoại thông minh',

            ]);



        // $post->save();
        return redirect('admin/product/list')->with('message', 'Cập nhật bài viết thành công');
    }


    //Xóa 1 san phẩm 
    public function delete($id)
    {
        Product::where('id', $id)->delete();
        return redirect('admin/product/list')->with('message', 'Bạn đã xóa thành công sản phẩm');
    }



    //Danh sách danh mục sản phẩm 
    public function cat_list()
    {
        return view('admin.product.cat.list');
    }

    public function categories(Request $request)
    {
        $category = Product::all();


        //Nut them moi 
        $add_category = $request->input('add-category');

        //Tên danh mục 
        $category_name = $request->input('category_name');
        $checkbox = $request->input('check_box');

        if ($checkbox) {
            $category = DB::table('products')->whereIn('id', $checkbox)->update([
                'categories' => $category_name
            ]);

            return redirect('admin/product/list')->with('message', 'Cập nhật danh mục thành công');
        }




        return view('admin.product.cat.list', compact('category', 'checkbox'));
    }




    public function order(Request $request, $id)
    {

        $product = new Product();

        // $productId = Product::find($id);
        // $userId = Product::find($id);
        $order = new Order();
        $order_test = Order::all();
        $product = Product::find($id);
        $dashboard = new Dashboard();
        $user = new User();

        $input = $request->all();
        $order->user_id = $user->id = $product->user_id = intval($input['userId']);
        if ($product->user_id == $user->id) {
            $order->phone = $user->phone   = $input['phone'];
            $order->username = $user->name = $input['user_name'];
        }
        $order->name = $product->name  = $input['name'];

        $order->price = $product->price = $input['price'];
        $order->user_id = $user->id = $input['userId'];
        $order->quantity = $product->quantity = $dashboard->quantity = $input['quantity'];
        $order->status = $input['status'];
        $order->save();
        return redirect('admin/order/list')->with('message', 'Cập nhật đơn hàng thành công');
    }



    public function byProduct(Request $request, $id)
    {


        // $product->status = 'Đang xử lí';
        $product = Product::find($id);
        foreach ($product as $products) {
            # code...
        }



        $orders = 'Đang xử lí';


        $user = User::join('products', 'users.id', '=', 'products.user_id')
            ->select('users.phone', 'users.name', 'users.id')
            ->get();

        foreach ($user as $users) {
            if ($product->user_id == $users->id) {
                $userId  = $users->id;
                $username = $users->name;
                $phone    = $users->phone;
            }
        }

        return view('admin.product.byProduct', compact('product', 'user', 'users', 'orders', 'userId', 'username', 'phone'));

        // return view('admin.product.byProduct', compact('product'));
    }

    // public function updateQuantity(Request $request, $product_id)
    // {
    //     $product = Product::find($product_id);
    //     dd($product);
    //     if (!$product) {
    //         return redirect('admin/product/list')->with('message', 'Khong the xu ly');
    //     }

    //     $quantity = $request->input('quantity');
    //     // Thực hiện logic tăng giảm số lượng ở đây, ví dụ:
    //     $action = $request->input('action'); // 'increase' or 'decrease'
    //     if ($action === 'increment') {
    //         $product->quantity += $quantity;
    //         DB::table('products')
    //             ->update(['quantity' => $product->quantity]);
    //     }
    //     return redirect('admin/product/list')->with('message', 'Thanh cong');
    // }

    public function increaseQuantity(Request $request)
    {
        $productId = $request->input('product_id');

        $product = Product::findOrFail($productId);

        $product->quantity += 1;
        $product->save();

        return redirect()->back();
    }

    public function decreaseQuantity(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::findOrFail($productId);

        if ($product->quantity > 0) {
            $product->quantity -= 1;
            $product->save();
        }

        return redirect()->back();
    }
}
