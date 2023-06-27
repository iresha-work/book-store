@extends('layouts.welcome')

@section('content')

@include('front.slider')
<div id="mainContainer">
@forelse($bookList as $keyct => $book)
<h1 class="p-1">{{$category_map[$keyct]->name}}</h1>
<div id="containerClothing">
    @forelse($book as $bookmap)
    <div id="box">
        <a target="_blank" href="{{url('book-detail/'.$bookmap->name.'?pid='.$bookmap->id)}}">
            @if (empty($bookmap->images))
                <img src="https://assets.myntassets.com/h_1440,q_100,w_1080/v1/assets/images/7579188/2018/11/5/08a7b230-ee8f-46c0-a945-4e835a3c01c01541402833619-United-Colors-of-Benetton-Men-Sweatshirts-1271541402833444-1.jpg">
            @else
                <img class="img-fluid img-thumbnail" alt="{{$bookmap->name}}" src="{{$url_base.'storage/app/public/'.json_decode($bookmap->images)[0]}}">
            @endif
            <div id="details">
                <h3>{{$bookmap->name}}</h3>
                <h2>{{number_format($bookmap->price ,2,".",",")}} LKR</h2>
            </div>
        </a>
    </div>
    @empty
        <p>No Products</p>
    @endforelse
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
@endsection