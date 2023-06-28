<div class="col-sm-12">
    <div class="card card-primary">
        <div class="card-header bg-info text-white">
            <h5 class="card-title">{{ __('Edit / Delete Discount') }}</h5>
        </div>

        <form autocomplte="off" id="discount-edit-form" method="POST" >
            @csrf
            <input type="hidden" value="{{$discount->id}}" id="pid" name="pid">
            <div class="card-body">
                <div class="form-group">
                    <label for="dis_code">Rule Name</label>
                    <input value="{{$discount->name}}" autocomplete="off" required type="text" class="form-control" id="dis_code" name="dis_code" placeholder="Enter Rule Name">
                </div>

                <div class="row mt-2">
                    <div class="col-sm-6">
                        <label for="dis_start">Start Date</label>
                        <input value="{{$discount->start_date}}" min="{{date('Y-m-d')}}" type="date" name="dis_start" id="dis_start" class="form-control" placeholder="Start Date">
                    </div>
                    <div class="col-sm-6">
                        <label for="dis_end">Start End</label>
                        <input value="{{$discount->end_date}}" min="{{date('Y-m-d')}}" type="date" name="dis_end" id="dis_end" class="form-control" placeholder="End Date">
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label for="dis_seq">Sequence No</label>
                    <input value="{{$discount->sequence}}" required type="text" class="form-control" id="dis_seq" name="dis_seq" placeholder="Enter Sequence No">
                </div>

                <div class="form-group mt-2">
                    <label for="dis_type">Discount Type</label>
                    <select name="dis_type" class="form-control" required id="dis_type">
                    <option value="">Choose Type</option>
                        <option {{$discount->discount_type == "percentage" ? 'selected' : ''}} value="percentage">Percentage</option>
                        <option {{$discount->discount_type == "amount" ? 'selected' : ''}} value="amount">Amount</option>
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label for="dis_map">Discount Map</label>
                    <select name="dis_map" class="form-control" required id="dis_map">
                    <option value="">Choose Type</option>
                        <option {{$discount->discount_map == "category_id" ? 'selected' : ''}} value="category_id">Category</option>
                        <option {{$discount->discount_map == "all" ? 'selected' : ''}} value="all">Order</option>
                    </select>
                </div>

                <div class="form-group mt-2 {{$discount->discount_map == 'category_id' ? '' : 'd-none'}}" id="catgory" >
                    <label for="dis_cat">Choose Category</label>
                    <select name="dis_cat" class="form-control" id="dis_cat">
                        <option value="">Choose Category</option>
                        @foreach ($categoryList as $categoryob)
                            <option {{$discount->map_category_id == $categoryob->id ? 'selected' : ''}} value="{{$categoryob->id}}">{{$categoryob->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label for="dis_buy_qty">Qty</label>
                    <input value="{{$discount->buy_qty}}" required type="text" class="form-control" id="dis_buy_qty" name="dis_buy_qty" placeholder="Qty">
                </div>

                <div class="form-group mt-2">
                    <label for="dis_price">Discount Value</label>
                    <input value="{{$discount->discount_value}}" required type="text" class="form-control" id="dis_price" name="dis_price" placeholder="Discount Value">
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" id="btnDiscountEdit" class="btn btn-success">Save changes</button>
                <button data-pid="{{$discount->id}}" type="button" id="btnDiscountDelete" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>