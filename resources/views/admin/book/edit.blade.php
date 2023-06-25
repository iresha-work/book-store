<div class="col-sm-12">
    <div class="card card-primary">
        <div class="card-header bg-info text-white">
            <h5 class="card-title">{{ __('Edit / Delete Book') }}</h5>
        </div>

        <form method="POST" id="book-edit-form" enctype="multipart/form-data">
        @csrf
        <input type="hidden" value="{{$book->id}}" id="pid" name="pid">
        <div class="card-body">
            <div class="form-group">
                <label for="book_name">Name</label>
                <input value="{{$book->name}}" required type="text" class="form-control" id="book_name" name="book_name" placeholder="Enter Name">
            </div>

            <div class="form-group mt-2">
                <label for="book_cat">Choose Category</label>
                <select required name="book_cat" class="form-control" id="book_cat">
                    <option value="">Choose Category</option>
                    @foreach ($categoryList as $categoryob)
                        <option {{$book->category_id  == $categoryob->id ? 'selected' : ''}} value="{{$categoryob->id}}">{{$categoryob->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mt-2">
                <label for="book_description">Description</label>
                <div id="ebook_description">{!!$book->description!!}</div>
                <input type="hidden" name="ebook_descriptionh" id="ebook_descriptionh">
            </div>

            <div class="form-group mt-2">
                <label for="book_price">Price</label>
                <input value="{{$book->price}}" required type="text" class="form-control" id="book_price" name="book_price" placeholder="Enter Price">
            </div>

            <div class="form-group mt-2">
                <label for="book_seq">Sequence No</label>
                <input value="{{$book->sequence}}" required type="text" class="form-control" id="book_seq" name="book_seq" placeholder="Enter Sequence No">
            </div>

            <div class="form-group mt-2">
                <label for="">Product Images</label>
                @if (!empty($book->images))
                    <div class="row">
                        @foreach (json_decode($book->images) as $keyim => $image)
                            <div class="col-sm-3" id="keyim_{{$keyim}}">
                                <img src="{{$url_base.'storage/app/public/'.$image}}" class="rounded m-2 img-thumbnail" alt="{{$image}}">
                                <p class="text-center"><a data-keyim="{{$keyim}}" data-pimage="{{$image}}" data-pid="{{$book->id}}" href="javascript:void(0)" title="Remove Image" class="text-danger imageremove-book">x</a></p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="form-group mt-2">
                <label for="book_images">Choose Product Images</label>
                <input accept="image/png, image/gif, image/jpeg"  multiple type="file" name ="book_images[]" class="form-control-file" id="book_images">
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Save changes</button>
            <button data-pid="{{$book->id}}" type="button" id="btnBookDelete" class="btn btn-danger">Delete</button>
        </div>
    </form>
    </div>
</div>