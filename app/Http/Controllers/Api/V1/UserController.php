<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\SentGiftsResource;
use App\Models\BuyGift;
use App\Models\CustomerInvoices;
use App\Models\GiftsNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function loginUser(Request $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'email' => 'required',
                'password' => 'required',
            ];
            $validator = Validator::make($request->all(), $messages);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->first()
                ], 401);
            }

            $user = User::where(['email'=>$request->email])->first();
            if (!$user) {
                return response()->json(['success'=>false, 'message' => 'Login Fail, please check email']);
            }
            $password = $request->password;

            if(!Hash::check($password, $user->password)) {
                return response()->json(['success'=>false, 'message' => 'Login Fail, please check password']);
            }

            $player = User::where(['id'=> $user->id,'status'=> 'InActive', 'user_type' => 'player'])->first();
            if ($player){
                return response()->json(['success'=>false, 'message' => 'Login Fail, Player Account is not active']);
            }

            $token=JWTAuth::fromUser($user);

            User::where('id',$user->id)->update([
                'user_JWT_Auth_Token'=>$token,
                'device_type'=>$request->device_type,
                'device_token'=>$request->device_token,
            ]);

            $user_data = User::getuser($user->id);

            return response()->json([
                'success' => true,
                'new_user' => false,
                'user' => $user_data,
                'token' => $token
            ], 200);

        }catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'driver_login','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $validator = Validator::make($request->all(), [
                'full_name' => 'required',
                'email' => 'required|unique:users',
                'user_type' => 'required',
                'password' => 'required',
//                'mobile_no' => 'required',
                'device_type' => 'required',
                'device_token' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            if(User::where(['email'=>$request->email])->exists()) {

                $message =  "User already created";
                $error = ['field'=>'user_exist','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);

            }else {
                $user = new User();
                $user->name = $request->full_name;
                if ($request->mobile_no) {
                    $user->mobile_no = $request->mobile_no;
                }
                if ($request->email) {
                    $user->email = $request->email;
                }

                $user->user_type = $request->user_type;
                $user->password = bcrypt($request->password);
                $user->status = 'InActive';
                $user->device_type =  $request->device_type;
                $user->device_token =  $request->device_token;

                if ($request->user_type == 'player'){
                    $user->category_id = $request->team_id;
                }
                $user->save();

//                $name = $user->name;
//                $id = $user->id;
//                if ($user->email){
//                    Mail::to($request->email)->send(new WelcomeEmail($name,$id));
//                }

                if(isset($user->user_JWT_Auth_Token) && $user->user_JWT_Auth_Token != null) {
                    $newToken = JWTAuth::manager()->invalidate(new \Tymon\JWTAuth\Token($user->user_JWT_Auth_Token), $forceForever = false);
                }

                $token=JWTAuth::fromUser($user);
                User::where('id',$user->id)->update(['user_JWT_Auth_Token'=>$token]);

                $user_data = User::getuser($user->id);

                return response()->json([
                    'token' => $token,
                    'user' => $user_data,
                ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                    'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
            }

        }catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'user_not_created','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function profileImage(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $validator = Validator::make($request->all(), [
                'profile_pic' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            if (isset($request->profile_pic) && !empty($request->profile_pic) && $request->hasFile('profile_pic')) {
                if ($user->profile_pic != "assets/default/user.png") {
                    @unlink(public_path() . '/' . $user->profile_pic);
                }
                $mime = $request->profile_pic->getMimeType();
                $image = $request->file('profile_pic');
                $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() . '-' . $image_name;
                $image->move('./assets/user/profile_pic/', $ImageName);
                $path_image = 'assets/user/profile_pic/' . $ImageName;
                User::where('id', $user->id)->update(['profile_pic' => $path_image]);
            }
            $user_data = User::getuser($user->id);
            return response()->json([
                'user' => $user_data,
                'message' => 'Image Added Successfully!',
                'success' => true,
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


    public function userIDCard(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $validator = Validator::make($request->all(), [
                'id_card_front' => 'required',
                'id_card_back' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            if (isset($request->id_card_front) && !empty($request->id_card_front) && $request->hasFile('id_card_front')) {
                if ($user->id_card_front != "assets/default/user.png") {
                    @unlink(public_path() . '/' . $user->id_card_front);
                }
                $mime = $request->id_card_front->getMimeType();
                $image = $request->file('id_card_front');
                $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() . '-' . $image_name;
                $image->move('./assets/user/id_card_front/', $ImageName);
                $path_image = 'assets/user/id_card_front/' . $ImageName;
                User::where('id', $user->id)->update(['id_card_front' => $path_image]);
            }

            if (isset($request->id_card_back) && !empty($request->id_card_back) && $request->hasFile('id_card_back')) {
                if ($user->id_card_back != "assets/default/user.png") {
                    @unlink(public_path() . '/' . $user->id_card_back);
                }
                $mime = $request->id_card_back->getMimeType();
                $image = $request->file('id_card_back');
                $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() . '-' . $image_name;
                $image->move('./assets/user/id_card_back/', $ImageName);
                $path_image = 'assets/user/id_card_back/' . $ImageName;
                User::where('id', $user->id)->update(['id_card_back' => $path_image]);
            }
            $user_data = User::getuser($user->id);

            return response()->json([
                'user' => $user_data,
                'message' => 'ID Card Added Successfully!',
                'success' => true,
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

    /**
     * User edit Profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

    public function editProfile(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);

            if(isset($request->profile_pic) && !empty($request->profile_pic) && $request->hasFile('profile_pic')){
                if($user->profile_pic != "assets/default/user.png") {
                    @unlink(public_path() . '/' . $user->profile_pic);
                }
                $mime= $request->profile_pic->getMimeType();
                $image = $request->file('profile_pic');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .'-'.$image_name;
                $image->move('./assets/user/profile_pic/', $ImageName);
                $path_image = 'assets/user/profile_pic/'.$ImageName;
                $update = User::where('id', $user->id)->update(['profile_pic' => $path_image]);
            }

            if(isset($request->full_name) && !empty($request->full_name)){
                $update = User::where('id', $user->id)->update(['name' => $request->full_name]);
            }

            if(isset($request->mobile_no) && !empty($request->mobile_no)){

                if(User::where(['mobile_no'=>$request->mobile_no])->exists()){
                    $message = "Mobile Number Already exist";
                    $error = ['field'=>'profile_not_edit','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }else {
                    $update = User::where('id', $user->id)->update(['mobile_no' => $request->mobile_no, 'mobile_number_verified' => 0]);
                }

                if($update){
                    return response()->json([
                        'mobile_no'=> $request->mobile_no,
                    ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                        'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

                }else{
                    $message = "Profile not edited";
                    $error = ['field'=>'user_profile_not_edit','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }
            }
            if(isset($request->email) && !empty($request->email)) {
                if (User::where(['email' => $request->email])->exists()) {
                    $message = "Email already exist";
                    $error = ['field' => 'p_email_already_exist', 'message' => $message];
                    $errors = [$error];
                    return response()->json(['errors' => $errors], 401);
                } else {
                    $messages = [
                        'required' => 'the_field_is_required'
                    ];
                    $validator = Validator::make($request->all(), [
                        'email' => 'required',
                    ], $messages);
                    if ($validator->fails()) {
                        $errors = $validator->messages();
                        return response()->json(compact('errors'), 401);
                    }
                    $update = User::where('id', $user->id)->update(['email' => $request->email]);
                }
            }
            if(isset($request->description)){
                $update = User::where('id', $user->id)->update(['description' => $request->description]);
            }

            if(isset($request->address)){
                $update = User::where('id', $user->id)->update(['address' => $request->address]);
            }

            return response()->json([
                'user' => User::getuser($user->id)
            ], 200, ['Content-Type' => 'application/json; charset=UTF-8',
                'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);

        }catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function logout(Request $request)
    {
        Log::info('app.requests', ['request' => $request->all()]);
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            $message = "User logout successfully!";
            return response()->json(['message'=>$message ], 200);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function buyGift(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $validator = Validator::make($request->all(), [
                'player_id' => 'required',
                'gift_id' => 'required',
            ]);

            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            if ($request->payment_status == 'success'){

                BuyGift::create([
                    'player_id' => $request->player_id,
                    'gift_id' => $request->gift_id,
                    'user_id' => $user->id,
                    'gift_status' => 'sent',
                ]);

                CustomerInvoices::create([
                    'player_id' => $request->player_id,
                    'gift_id' => $request->gift_id,
                    'user_id' => $user->id,
                    'invoice_status' => 'sent',
                    'amount' => $request->total_amount_charged,
                ]);

                GiftsNotifications::create([
                    'user_id' => $request->player_id,
                    'sender_id' => $user->id,
                    'title' => 'Gift Sent By Fan',
                    'message' => 'A new gift sent by your fan. Redeem and Check!',
                ]);

                $message = "Gift successfully sent!";
                return response()->json(['success'=>true,'message'=>$message ], 200);
            }else{
                $message = "Gift Not sent!";
                return response()->json(['success'=>false,'message'=>$message ], 200);
            }

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function giftsSent(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $sentGifts = BuyGift::with('gifts', 'player')->where('user_id', $user->id)->get();
            $sentGifts = SentGiftsResource::collection($sentGifts);

            return response()->json(['giftsSent'=> $sentGifts ], 200);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function getPlayerGifts(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $sentGifts = BuyGift::with('gifts', 'player')->where(['user_id' => $user->id, 'player_id' => $request->id])->get();
            $sentGifts = SentGiftsResource::collection($sentGifts);

            return response()->json(['giftsSent'=> $sentGifts ], 200);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function getGiftsNotifications(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $notifications = GiftsNotifications::with('sender')->where('user_id', $user->id)->get();
            $notifications = NotificationResource::collection($notifications);
            return response()->json(['notifications'=> $notifications ], 200);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function postNotifications(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            GiftsNotifications::create([
                'user_id' => $request->reciever_id,
                'sender_id' => $request->sender_id,
                'title' =>  $request->title,
                'message' =>  $request->message,
            ]);

            $message = "Notification successfully sent!";
            return response()->json(['success'=>true,'message'=>$message ], 200);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    public function SearchPlayer(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $search_key = $request->search_key;
            $players = User::with('team')->where('name', 'LIKE', '%'. $search_key. '%')->where('user_type', 'player')->get();
            dd($players);
            $players = PlayerResource::collection($players);

            return response()->json([ 'players' => $players], 200);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $error = ['field'=>'error','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
