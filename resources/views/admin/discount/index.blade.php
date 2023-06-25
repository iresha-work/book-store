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
                                <h5 class="card-title">{{ __('Add Discount Rule') }}</h5>
                            </div>

                            <form autocomplte="off" id="discount-add-form" method="POST" >
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="dis_code">Rule Name</label>
                                        <input autocomplete="off" required type="text" class="form-control" id="dis_code" name="dis_code" placeholder="Enter Rule Name">
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-sm-6">
                                            <label for="dis_start">Start Date</label>
                                            <input min="{{date('Y-m-d')}}" type="date" name="dis_start" id="dis_start" class="form-control" placeholder="Start Date">
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="dis_end">Start End</label>
                                            <input min="{{date('Y-m-d')}}" type="date" name="dis_end" id="dis_end" class="form-control" placeholder="End Date">
                                        </div>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="dis_type">Discount Type</label>
                                        <select name="dis_type" class="form-control" required id="dis_type">
                                        <option value="">Choose Type</option>
                                            <option value="percentage">Percentage</option>
                                            <option value="amount">Amount</option>
                                        </select>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="dis_map">Discount Map</label>
                                        <select name="dis_map" class="form-control" required id="dis_map">
                                        <option value="">Choose Type</option>
                                            <option value="category_id">Category</option>
                                            <option value="all">Order</option>
                                        </select>
                                    </div>

                                    <div class="form-group mt-2 d-none" id="catgory" >
                                        <label for="dis_cat">Choose Category</label>
                                        <select name="dis_cat" class="form-control" id="dis_cat">
                                            <option value="">Choose Category</option>
                                            @foreach ($categoryList as $categoryob)
                                                <option value="{{$categoryob->id}}">{{$categoryob->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="dis_price">Discount Value</label>
                                        <input required type="text" class="form-control" id="dis_price" name="dis_price" placeholder="Discount Value">
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" id="btnDiscountnAdd" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-sm-7">
                        <div class="card card-primary">
                            <div class="card-header bg-primary text-white mb-2">
                                <h5 class="card-title">{{ __('List Discount Rule') }}</h5>
                            </div>
                            <table id="discount-list" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Rule</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Type</th>
                                        <th>Map</th>
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
    var discount_add_form = "discount-add-form";
    var discount_edit_form = "discount-edit-form";

    var discount_list = "";
    $(document).ready(function () {
        jQuery.validator.addMethod("lettersonly", function(value, element) 
        {
        return this.optional(element) || /^[a-z ]+$/i.test(value);
        }, "Contains only letters");

        jQuery.validator.addMethod("noSpace", function(value, element) { 
        return value.indexOf(" ") < 0 && value != ""; 
        }, "No space please and don't leave it empty");
        
        
        $(document).on("change","#dis_map",function() {
            
            if($(this).val() == "category_id"){
                $('#catgory').removeClass("d-none");
                $("#dis_cat").attr("required",true);
            }else{
                $("#dis_cat").attr("required",false);
                $('#catgory').addClass("d-none");
            }
            
        });

        $(document).on("click",".discount-edit",function() {
            var pid = $(this).data('pid');
            if(pid != ''){
                getDiscount(pid, $(this));
            }
        });

        $(document).on("click","#btnDiscountDelete",function() {
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
                            deleteDiscount(pid);
                        }
                });
            }
        });

        $("#"+discount_add_form).validate({
            rules: {
                dis_code: {
                    required: true,
                    minlength : 2,
                },
                dis_start: {
                    required: true,
                },
                dis_end: {
                    required: true,
                },
                dis_price: {
                    required: true,
                    number: true,
                    max: isMaxCheckDiscount
                },
            },
            messages: {
                dis_code: {
                    required: "Rule Required",
                    minlength: "Enter at least {0} characters"
                },

                dis_start: {
                    required: "Start Date Required"
                },

                dis_end: {
                    required: "End Date Required"
                },

                dis_price: {
                    required: "Discount Value Required",
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
                            submitDiscount();
                        }
                });
            }
        });
        
        discount_list = $('#discount-list').DataTable({
             processing: true,
             serverSide: true,
             responsive: true,
             ajax: "{{route('discount.list.ajax')}}",
             columns: [
                 { data: 'pid' },
                 { data: 'rule' },
                 { data: 'start_date' },
                 { data: 'end_date' },
                 { data: 'dis_type' },
                 { data: 'dis_map' },
                 { data: 'dis_value', className: "text-right" },
                 { data: 'created_at' },
                 { data: 'id',className: 'text-center' , orderable: false },
             ],
             order: [[0, 'desc']]
         });
    });

    function isMaxCheckDiscount() {
        if($("#dis_type option:selected").val() == "percentage"){
            return 100;
        }
    }

    function realodDiscountList(){
        discount_list.ajax.reload();
    }

    function editFormValidation(){
        $("#"+discount_edit_form).validate({
            rules: {
                dis_code: {
                    required: true,
                    minlength : 2,
                },
                dis_start: {
                    required: true,
                },
                dis_end: {
                    required: true,
                },
                dis_price: {
                    required: true,
                    number: true,
                    max: isMaxCheckDiscount
                },
            },
            messages: {
                dis_code: {
                    required: "Rule Required",
                    minlength: "Enter at least {0} characters"
                },

                dis_start: {
                    required: "Start Date Required"
                },

                dis_end: {
                    required: "End Date Required"
                },

                dis_price: {
                    required: "Discount Value Required",
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
                            submitDiscountUpdate();
                        }
                    });
            }
        });
    }

    function submitDiscount(){
        $("#btnDiscountnAdd").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/discount-store-ajax",
            data: $('#'+discount_add_form).serialize(),
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' ,  response.title , 2000);
                    $('#'+discount_add_form)[0].reset();
                    realodDiscountList();
                }else{
                    showMessage('error' ,  response.title , 4000);
                }
                $("#btnDiscountnAdd").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnDiscountnAdd").LoadingOverlay("hide");
            }
        });
    }

    function deleteDiscount(pid){
        $("#btnDiscountDelete").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/discount-delete-ajax",
            data: {
                pid : pid
            },
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    realodDiscountList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnDiscountDelete").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnDiscountDelete").LoadingOverlay("hide");
            }
        });
    }
    
    function submitDiscountUpdate(){
        $("#btnDiscountEdit").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/discount-update-ajax",
            data: $('#'+discount_edit_form).serialize(),
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    $('#'+discount_edit_form)[0].reset();
                    realodDiscountList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnDiscountEdit").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnDiscountEdit").LoadingOverlay("hide");
            }
        });
    }

    function getDiscount(pid , objref){
            $(objref).LoadingOverlay("show");
            $.ajax({
                method: "GET",
                url: BASE_URL_ADMIN+"/get-discount",
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
