<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BookCategoryController extends Controller
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
    protected $categoryAddMsg = "Book category saved.";

    /**
     *
     * @var string
     */
    protected $categoryEditMsg = "Book category updated.";

    /**
     *
     * @var string
     */
    protected $categoryDelMsg = "Book category deleted.";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.bookcategory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $ar_search_cols = ["id", "name" , "sequence" ,"created_at"];
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
        $totalRecords = BookCategory::select('count(id) as allcount')->count();
        $totalRecordswithFilter = BookCategory::select('count(id) as allcount')->where(function($query) use ($ar_search_cols , $searchValue ) {
            if($searchValue != ''){
                foreach ($ar_search_cols as $col) {
                    $query->orWhere($col,'like', '%' .$searchValue . '%');
                }
            }
        })->count();

        // Fetch records
        $book_categories = BookCategory::where(function($query) use ($ar_search_cols , $searchValue) {
        if($searchValue != ''){
            foreach ($ar_search_cols as $col) {
                $query->orWhere($col,'like', '%' .$searchValue . '%');
            }
        }
        })->select($ar_search_cols)
        ->skip($start)
        ->take($rowperpage)
        ->orderby($ar_search_cols[$columnIndex] , $columnSortOrder)
        ->get();

        $data_arr = array();
        foreach($book_categories as $book_category){
           $data_arr[] = array(

                "pid" => $book_category->id,
                "id" => '<a data-pid="'.$book_category->id.'" class="book-category-edit text-center text-success" href="javascript:void(0)"><ion-icon name="eye-outline"></ion-icon></a>',
                "name" => $book_category->name,
                "sequence" => $book_category->sequence,
                "created_at" => date('Y-m-d H:i:s', strtotime($book_category->created_at))
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
            'ct_name' => ['required' ,'min:2' , 'unique:App\Models\BookCategory,name'],
            'ct_seq' => ['required' ,'numeric']
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $bookCategory = new BookCategory;
            $bookCategory->name = $request->get('ct_name');
            $bookCategory->sequence = $request->get('ct_seq');
            $bookCategory->created_at = date('Y-m-d H:i:s');
            $bookCategory->save();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->categoryAddMsg;
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
        $bookCategory = BookCategory::find($request->get('pid'));
        if(empty($bookCategory)){
            abrot(404);
        }
        return view('admin.bookcategory.edit')->with(
            [
                'bookCategory' => $bookCategory
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'ect_name' => ['required' ,'min:2' , Rule::unique('App\Models\BookCategory' , 'name')->ignore($request->get('pid'))],
            'ect_seq' => ['required' ,'numeric']
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $bookCategory = BookCategory::find($request->get('pid'));
            $bookCategory->name = $request->get('ect_name');
            $bookCategory->sequence = $request->get('ect_seq');
            $bookCategory->updated_at = date('Y-m-d H:i:s');
            $bookCategory->save();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->categoryEditMsg;
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
            $bookCategory = BookCategory::find($request->get('pid'));
            $bookCategory->delete();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->categoryDelMsg;
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
            'ct_name' => 'Name',
            'ct_seq' => 'Sequence No',
            'ect_name' => 'Name',
            'ect_seq' => 'Sequence No',
        ];
    }
}
