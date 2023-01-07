<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ReportProblem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportProblemController extends Controller
{
    public function recommendSchool(Request $request)
    {
//        $user = JWTAuth::parseToken()->authenticate();
//        $user_id = $user->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'message' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $report_problem = new ReportProblem();
        $report_problem->name =$request->input('name');
        $report_problem->message = $request->input('message');

        $report_problem->save();


        return response()->json([
            'success' => true,
            'message' => 'Message Sent Successfully!',
        ],200);

    }

}
