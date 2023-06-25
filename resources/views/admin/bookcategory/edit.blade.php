<div class="col-sm-12">
    <div class="card card-primary">
        <div class="card-header bg-info text-white">
            <h5 class="card-title">{{ __('Edit / Delete Book Category') }}</h5>
        </div>

        <form autocomplte="off" id="category-edit-form" method="POST" >
            @csrf
            <input type="hidden" value="{{$bookCategory->id}}" id="pid" name="pid">
            <div class="card-body">
                <div class="form-group">
                    <label for="ect_name">Name</label>
                    <input value="{{$bookCategory->name}}" autocomplete="off" required type="text" class="form-control" id="ect_name" name="ect_name" placeholder="Enter Name">
                </div>

                <div class="form-group mt-2">
                    <label for="ect_seq">Sequence No</label>
                    <input value="{{$bookCategory->sequence}}" autocomplete="off" required type="text" class="form-control" id="ect_seq" name="ect_seq" placeholder="Enter Sequence No">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" id="btnCategoryEdit" class="btn btn-success">Save changes</button>
                <button data-pid="{{$bookCategory->id}}" type="button" id="btnCategoryDelete" class="btn btn-danger">Delete</button>
                
            </div>
        </form>
    </div>
</div>