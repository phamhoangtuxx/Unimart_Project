@extends('layouts.admin')
@section('content')
<div id="content" class="container-fluid">
    <div class="card">
        <div class="card-header font-weight-bold">
            Thêm bài viết
        </div>
        <div class="card-body">
            @if (\Session::has('message'))
            <div class="alert alert-success">
                {!! \Session::get('message') !!}
            </div>
            @else
                    {!! \Session::get('not_delete_admin') !!}            
        @endif
            <form action="{{route('product.update',$product->id)}}"method="POST"enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="title">Hình ảnh bài viết</label>
                    <input type="file" class="form-control"name="images"value="{{$product->thumbnail}}"> 
                </div>
                <div class="form-group">
                    <label for="name">Tên sản phẩm</label>
                    <input class="form-control" type="text" name="name" id="name"value="{{$product->name}}">
                </div>
               
                <div class="form-group">
                    <label for="name">GIá sản phẩm</label>
                    <input class="form-control" type="text" name="price" id="price"value="{{$product->price}}">
                </div>

                <div class="form-group">
                    <label for="name">Số lượng san pham</label>
                    <input class="form-control" type="text" name="quantity" id="quantity"value="{{$product->quantity}}">
                </div>


                <div class="form-group">
                    <label for="">Danh mục</label>
                    <select class="form-control" id="">
                      <option>Chọn danh mục</option>
                      <option>Danh mục 1</option>
                      <option>Danh mục 2</option>
                      <option>Danh mục 3</option>
                      <option>Danh mục 4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Trạng thái</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                        <label class="form-check-label" for="exampleRadios1">
                          Chờ duyệt
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                        <label class="form-check-label" for="exampleRadios2">
                          Công khai
                        </label>
                    </div>
                </div>
                <button type="submit"name="btn-add-product"value="Thêm sản phẩm" class="btn btn-primary">Thêm sản phẩm</button>
            </form>
        </div>
    </div>
</div>

@endsection