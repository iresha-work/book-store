<h4> Checkout </h4>
@php
$amountBookAll = 0;
$iscopun = 0;
@endphp

<h3 id="totalItem"> Total Items: {{!isset($cart_detail->items) ? 0 : sizeof($cart_detail->items)}} </h3>
@if (!empty($cart_detail))
    <div id="cartContainer">
        <div id="boxContainer">
        @forelse ($cart_detail->items as $cartitem)
        <div id="box">
            <img src="{{$url_base.'storage/app/public/'.json_decode($cartitem->book->images)[0]}}">
            <h3><a target="_blank" class="text-info" href="{{url('book-detail/'.$cartitem->book->name.'?pid='.$cartitem->book->id)}}">{{$cartitem->book->name}}</a> × {{$cartitem->qty}}</h3>
            @php
                $amountBook = $cartitem->book->price;
                $discounthtml = 0;
                if(isset($cartitem->discount)){
                    if($cartitem->discount->discount_type == "percentage"){
                        $amountBook = (($cartitem->book->price * $cartitem->qty ) * 
                        (100 - $cartitem->discount->discount_value) / 100);
                    }else{
                        $amountBook = (($cartitem->book->price * $cartitem->qty ) - $cartitem->discount->discount_value);
                    }
                    $discounthtml = 1;
                }
                $amountBookAll+= $amountBook;
            @endphp
            <h4>Amount: {{number_format($amountBook,2,".",",")}} LKR</h4>
            @if ($discounthtml)
                <h5 class="text-success" style="font-size: 1rem">{{$cartitem->discount->name}}</h5>
            @endif
        </div>
        @empty

        <div class="container">
            <div class="row justify-content-md-center">
                <div class="alert alert-dark" role="alert">
                    No Products Found
                </div>
            </div>
        </div>
        @endforelse
        </div>
        <div id="totalContainer">
        <div id="total">
            <h2>Total Amount</h2>
            @if ($copcodet_detail)
                @php
                    $iscopun = 1;
                    if($copcodet_detail->cupon_type == "percentage"){
                        $amountBookAll = ($amountBookAll * 
                        (100 - $copcodet_detail->cupon_value) / 100);
                    }else{
                        $amountBookAll = ($amountBookAll - $copcodet_detail->cupon_value);
                    }
                @endphp
            @endif
            <h4>Amount: {{number_format($amountBookAll,2,".",",")}} LKR</h4>
            
            <p><input type="text" value="{{$copcode}}" id="copcode" class="form-control" style="text-transform:uppercase" placeholder="Enter Coupon Code"></p>
            @if ($iscopun && ($copcode != ''))
                <span class="badge badge-warning mt-2 mb-2">Coupon Applied</span>
                <span class="badge badge-danger ml-1"><a id="removeCop" class="text-white" href="javascript:void(0)">Remove Coupon</a></span>
            @elseif (($iscopun == 0) && ($copcode != ''))
                <span class="badge badge-danger btn-block mt-2 mb-2">Coupon Code Not Valid</span>
            @endif
            <p class="mb-1"><button id="copcodeApply" class="btn btn-block btn-success">Apply Coupon</button></p>
            <div id="button">
                <a id="submitCart" class="btn btn-primary btn btn-block" href="javascript:void(0)">Place Order</a>
            </div>
        </div>
    </div>
    </div>
@else
<div class="container">
    <div class="row justify-content-md-center">
        <div class="alert alert-dark" role="alert">
            Cart Empty
        </div>
    </div>
</div>
@endif

@push('head')
<script>

function placeOrderAjax(){
    var is_cart_id = localStorage.getItem("cart_id");
    if(is_cart_id == null){
        is_cart_id = 0;
    }
    $('.cartMainContainerId').LoadingOverlay("show");
    $.ajax({
        method: "GET",
        url: BASE_URL+"/get/cart/ajax",
        data: {
            is_cart_id : is_cart_id
        },
        dataType: "html",
        success: function(response){
            $('.cartMainContainerId').html(response);
            $('.cartMainContainerId').LoadingOverlay("hide");
        },
        error: function(response){
            $('.cartMainContainerId').LoadingOverlay("hide");
        }
    });
}
</script>
@endpush
