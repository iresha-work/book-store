<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> {{ config('app.name', 'Laravel') }} </title>
    <!-- favicon -->
    <link rel="icon" href="https://yt3.ggpht.com/a/AGF-l78km1YyNXmF0r3-0CycCA0HLA_i6zYn_8NZEg=s900-c-k-c0xffffffff-no-rj-mo" type="image/gif" sizes="16x16">
    <!-- header links -->

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script src="https://kit.fontawesome.com/4a3b1f73a2.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <!-- slider links -->

    <script>
        var BASE_URL = '{{url('/')}}';
    </script>

    <script src="http://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css">

    <!-- validate js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <link rel="stylesheet" href="{{asset('css/header.css')}}">
    <link rel="stylesheet" href="{{asset('css/content.css')}}">
    <link rel="stylesheet" href="{{asset('css/contetDetails.css')}}">
    <link rel="stylesheet" href="{{asset('css/footer.css')}}">
    <link rel="stylesheet" href="{{asset('css/cart.css')}}">

    <style>
        body
        {
            margin: 0;
        }
        #containerSlider
        {
            margin: auto;
            width: 90%;
            text-align: center;
            padding-top: 100px;
            box-sizing: border-box;
        }
        #containerSlider img
        {
            width: 100%;
            height: 140%;
            text-align: center;
            align-content: center;
        }
        @media(max-width: 732px)
        {
            #containerSlider img
            {
                height: 12em;
            }
        }
        @media(max-width: 500px)
        {
            #containerSlider img
            {
                height: 10em;
            }
        }

        .error{
            color:red !important;
        }
    </style>
</head>

<body>
@include('front.header',['categoryList' => $categoryList])
<main class="py-4">
    
    @yield('content')
   
</main>
@include('front.footer')
<!-- Modal -->
<div class="modal fade" id="storeModal" tabindex="-1" role="dialog" aria-labelledby="storeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header alert alert-success">
      Biling Details
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="row">
        <div class="col-12 ">
          <div class="card ">
            <div class="card-body">
              <form id="billingform" >
                <div class="row">
                  <div class="col">
                    <div class="form-outline">
                      <label class="form-label" for="fname">First Name</label>
                      <input  type="text" id="fname" name="fname" class="form-control" />
                      
                    </div>
                  </div>
                  <div class="col">
                    <div class="form-outline">
                      <label class="form-label" for="lname">Last Name</label>
                      <input  type="text" id="lname" name="lname" class="form-control" />
                    </div>
                  </div>
                </div>

                <!-- Email input -->
                <div class="form-outline mb-4">
                  
                  <label class="form-label" for="cemail">Email</label>
                  <input  type="email" id="cemail" name="cemail" class="form-control" />
                </div>

                <!-- Number input -->
                <div class="form-outline mb-4">
                  
                  <label class="form-label" for="cmob">Phone</label>
                  <input  type="text" id="cmob" name="cmob" class="form-control" />
                </div>

                <!-- Message input -->
                <div class="form-outline mb-4">
                  <label class="form-label" for="caddress">Address</label>
                  <textarea class="form-control" id="caddress" name="caddress" rows="3"></textarea>
                </div>
                <input type="hidden" name="is_cart_idh" id="is_cart_idh">
                <button type="submit" id="btnPlaceOrder" class="btn btn-primary btn-block">Place Order</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    
</body>

<!-- sweetalert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- slider JS START -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<!-- loading-overlay -->
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    
<script>
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('input').attr('autocomplete', 'off');

    $('#containerSlider').slick({
        dots: true,
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 1500,
    });

    $(document).on("click",".previewImg",function() {
        $('#imgDetails')[0].src = this.src
    });

    $("#billingform").validate({
        rules: {
            fname: {
                required: true,
                minlength : 2,
            },
            lname: {
                required: true,
                minlength : 2,
            },

            cemail: {
                required: true,
                email: true
            },

            cmob: {
                digits: true,
                required: true
            },

            caddress: {
                required: true,
                minlength : 2,
            }
        },
        submitHandler: function(form) {
            Swal.fire({
                title: 'Are you sure ?',
                text: "Do you want place this order",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, place it!',
                reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitOrder();
                    }
            });
        }
    });

    getCartQty();
});


function showMessage(icon , title , timer){
    Swal.fire({
        position: 'top-end',
        toast : true,
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: timer
    });
}

function getCartQty(){
    var is_cart_id = localStorage.getItem("cart_id");
    if(is_cart_id == null){
        return false; 
    }
    
    $('#cartQty').LoadingOverlay("show");
    $.ajax({
        method: "POST",
        url: BASE_URL+"/get/cart/qty",
        data: {
            is_cart_id : is_cart_id
        },
        dataType: "html",
        success: function(response){
            $('#cartQty').html(response);
            $('#cartQty').LoadingOverlay("hide");
        },
        error: function(response){
            $('#cartQty').LoadingOverlay("hide");
        }
    });
}


function submitOrder(){
    var is_cart_id = localStorage.getItem("cart_id");
    if(is_cart_id == null){
        return false; 
    }
    $('#is_cart_idh').val(is_cart_id);
    $("#btnPlaceOrder").LoadingOverlay("show");
    $.ajax({
        method: "POST",
        url: BASE_URL+"/place/order",
        data: $('#billingform').serialize(),
        dataType: "json",
        success: function(response){
            if(response.status){
                localStorage.removeItem("cart_id");
                closeModal();
                $('#billingform')[0].reset();
                new swal({
                    title: "Wow!",
                    text: response.title,
                    type: "success"
                }).then(function() {
                    window.location.reload();
                });

            }else{
                showMessage('error' ,  response.title , 4000);
            }
            $("#btnPlaceOrder").LoadingOverlay("hide");
        },
        error: function(response){
            showMessage('error' , response.responseJSON.message , 4000);
            $("#btnPlaceOrder").LoadingOverlay("hide");
        }
    });
}

function submitAddToCart(pid , cart_qty_update_id , upate_qty){
        $("#btnAddCart").LoadingOverlay("show");
        var cart_id = Math.random().toString(16).slice(2);
        var is_cart_id = localStorage.getItem("cart_id");

        if(is_cart_id == null){
            localStorage.setItem("cart_id", cart_id);
            is_cart_id = cart_id;
        }

        $.ajax({
            method: "POST",
            url: BASE_URL+"/add-to/cart",
            data: {
                pid : pid,
                is_cart_id : is_cart_id,
                cart_qty_update_id : cart_qty_update_id,
                upate_qty : upate_qty
            },
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    getCartQty();
                    if(cart_qty_update_id | (cart_qty_update_id == -1)){
                        getCartAjax(1);
                    }
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnAddCart").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnAddCart").LoadingOverlay("hide");
            }
        });
    }

function closeModal(){
    $('#storeModal').modal('hide');
}
    
</script>
@stack('head')
<!-- slider JS ENDS -->

</html>