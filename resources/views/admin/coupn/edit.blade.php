<div class="col-sm-12">
    <div class="card card-primary">
        <div class="card-header bg-info text-white">
            <h5 class="card-title">{{ __('Edit / Delete Coupon') }}</h5>
        </div>

        <form autocomplte="off" id="coupn-edit-form" method="POST" >
            @csrf
            <input type="hidden" value="{{$coupon->id}}" id="pid" name="pid">
            <div class="card-body">
                <div class="form-group">
                    <label for="cp_code">Coupon Code</label>
                    <input value="{{$coupon->cupon_code}}" style="text-transform:uppercase" autocomplete="off" required type="text" class="form-control" id="cp_code" name="cp_code" placeholder="Enter Coupon Code">
                </div>

                <div class="row mt-2">
                    <div class="col-sm-6">
                        <label for="cp_start">Start Date</label>
                        <input value="{{$coupon->start_date}}" min="{{date('Y-m-d')}}" type="date" name="cp_start" id="cp_start" class="form-control" placeholder="Start Date">
                    </div>
                    <div class="col-sm-6">
                        <label for="cp_end">Start End</label>
                        <input value="{{$coupon->end_date}}" min="{{date('Y-m-d')}}" type="date" name="cp_end" id="cp_end" class="form-control" placeholder="End Date">
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label for="cp_type">Coupon Type</label>
                    <select name="cp_type" class="form-control" required id="cp_type">
                    <option value="">Choose Type</option>
                        <option {{$coupon->cupon_type == "percentage" ? 'selected' : ''}} value="percentage">Percentage</option>
                        <option {{$coupon->cupon_type == "amount" ? 'selected' : ''}} value="amount">Amount</option>
                    </select>
                </div>

                <div class="form-group mt-2">
                    <label for="cp_price">Coupon Value</label>
                    <input value="{{$coupon->cupon_value}}" required type="text" class="form-control" id="cp_price" name="cp_price" placeholder="Coupn Value">
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" id="btnCouponEdit" class="btn btn-success">Save changes</button>
                <button data-pid="{{$coupon->id}}" type="button" id="btnCouponDelete" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>