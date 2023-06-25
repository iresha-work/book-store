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
                                <h5 class="card-title">{{ __('Add Book') }}</h5>
                            </div>

                            <form method="POST" id="book-add-form" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="book_name">Name</label>
                                        <input required type="text" class="form-control" id="book_name" name="book_name" placeholder="Enter Name">
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="book_cat">Choose Category</label>
                                        <select required name="book_cat" class="form-control" id="book_cat">
                                            <option value="">Choose Category</option>
                                            @foreach ($categoryList as $categoryob)
                                                <option value="{{$categoryob->id}}">{{$categoryob->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    

                                    <div class="form-group mt-2">
                                        <label for="book_description">Description</label>
                                        <div id="book_description"></div>
                                        <input type="hidden" name="book_descriptionh" id="book_descriptionh">
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="book_price">Price</label>
                                        <input required type="text" class="form-control" id="book_price" name="book_price" placeholder="Enter Price">
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="book_seq">Sequence No</label>
                                        <input required type="text" class="form-control" id="book_seq" name="book_seq" placeholder="Enter Sequence No">
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="book_images">Choose Product Images</label>
                                        <input accept="image/png, image/gif, image/jpeg"  multiple type="file" name ="book_images[]" class="form-control-file" id="book_images">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-sm-7">
                        <div class="card card-primary">
                            <div class="card-header bg-primary text-white mb-2">
                                <h5 class="card-title">{{ __('List Book') }}</h5>
                            </div>
                            <table id="book-list" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th title="Sequence No">Seq.No</th>
                                        <th>Create At</th>
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
    var book_add_form = "book-add-form";
    var book_edit_form = "book-edit-form";

    var book_list = "";
    $(document).ready(function () {
        jQuery.validator.addMethod("lettersonly", function(value, element) 
        {
        return this.optional(element) || /^[a-z ]+$/i.test(value);
        }, "Contains only letters");
        
        $(document).on("click","#btnreload",function() {
            book_list.ajax.reload();
        });
        
        $(document).on("click",".book-edit",function() {
            var pid = $(this).data('pid');
            if(pid != ''){
                getBook(pid, $(this));
            }
        });

        $(document).on("click",".imageremove-book",function() {
            var pid = $(this).data('pid');
            var pimage = $(this).data('pimage');
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
                            removeBookImage(pid, pimage , $(this));
                        }
                });
            }
        });

        $('#book_description').summernote({
            placeholder: 'Enter Description',
            height: 100
        });

        $(document).on("click","#btnBookDelete",function() {
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
                            deleteBook(pid);
                        }
                });
            }
        });

        $("#"+book_add_form).validate({
            rules: {
                book_name: {
                    required: true,
                    minlength : 2,
                },
                book_seq: {
                    required: true,
                    digits: true
                },

                book_price: {
                    required: true,
                    number: true
                },

                book_cat: {
                    required: true
                }
            },
            messages: {
                book_name: {
                    required: "Name Required",
                    minlength: "Enter at least {0} characters"
                },
                book_seq: {
                    required: "Sequence No Required",
                },
                book_price: {
                    required: "Price Required",
                },
                book_cat: {
                    required: "Category Required",
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
                            submitBook();
                        }
                });
            }
        });
        
        book_list = $('#book-list').DataTable({
             processing: true,
             serverSide: true,
             responsive: true,
             ajax: "{{route('book.list.ajax')}}",
             columns: [
                 { data: 'pid' },
                 { data: 'name' },
                 { data: 'category' },
                 { data: 'price' ,  className: "text-right" },
                 { data: 'sequence', className: "text-right" },
                 { data: 'created_at' },
                 { data: 'id',className: 'text-center' , orderable: false },
             ],
             order: [[0, 'desc']]
         });
    });

    function realodBookList(){
        book_list.ajax.reload();
    }

    function editFormValidation(){
        $("#"+book_edit_form).validate({
            rules: {
                book_name: {
                    required: true,
                    minlength : 2,
                },
                book_seq: {
                    required: true,
                    digits: true
                },

                book_price: {
                    required: true,
                    number: true
                },

                book_cat: {
                    required: true
                }
            },
            messages: {
                book_name: {
                    required: "Name Required",
                    minlength: "Enter at least {0} characters"
                },
                book_seq: {
                    required: "Sequence No Required",
                },
                book_price: {
                    required: "Price Required",
                },
                book_cat: {
                    required: "Category Required",
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
                            submitBookUpdate();
                        }
                    });
            }
        });
    }

    function submitBook(){
        $("#btnBookAdd").LoadingOverlay("show");
        $('#book_descriptionh').val($('#book_description').summernote('code'));
        var formData = new FormData($('#'+book_add_form)[0]);
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/book-store-ajax",
            data: formData,
            cache: false,
            dataType : "json",
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status){
                    showMessage('success' ,  response.title , 2000);
                    $('#'+book_add_form)[0].reset();
                    realodBookList();
                }else{
                    showMessage('error' ,  response.title , 4000);
                }
                $("#btnBookAdd").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnBookAdd").LoadingOverlay("hide");
            }
        });
    }

    function removeBookImage(pid , pimage , objRef){
        $(objRef).LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/book-delete-image-ajax",
            data: {
                pid : pid,
                pimage : pimage
            },
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    var keyim = $(objRef).data('keyim');
                    $('#keyim_'+keyim).remove();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $(objRef).LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $(objRef).LoadingOverlay("hide");
            }
        });
    }

    function deleteBook(pid){
        $("#btnBookDelete").LoadingOverlay("show");
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/book-delete-ajax",
            data: {
                pid : pid
            },
            dataType: "json",
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    realodBookList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnBookDelete").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnBookDelete").LoadingOverlay("hide");
            }
        });
    }
    
    function submitBookUpdate(){
        $("#btnBookEdit").LoadingOverlay("show");
        $('#ebook_descriptionh').val($('#ebook_description').summernote('code'));
        var formData = new FormData($('#'+book_edit_form)[0]);
        $.ajax({
            method: "POST",
            url: BASE_URL_ADMIN+"/book-update-ajax",
            data: formData,
            cache: false,
            dataType : "json",
            contentType: false,
            processData: false,
            success: function(response){
                if(response.status){
                    showMessage('success' , response.title , 2000);
                    $('#'+book_edit_form)[0].reset();
                    realodBookList();
                    closeModal();
                }else{
                    showMessage('error' , response.title , 4000);
                }
                $("#btnBookEdit").LoadingOverlay("hide");
            },
            error: function(response){
                showMessage('error' , response.responseJSON.message , 4000);
                $("#btnBookEdit").LoadingOverlay("hide");
            }
        });
    }

    function getBook(pid , objref){
            $(objref).LoadingOverlay("show");
            $.ajax({
                method: "GET",
                url: BASE_URL_ADMIN+"/get-book",
                data: {
                    pid : pid
                },
                dataType: "html",
                success: function(response){
                    $('#commonModal').html(response);
                    $('#commonModalMain').modal('show');
                    $(objref).LoadingOverlay("hide");
                    editFormValidation();

                    $('#ebook_description').summernote({
                        placeholder: 'Enter Description',
                        height: 100
                    });
                },
                error: function(response){
                    $(objref).LoadingOverlay("hide");
                }
            });
        }
</script>
@endpush
