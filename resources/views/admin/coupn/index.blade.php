@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="card card-primary">
                            <div class="card-header bg-success text-white">
                                <h5 class="card-title">{{ __('Add Coupon') }}</h5>
                            </div>

                            <form autocomplte="off" id="coupn-add-form" method="POST" >
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="cp_code">Coupon Code</label>
                                        <input style="text-transform:uppercase" autocomplete="off" required type="text" class="form-control" id="cp_code" name="cp_code" placeholder="Enter Coupon Code">
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-sm-6">
                                            <label for="cp_start">Start Date</label>
                                            <input min="{{date('Y-m-d')}}" type="date" name="cp_start" id="cp_start" class="form-control" placeholder="Start Date">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="cp_end">Start End</label>
                                            <input min="{{date('Y-m-d')}}" type="date" name="cp_end" id="cp_end" class="form-control" placeholder="End Date">
                                        </div>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="cp_type">Coupon Type</label>
                                        <select name="cp_type" class="form-control" required id="cp_type">
                                        <option value="">Choose Type</option>
                                            <option value="percentage">Percentage</option>
                                            <option value="amount">Amount</option>
                                        </select>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="cp_price">Coupon Value</label>
                                        <input required type="text" class="form-control" id="cp_price" name="cp_price" placeholder="Coupn Value">
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" id="btnCoupnAdd" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-sm-7">
                        <div class="card card-primary">
                            <div class="card-header bg-primary text-white mb-2">
                                <h5 class="card-title">{{ __('List Coupon Category') }}</h5>
                            </div>
                            <table id="coupn-list" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Code</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Type</th>
                                        <th class="text-right">Value</th>
                                        <th>Create At</th>
                                        <th class="text-center">Action</th>
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
    var coupn_add_form = "coupn-add-form";
    var coupn_edit_form = "coupn-edit-form";

    var coupn_list = "";
    $(document).ready(function () {
        jQuery.validator.addMethod("lettersonly", function(value, element) 
        {
        return this.optional(element) || /^[a-z ]+$/i.test(value);
        }, "Contains only letters");

        jQuery.validator.addMethod("noSpace", function(value, element) { 
        return value.indexOf(" ") < 0 && value != ""; 
        }, "No space please and don't leave it empty");
        
        
        $(document).on("click",".coupon-edit",function() {
            var pid = $(this).data('pid');
            if(pid != ''){
                getCoupon(pid, $(this));
            }
        });

        $(document).on("click","#btnCouponDelete",function() {
            var pid = $(this).data('pid');
            if(pid != ''){
                
                Swal.fire({
                    title: 'Are you sure ?',
                    text: "Do you want delete this",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteCoupon(pid);
                        }
                });
            }
        });

        $("#"+coupn_add_form).validate({
            rules: {
                cp_code: {
                    required: true,
                    minlength : 2,
                    maxlength : 10,
                    noSpace : true
                },
                cp_start: {
                    required: true,
                },
                cp_end: {
                    required: true,
                },
                cp_price: {
                    required: true,
                    number: true,
                    max: isMaxCheckCoupon
                },
            },
            messages: {
                cp_code: {
                    required: "Code Required",
                    minlength: "Enter at least {0} characters"
                },

                cp_start: {
                    required: "Start Date Required"
                },

                cp_end: {
                    required: "End Date Required"
                },

                cp_price: {
                    required: "Price Required",
                },
            },
            submitHandler: function(form) {
                Swal.fire({
                    title: 'Are you sure ?',
                    text: "Do you want save this",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!',
                    reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitCoupn();
                        }
                });
            }
        });
        
        coupn_list = $('#coupn-list').DataTable({
             processing: true,
             serverSide: true,
             responsive: true,
             ajax: "{{route('coupon.list.ajax')}}",
             columns: [
                 { data: 'pid' },
                 { data: 'cupon_code' },
                 { data: 'start_date' },
                 { data: 'end_date' },
                 { data: 'cupon_type' },
                 { data: 'cupon_value', className: "text-right" },
                 { data: 'created_at' },
                 { data: 'id',className: 'text-center' , orderable: false },
             ],
             order: [[0, 'desc']]
         });
    });

    function isMaxCheckCoupon() {
        if($("#cp_type option:selected").val() == "percentage"){
            return 100;
        }
    }

    function realodCouponList(){
        coupn_list.ajax.reload();
    }

    function editFormValidation(){
        $("#"+coupn_edit_form).validate({
            rules: {
                cp_code: {
                    required: true,
                    minlength : 2,
                    maxlength : 10,
                    noSpace : true
                },
                cp_start: {
                    required: true,
                },
                cp_end: {
                    required: true,
                },
                cp_price: {
                    required: true,
                    number: true
                },
            },
            messages: {
                cp_code: {
                    required: "Code Required",
                    minlength: "Enter at least {0} characters"
                },

                cp_start: {
                    required: "Start Date Required"
                },

                cp_end: {
                    required: "End Date Required"
                },

                cp_price: {
                    required: "Price Required",
                },
            },
            submitHandler: function(form) {
                Swal.fire({
                    title: 'Are you sure ?',
                    text: "Do you want update this",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!',
                    reverseButtons: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitCoupnUpdate();
                        }
                    });
            }
        });
    }

    function submitCoupn(){
        $("#btnCoupnAdd").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/coupon-store-ajax",
            data: $('#'+coupn_add_form).serialize(),
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' ,  response.title , 2000);
                    $('#'+coupn_add_form)[0].reset();
                    realodCouponList();
                }else{
                    showMessage('error' ,  response.title , 4000);
                }
                $("#btnCoupnAdd").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnCoupnAdd").LoadingOverlay("hide");
            }
        });
    }

    function deleteCoupon(pid){
        $("#btnCouponDelete").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/coupon-delete-ajax",
            data: {
                pid : pid
            },
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    realodCouponList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnCouponDelete").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnCouponDelete").LoadingOverlay("hide");
            }
        });
    }
    
    function submitCoupnUpdate(){
        $("#btnCouponEdit").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/coupon-update-ajax",
            data: $('#'+coupn_edit_form).serialize(),
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    $('#'+coupn_edit_form)[0].reset();
                    realodCouponList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnCouponEdit").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnCouponEdit").LoadingOverlay("hide");
            }
        });
    }

    function getCoupon(pid , objref){
            $(objref).LoadingOverlay("show");
            $.ajax({
                method: "GET",
                url: BASE_URL_ADMIN+"/get-coupon",
                data: {
                    pid : pid
                },
                dataType: "html",
                success: function(response){
                    $('#commonModal').html(response);
                    $('#commonModalMain').modal('show');
                    $(objref).LoadingOverlay("hide");
                    editFormValidation();
                },
                error: function(response){
                    $(objref).LoadingOverlay("hide");
                }
            });
        }         
</script>
@endpush
