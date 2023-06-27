@extends('layouts.app')
@section('content')


@php
$netTotalBookAll = 0;
$amountBookAll = 0;
@endphp
<!-- Main content -->
<div class="container">
<div class="invoice p-3 mb-3">
<!-- title row -->
<div class="row">
<div class="col-12">
    <h4>
    <i class="fas fa-globe"></i> Order ID {{$order_detail->id}}
    <small class="float-right">Date: {{$order_detail->created_at}}</small>
    </h4>
</div>
<!-- /.col -->
</div>
<!-- info row -->
<div class="row invoice-info">
<!-- /.col -->
<div class="col-sm-4 invoice-col">
    To - <strong>{{$order_detail->customer->first_name . ' '.$order_detail->customer->last_name}}</strong><br>
    Email - {{$order_detail->customer->email}}<br>
    Mobile - {{$order_detail->customer->contact_no}}<br>
    <address>
    Address - {{$order_detail->customer->billing_address}}
    </address>
</div>
</div>
<!-- /.row -->

<!-- Table row -->
<div class="row">
    <div class="col-12 table-responsive">
        <table class="table table-striped">
        <thead>
        <tr>
            
            <th>Product</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Subtotal</th>
            <th class="text-right">Discount</th>
            <th class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
            @forelse ($order_detail->items as $oditem)
            @php
                $amountBook = $oditem->price;
                if(isset($oditem->discount)){
                    if($oditem->discount->discount_type == "percentage"){
                        $amountBook = (($oditem->price * $oditem->qty ) * 
                        (100 - $oditem->discount->discount_value) / 100);
                    }else{
                        $amountBook = (($oditem->price * $oditem->qty ) - $oditem->discount->discount_value);
                    }
                    
                }
                $netTotalBookAll += $amountBook;
            @endphp
                <tr>
                    <td>{{$oditem->book->name}}</td>
                    <td class="text-right">{{$oditem->qty}}</td>
                    <td class="text-right">{{number_format($oditem->price * $oditem->qty,2,".",",")}}</td>
                    <td class="text-right">{{number_format(($oditem->price * $oditem->qty) - $amountBook,2,".",",")}}</td>
                    <td class="text-right">{{number_format($amountBook,2,".",",")}}</td>
                </tr>
            @empty
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="alert alert-dark" role="alert">
                        Cart Empty
                    </div>
                </div>
            </div>
            @endforelse
        </tbody>
        </table>
    </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
    <!-- /.col -->
    <div class="col-sm-8 offset-sm-4">
        <div class="table-responsive">
        <table class="table">
            <tr>
            <th>Sub Total </th>
            <td class="text-right">{{number_format($netTotalBookAll,2,".",",")}} LKR</td>
            </tr>
            <tr>
            <th style="width:50%">Copun Code:</th>
            <td class="text-right">
            @if ($copcodet_detail)
                @php
                    if($copcodet_detail->cupon_type == "percentage"){
                        $netTotalBookAll = ($netTotalBookAll * 
                        (100 - $copcodet_detail->cupon_value) / 100);
                    }else{
                        $netTotalBookAll = ($netTotalBookAll - $copcodet_detail->cupon_value);
                    }
                @endphp
                Code - {{$copcodet_detail->cupon_code .' | Applied Value - '.number_format($netTotalBookAll- $amountBookAll,2,".",",")}} LKR
            @endif
            </td>
            </tr>
            <tr>
            <th>Total </th>
            <td class="text-right">{{number_format($netTotalBookAll,2,".",",")}} LKR</td>
            </tr>
        </table>
        </div>
    </div>
<!-- /.col -->
</div>
</div> 
<!-- /.row -->
@endsection