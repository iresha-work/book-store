@extends('layouts.welcome')
@section('content')
<div id="containerProduct">
        <!-- JS rendered code -->
    <div id="containerD" class="p-1">
        <div id="imageSection">
            @if (!empty($book->images))
                <img id="imgDetails" src="{{$url_base.'storage/app/public/'.json_decode($book->images)[0]}}">
            @endif
        </div>
        <div id="productDetails">
            <h1>{{$book->name}}</h1>
            <h4>{{$book->category->name}}</h4>
            <div id="details">
                <h3>{{number_format($book->price ,2,".",",")}} LKR</h3>
                <h3>Description</h3>
                <p>{!!$book->description!!}</p>
            </div>
            <div id="productPreview">
                <h3>Product Preview</h3>
                @if (!empty($book->images))
                    @foreach (json_decode($book->images) as $keyim => $image)
                        <img id="previewImg{{$keyim}}" class="previewImg" src="{{$url_base.'storage/app/public/'.$image}}">
                    @endforeach
                @endif
                
            </div>
            <div id="button">
                <button data-pid="{{$book->id}}" id="btnAddCart">Add to Cart</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head')
<script>
    $(document).ready(function () {
        $(document).on("click","#btnAddCart",function() {
            var pid = $(this).data('pid');
            if(pid != ''){
                submitAddToCart(pid ,0,0);
            }
        });
    });
    
    

    
</script>
@endpush