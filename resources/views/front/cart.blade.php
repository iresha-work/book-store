@extends('layouts.welcome')
@section('content')
<div id="cartMainContainer" class="cartMainContainerId">
    
</div>
@endsection

@push('head')
<script>
$(document).ready(function(){
    getCartAjax(1);

    $(document).on("click","#copcodeApply",function() {
        var copcode = $('#copcode').val();
        getCartAjax(1);
        
    });

    $(document).on("click","#removeCop",function() {
        getCartAjax(-1);
    });

    $(document).on("click","#submitCart",function() {
        $('#storeModal').modal('show');
    });

    $(document).on("change",".qty-update",function() {
        var bid = $(this).data('bid');
        var pid = $(this).data('pid');
        submitAddToCart(bid , pid , $(this).val());
    });

    $(document).on("click",".remove-itemcart",function() {
        var bid = $(this).data('bid');
        var pid = $(this).data('pid');
        submitAddToCart(bid , pid , -1);
    });

});

function getCartAjax(clrcop){
    var is_cart_id = localStorage.getItem("cart_id");
    if(is_cart_id == null){
        is_cart_id = 0;
    }
    $('.cartMainContainerId').LoadingOverlay("show");
    $.ajax({
        method: "GET",
        url: BASE_URL+"/get/cart/ajax",
        data: {
            is_cart_id : is_cart_id,
            copcode : (clrcop == -1) ? -1 : $('#copcode').val()
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