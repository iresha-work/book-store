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
                                <h5 class="card-title">{{ __('Add Book Category') }}</h5>
                            </div>

                            <form autocomplte="off" id="category-add-form" method="POST" >
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="ct_name">Name</label>
                                        <input autocomplete="off" required type="text" class="form-control" id="ct_name" name="ct_name" placeholder="Enter Name">
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="ct_seq">Sequence No</label>
                                        <input autocomplete="off" required type="text" class="form-control" id="ct_seq" name="ct_seq" placeholder="Enter Sequence No">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" id="btnCategoryAdd" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-sm-7">
                        <div class="card card-primary">
                            <div class="card-header bg-primary text-white mb-2">
                                <h5 class="card-title">{{ __('List Book Category') }}</h5>
                            </div>
                            <table id="book-category-list" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Name</th>
                                        <th class="text-right">Sequence No</th>
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
    var category_add_form = "category-add-form";
    var category_edit_form = "category-edit-form";

    var category_list = "";
    $(document).ready(function () {
        jQuery.validator.addMethod("lettersonly", function(value, element) 
        {
        return this.optional(element) || /^[a-z ]+$/i.test(value);
        }, "Contains only letters");
        
        $(document).on("click","#btnreload",function() {
            category_list.ajax.reload();
        });
        
        $(document).on("click",".book-category-edit",function() {
            var pid = $(this).data('pid');
            if(pid != ''){
                getBookCategory(pid, $(this));
            }
        });

        $(document).on("click","#btnCategoryDelete",function() {
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
                            deleteBookCategory(pid);
                        }
                });
            }
        });

        $("#"+category_add_form).validate({
            rules: {
                ct_name: {
                    required: true,
                    minlength : 2,
                },
                ct_seq: {
                    required: true,
                    digits: true
                },
            },
            messages: {
                ct_name: {
                    required: "Name Required",
                    minlength: "Enter at least {0} characters"
                },
                ct_seq: {
                    required: "Sequence No Required",
                }
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
                            submitBookCategory();
                        }
                });
            }
        });
        
        category_list = $('#book-category-list').DataTable({
             processing: true,
             serverSide: true,
             responsive: true,
             ajax: "{{route('book.category.list.ajax')}}",
             columns: [
                 { data: 'pid' },
                 { data: 'name' },
                 { data: 'sequence', className: "text-right" },
                 { data: 'created_at' },
                 { data: 'id',className: 'text-center' , orderable: false },
             ],
             order: [[0, 'desc']]
         });
    });

    function realodCategoryList(){
        category_list.ajax.reload();
    }

    function editFormValidation(){
        $("#"+category_edit_form).validate({
            rules: {
                ect_name: {
                    required: true,
                    minlength : 2,
                },
                ect_seq: {
                    required: true,
                    digits: true
                },
            },
            messages: {
                ct_name: {
                    required: "Name Required",
                    minlength: "Enter at least {0} characters"
                },
                ct_seq: {
                    required: "Sequence No Required",
                }
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
                            submitBookCategoryUpdate();
                        }
                    });
            }
        });
    }

    function submitBookCategory(){
        $("#btnCategoryAdd").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/book-category-store-ajax",
            data: $('#'+category_add_form).serialize(),
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' ,  response.title , 2000);
                    $('#'+category_add_form)[0].reset();
                    realodCategoryList();
                }else{
                    showMessage('error' ,  response.title , 4000);
                }
                $("#btnCategoryAdd").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnCategoryAdd").LoadingOverlay("hide");
            }
        });
    }

    function deleteBookCategory(pid){
        $("#btnCategoryDelete").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/book-category-delete-ajax",
            data: {
                pid : pid
            },
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    realodCategoryList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnCategoryDelete").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnCategoryAdd").LoadingOverlay("hide");
            }
        });
    }
    
    function submitBookCategoryUpdate(){
        $("#btnCategoryEdit").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/book-category-update-ajax",
            data: $('#'+category_edit_form).serialize(),
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    $('#'+category_edit_form)[0].reset();
                    realodCategoryList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnCategoryEdit").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnCategoryEdit").LoadingOverlay("hide");
            }
        });
    }

    function getBookCategory(pid , objref){
            $(objref).LoadingOverlay("show");
            $.ajax({
                method: "GET",
                url: BASE_URL_ADMIN+"/get-book-category",
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
