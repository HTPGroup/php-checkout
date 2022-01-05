<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán đơn hàng</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
<div class="container mt-4">
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="card">
        <div class="card-header text-center font-weight-bold">
            Thanh toán đơn hàng
        </div>
        <div class="card-body">
            <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('store-form')}}">
                @csrf

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">App ID</label>
                    <input type="text" id="title" name="key" class="form-control" required="" value="64815">
                </div>

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">App Code</label>
                    <input type="text" id="title" name="code" class="form-control" required=""
                           value="f3485d1b6be04a344f8e8c95b7e7534e">
                </div>

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">Nội dung</label>
                    <input type="text" id="title" name="title" class="form-control" required=""
                           value="Thanh toán đơn hàng 10001">
                </div>

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">Full Name</label>
                    <input type="text" id="title" name="fullname" class="form-control" required=""
                           value="Phan Xuân Dũng">
                </div>

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">Email</label>
                    <input type="text" id="title" name="email" class="form-control" value="dungpx@htpgroup.com.vn">
                </div>

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">Phone</label>
                    <input type="text" id="title" name="phone" class="form-control" value="0367158269">
                </div>

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">Price</label>
                    <input type="text" id="title" name="price" class="form-control" value="200000">
                </div>

                <div class="form-group mb-3">
                    <label for="exampleInputEmail1">Mô tả</label>
                    <textarea name="description" class="form-control" required="">Mô tả</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Thanh toán</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
