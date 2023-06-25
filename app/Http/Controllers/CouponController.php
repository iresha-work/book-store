<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CouponController extends Controller
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
    protected $coupnAddMsg = "Coupon saved.";

    /**
     *
     * @var string
     */
    protected $coupnEditMsg = "Coupon updated.";

    /**
     *
     * @var string
     */
    protected $coupnDelMsg = "Coupon deleted.";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.coupn.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $ar_search_cols = ["id", "cupon_code" , "start_date" ,"end_date" , "cupon_type" ,"cupon_value" ,"created_at"];
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
        $totalRecords = Coupon::select('count(id) as allcount')->count();
        $totalRecordswithFilter = Coupon::select('count(id) as allcount')->where(function($query) use ($ar_search_cols , $searchValue ) {
            if($searchValue != ''){
                foreach ($ar_search_cols as $col) {
                    $query->orWhere($col,'like', '%' .$searchValue . '%');
                }
            }
        })->count();

        // Fetch records
        $coupns = Coupon::where(function($query) use ($ar_search_cols , $searchValue) {
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
        foreach($coupns as $coupn){
           $data_arr[] = array(

                "pid" => $coupn->id,
                "cupon_value" => $coupn->cupon_value,
                "id" => '<a data-pid="'.$coupn->id.'" class="coupon-edit text-center text-success" href="javascript:void(0)"><ion-icon name="eye-outline"></ion-icon></a>',
                "cupon_code" => $coupn->cupon_code,
                "start_date" => $coupn->start_date,
                "end_date" => $coupn->end_date,
                "cupon_type" => $coupn->cupon_type,
                "created_at" => date('Y-m-d H:i:s', strtotime($coupn->created_at))
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
            'cp_code' => ['required' ,'min:2' , 'unique:App\Models\Coupon,cupon_code'],
            'cp_price' => ['required' ,'numeric'],
            'cp_type' => ['required'],
            'cp_start' => ['required' ],
            'cp_end' => ['required'],
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $coupn = new Coupon;
            $coupn->cupon_code = Str::upper($request->get('cp_code'));
            $coupn->start_date = $request->get('cp_start');
            $coupn->end_date = $request->get('cp_end');
            $coupn->cupon_type = $request->get('cp_type');
            $coupn->cupon_value = $request->get('cp_price');
            $coupn->created_at = date('Y-m-d H:i:s');
            $coupn->save();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->coupnAddMsg;
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
        $coupon = Coupon::find($request->get('pid'));
        if(empty($coupon)){
            abrot(404);
        }
        return view('admin.coupn.edit')->with(
            [
                'coupon' => $coupon
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'cp_code' => ['required' ,'min:2' , Rule::unique('App\Models\Coupon' , 'cupon_code')->ignore($request->get('pid'))],
            'cp_price' => ['required' ,'numeric'],
            'cp_type' => ['required'],
            'cp_start' => ['required' ],
            'cp_end' => ['required'],
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $coupn = Coupon::find($request->get('pid'));
            $coupn->cupon_code = Str::upper($request->get('cp_code'));
            $coupn->start_date = $request->get('cp_start');
            $coupn->end_date = $request->get('cp_end');
            $coupn->cupon_type = $request->get('cp_type');
            $coupn->cupon_value = $request->get('cp_price');
            $coupn->updated_at = date('Y-m-d H:i:s');
            $coupn->save();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->coupnEditMsg;
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
            $coupon = Coupon::find($request->get('pid'));
            $coupon->delete();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->coupnDelMsg;
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
            'cp_code' => 'Coupon Code',
            'cp_start' => 'Start Date',
            'cp_end' => 'End Date',
            'cp_type' => 'Coupon Type',
            'cp_price' => 'Coupon Value',
        ];
    }
}
