@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-header bg-primary text-white mb-2">
                                <h5 class="card-title">{{ __('Orders') }}</h5>
                            </div>
                            <table id="order-list" class="display" style="width:100%">
                                <thead>
                                    <tr> 
                                        <th>#ID</th>
                                        <th>Customer Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Date</th>
                                        <th>Books</th>
                                        <th class="text-right">Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('head')
<script>
    var orders_list = "";
    $(document).ready(function () {
        orders_list = $('#order-list').DataTable({
             processing: true,
             serverSide: true,
             responsive: true,
             ajax: "{{route('orders.list.ajax')}}",
             columns: [
                 { data: 'pid' },
                 { data: 'cname' },
                 { data: 'cemail'},
                 { data: 'cmob' },
                 { data: 'created_at' },
                 { data: 'bcount', className: "text-right" },
                 { data: 'amounod' ,className: "text-right" },
                 { data: 'id',className: 'text-center' , orderable: false },
             ],
             order: [[0, 'desc']]
         });
    });

    function realodOrderList(){
        orders_list.ajax.reload();
    }
</script>
@endpush
