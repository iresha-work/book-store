<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\BookController;

class DiscountController extends Controller
{
    /**
     *
     * @var string
     */
    protected $sucsess = "";

    /**
     *
     * @var string
     */
    protected $error = 500;

    /**
     *
     * @var string
     */
    protected $discountAddMsg = "Discount Rule saved.";

    /**
     *
     * @var string
     */
    protected $discountEditMsg = "Discount Rule updated.";

    /**
     *
     * @var string
     */
    protected $discountDelMsg = "Discount Rule deleted.";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoryList = (new BookController)->categoryList();
        return view('admin.discount.index')
        ->with([
            'categoryList' => $categoryList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $ar_search_cols = ["id", "name" , "start_date" ,"end_date" , "cupon_type" ,"discount_map","cupon_value" ,"created_at"];
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Discount::select('count(id) as allcount')->count();
        $totalRecordswithFilter = Discount::select('count(id) as allcount')->where(function($query) use ($ar_search_cols , $searchValue ) {
            if($searchValue != ''){
                foreach ($ar_search_cols as $col) {
                    $query->orWhere($col,'like', '%' .$searchValue . '%');
                }
            }
        })->count();

        // Fetch records
        $discounts = Discount::where(function($query) use ($ar_search_cols , $searchValue) {
        if($searchValue != ''){
            foreach ($ar_search_cols as $col) {
                $query->orWhere($col,'like', '%' .$searchValue . '%');
            }
        }
        })->select('*')
        ->skip($start)
        ->take($rowperpage)
        ->orderby($ar_search_cols[$columnIndex] , $columnSortOrder)
        ->get();

        $data_arr = array();
        foreach($discounts as $discount){
           $data_arr[] = array(

                "pid" => $discount->id,
                "dis_value" => $discount->discount_value,
                "id" => '<a data-pid="'.$discount->id.'" class="discount-edit text-center text-success" href="javascript:void(0)"><ion-icon name="eye-outline"></ion-icon></a>',
                "rule" => $discount->name,
                "start_date" => $discount->start_date,
                "end_date" => $discount->end_date,
                "dis_type" => $discount->discount_type,
                "dis_map" => $discount->discount_map,
                "created_at" => date('Y-m-d H:i:s', strtotime($discount->created_at))
           );
        }

        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        return response()->json($response); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dis_code' => ['required' ,'min:2' , 'unique:App\Models\Discount,name'],
            'dis_price' => ['required' ,'numeric'],
            'dis_type' => ['required'],
            'dis_start' => ['required' ],
            'dis_end' => ['required'],
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $discount = new Discount;
            $discount->name = $request->get('dis_code');
            $discount->start_date = $request->get('dis_start');
            $discount->end_date = $request->get('dis_end');
            $discount->discount_type = $request->get('dis_type');
            $discount->discount_value = $request->get('dis_price');
            $discount->discount_map = $request->get('dis_map');
            $discount->map_category_id = $request->get('dis_cat');
            $discount->created_at = date('Y-m-d H:i:s');
            $discount->save();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->discountAddMsg;
            return response()->json($response_ar);

        } catch (\Throwable $th) {

            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $discount = Discount::find($request->get('pid'));
        if(empty($discount)){
            abrot(404);
        }
        $categoryList = (new BookController)->categoryList();
        return view('admin.discount.edit')->with(
            [
                'discount' => $discount,
                'categoryList' => $categoryList
            ]
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dis_code' => ['required' ,'min:2' , Rule::unique('App\Models\Discount' , 'name')->ignore($request->get('pid'))],
            'dis_price' => ['required' ,'numeric'],
            'dis_type' => ['required'],
            'dis_start' => ['required' ],
            'dis_end' => ['required'],
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $discount = Discount::find($request->get('pid'));
            $discount->name = $request->get('dis_code');
            $discount->start_date = $request->get('dis_start');
            $discount->end_date = $request->get('dis_end');
            $discount->discount_type = $request->get('dis_type');
            $discount->discount_value = $request->get('dis_price');
            $discount->discount_map = $request->get('dis_map');
            $discount->map_category_id = $request->get('dis_cat');
            $discount->updated_at = date('Y-m-d H:i:s');
            $discount->save();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->discountEditMsg;
            return response()->json($response_ar);

        } catch (\Throwable $th) {

            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'pid' => ['required']
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $discount = Discount::find($request->get('pid'));
            $discount->delete();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->discountDelMsg;
            return response()->json($response_ar);

        } catch (\Throwable $th) {

            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'dis_code' => 'Coupon Code',
            'dis_start' => 'Start Date',
            'dis_end' => 'End Date',
            'dis_type' => 'Coupon Type',
            'dis_price' => 'Coupon Value',
        ];
    }
}
