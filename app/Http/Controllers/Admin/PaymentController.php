<?php

namespace App\Http\Controllers\Admin;


use App\Models\BuyGift;
use App\Models\CustomerInvoices;
use App\Models\Withdraw;
use Illuminate\Contracts\Foundation\App\Modelslication;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Modelslication|Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $gifts = CustomerInvoices::with('gifts', 'player','fan')->get();
            return Datatables::of($gifts)
                ->addColumn('gift_name', function($gifts){
                    return $gifts->gifts->name;
                    return '';
                })
                ->addColumn('fan', function($gifts){
                    return $gifts->fan->name;
                })
                ->addColumn('player', function($gifts){
                    return $gifts->player->name;
                })
                ->addColumn('gift_price', function($gifts){
                    return $gifts->gifts->price;
                })
                ->addColumn('gift_status', function($gifts){
                    $buyGift = BuyGift::where(['gift_id' => $gifts->gift_id, 'player_id' => $gifts->player_id, 'user_id' => $gifts->user_id])->first();
                    if ($buyGift){
                        $status = $buyGift->gift_status;
                    }else{
                        $status = '';
                    }
                    return $status;
                })
                ->addColumn('action', function($gifts){
//                    $edit_button = '<a href="' . route('admin.withdraw_requests.edit', [$engines->id]) . '" class="btn btn-info btn-icon" data-toggle="tooltip" data-placement="top" title="' . config('languageString.edit') . '"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $gifts->id . '" class="delete-single btn btn-danger btn-icon" data-toggle="tooltip" data-placement="top" title="' . config('languageString.delete') . '"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return '<div class="btn-icon-list"> ' . $delete_button . '</div>';
                })
                ->rawColumns(['action','gift_name','fan','player','gift_price','gift_status'])
                ->make(true);
        }
        return view('admin.payments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Modelslication|Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.payments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Modelslication|Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
       //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Withdraw::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Payment Removed Successfully']);
    }
}
