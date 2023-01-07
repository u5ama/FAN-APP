<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\ReceivedGiftsResource;
use App\Models\BuyGift;
use App\Models\Categories;
use App\Models\CustomerInvoices;
use App\Models\Gifts;
use App\Models\GiftsNotifications;
use App\Models\Teams;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class PlayerController extends Controller
{

    public function categories(Request $request)
    {
        try {
//            $token = JWTAuth::getToken();
//            $user = JWTAuth::toUser($token);
//            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $categories = Categories::all();

            return response()->json([
                'schools' => $categories
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function allGifts(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $gifts = Gifts::all();

            return response()->json([
                'gifts' => $gifts
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function playerCategory(Request $request)
    {
        try {
//            $token = JWTAuth::getToken();
//            $user = JWTAuth::toUser($token);
//            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $category_id = $request->category_id;

            $teams = Teams::where('category_id', $category_id)->get();

            return response()->json([
                'teams' => $teams,
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function playerTeam(Request $request)
    {
        try {
//            $token = JWTAuth::getToken();
//            $user = JWTAuth::toUser($token);
//            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $category_id = $request->team_id;
//            $user = User::where('id', $user->id)->update([
//               'category_id' =>  $category_id
//            ]);

            return response()->json([
                'success' => true,
                'message' => 'Team Added Successfully!',
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function allPlayers(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $players = User::with('team')->where('user_type', 'player')->get();

            $players = PlayerResource::collection($players);

            return response()->json([
                'players' => $players
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function getPlayerByCategory(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $category_id = $request->team_id;

            $players = User::with('team')->where(['category_id' => $category_id])->get();

            $players = PlayerResource::collection($players);

            return response()->json([
                'players' => $players
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function getPlayerInfo(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $player_id = $request->player_id;

            $player = User::with('category')->where(['id' => $player_id])->first();

            $player = new PlayerResource($player);

            return response()->json([
                'player' => $player
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function giftsReceived(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $gifts = BuyGift::with('gifts', 'fan')->where('player_id', $user->id)->get();
            $gifts = ReceivedGiftsResource::collection($gifts);

            return response()->json([
                'gifts' => $gifts
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function redeemGift(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $validator = Validator::make($request->all(), [
                'fan_id' => 'required',
                'gift_id' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            if ($request->payment_status == 'success'){

                BuyGift::where(['gift_id'=> $request->gift_id, 'player_id' => $user->id])->update([
                    'gift_status' => 'redeem',
                ]);

                CustomerInvoices::create([
                    'player_id' => $user->id,
                    'gift_id' => $request->gift_id,
                    'user_id' => $request->fan_id,
                    'invoice_status' => 'redeem',
                    'amount' => $request->total_amount_charged,
                ]);

                GiftsNotifications::create([
                    'user_id' => $request->fan_id,
                    'sender_id' => $user->id,
                    'title' => 'Gift Received',
                    'message' => 'Gift sent is Received! Thanks',
                ]);

                $message = "Gift successfully redeemed!";
                return response()->json(['success'=>true,'message'=>$message ], 200);
            }else{
                $message = "Gift Not redeemed!";
                return response()->json(['success'=>false,'message'=>$message ], 200);
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
