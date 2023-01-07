<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GetBusResource;
use App\Http\Resources\GetRideResource;
use App\Http\Resources\GetRidesResource;
use App\Http\Resources\GetTripResource;
use App\Models\AssignBuses;
use App\Models\Buses;
use App\Models\BusStations;
use App\Models\CustomerInvoices;
use App\Models\CustomerWallet;
use App\Models\Driver;
use App\Models\PassengerCurrentLocation;
use App\Models\RideBooking;
use App\Models\Routes;
use App\Utility\Utility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookARideController extends Controller
{
    /**
     * Display a listing of CurrentStations
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function getCurrentStations(Request $request){
        try{
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
                'numeric' => 'the_field_should_numeric',
                'gt' => 'the_field_should_greater_than_zero',
            ];
            // param validation
            $validator = Validator::make($request->all(), [
                'passenger' => 'required',
                'passenger.lat' => 'required|between:-90,90',
                'passenger.long' => 'required|between:-180,180',
            ], $messages);
            // validator is fail then return false
            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            $latitude = $request->passenger['lat'];
            $longitude = $request->passenger['long'];
            $country = 'Germany';
            if(isset($country) && $country != null) {
                // create  passenger current location
                PassengerCurrentLocation::updateOrCreate([
                    'pcl_passenger_id' => $user->id,
                ], [
                    'pcl_lat' => $latitude,
                    'pcl_long' => $longitude,
                    'pcl_country' => $country,
                    'pcl_current_date' => now(),
                    'pcl_current_time' => now(),
                ]);
            }

            $stations = BusStations::where(['station_active'=>1])->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( station_lat ) ) * cos( radians( station_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( station_lat ) ) ) ) AS distance'))
                ->having('distance', '<', 100)
                ->orderBy('distance')
                ->get();
            if(isset($stations) && count($stations) > 0) {
                $user_stations = array();
                foreach ($stations as $station) {
                    $selected_for_estimate_dis_time = Utility::timeAndDistance($station->station_lat, $station->station_long, $latitude, $longitude);
                    $distance = $selected_for_estimate_dis_time->routes[0]->legs[0]->distance->value/1000;
                    $time = $selected_for_estimate_dis_time->routes[0]->legs[0]->duration->value/60;

                    $station->station_distance = $distance;
                    $station->station_time = $time;
                    $user_stations[] = $station;
                }
                $stations = GetRideResource::collection($user_stations);
                Log::info('app.response', ['response' => $stations, 'statusCode' => 200,'success' => true]);
                return response()->json(['success' => true,'stations'=>$stations], 200);
            }
        else{
            $stations = [];
            Log::info('app.response', ['response' => $stations, 'statusCode' => 200,'success' => true]);
            return response()->json(['success' => true,'stations'=>$stations], 200);
        }

        }catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'get_current_stations','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    /**
     * Display a listing of DestinationStations
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function getDestinationStation(Request $request){
        try{
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
                'numeric' => 'the_field_should_numeric',
                'gt' => 'the_field_should_greater_than_zero',
            ];
            // param validation
            $validator = Validator::make($request->all(), [
                'destination' => 'required',
                'destination.lat' => 'required|between:-90,90',
                'destination.long' => 'required|between:-180,180',
            ], $messages);
            // validator is fail then return false
            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            $latitude = $request->destination['lat'];
            $longitude = $request->destination['long'];

            /*$country = 'Germany';
            if(isset($country) && $country != null) {
                // create  passenger current location
                PassengerCurrentLocation::updateOrCreate([
                    'pcl_passenger_id' => $user->id,
                ], [
                    'pcl_lat' => $latitude,
                    'pcl_long' => $longitude,
                    'pcl_country' => $country,
                    'pcl_current_date' => now(),
                    'pcl_current_time' => now(),
                ]);
            }*/

            $stations = BusStations::where(['station_active'=>1])->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( station_lat ) ) * cos( radians( station_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( station_lat ) ) ) ) AS distance'))
                ->having('distance', '<', 100)
                ->orderBy('distance')
                ->get();
            if(isset($stations) && count($stations) > 0) {
                $user_stations = array();
                foreach ($stations as $station) {
                    $selected_for_estimate_dis_time = Utility::timeAndDistance($station->station_lat, $station->station_long, $latitude, $longitude);
                    $distance = $selected_for_estimate_dis_time->routes[0]->legs[0]->distance->value/1000;
                    $time = $selected_for_estimate_dis_time->routes[0]->legs[0]->duration->value/60;

                    $station->station_distance = $distance;
                    $station->station_time = $time;
                    $user_stations[] = $station;
                }
                $stations = GetRideResource::collection($user_stations);
                Log::info('app.response', ['response' => $stations, 'statusCode' => 200,'success' => true]);
                return response()->json(['success' => true,'stations'=>$stations], 200);
            }
            else{
                $stations = [];
                Log::info('app.response', ['response' => $stations, 'statusCode' => 200,'success' => true]);
                return response()->json(['success' => true,'stations'=>$stations], 200);
            }

        }catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'get_destination_stations','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    /**
     * Display a listing of BookARide
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */

    public function getRide(Request $request){

        try{
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
                'numeric' => 'the_field_should_numeric',
                'gt' => 'the_field_should_greater_than_zero',
            ];
            // param validation
            $validator = Validator::make($request->all(), [
                'destination' => 'required',
                'destination.lat' => 'required|between:-90,90',
                'destination.long' => 'required|between:-180,180',
                'destination.distance' => 'required|numeric|gt:0',
                'destination.time' => 'required|numeric|gt:0',
                'passenger' => 'required',
                'passenger.lat' => 'required|between:-90,90',
                'passenger.long' => 'required|between:-180,180',
            ], $messages);
            // validator is fail then return false
            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }
            $destination = $request->destination;

            $desLat = $destination['lat'];
            $desLong = $destination['long'];
            $latitude = $request->passenger['lat'];
            $longitude = $request->passenger['long'];

//            $pickup_location = app('geocoder')->reverse($latitude,$longitude)->get()->first();

            $country = 'Germany';
            if(isset($country) && $country != null){
                // create  passenger current location
                PassengerCurrentLocation::updateOrCreate([
                    'pcl_passenger_id'   => $user->id,
                ],[
                    'pcl_lat' => $latitude,
                    'pcl_long' => $longitude,
                    'pcl_country' => $country,
                    'pcl_current_date' => now(),
                    'pcl_current_time' => now(),
                ]);
                $stations = BusStations::where(['station_active'=>1])->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$latitude.') ) * cos( radians( station_lat ) ) * cos( radians( station_long ) - radians('.$longitude.') ) + sin( radians('.$latitude.') ) * sin( radians( station_lat ) ) ) ) AS distance'))
                    ->having('distance', '<', 100)
                    ->orderBy('distance')
                    ->get();
                if(isset($stations) && count($stations) > 0) {
                    $user_stations = array();
                    foreach ($stations as $stat){

                        $stations = BusStations::where('id', $stat->id)
                            ->selectRaw(DB::raw('*, ( 6367 * acos( cos( radians('.$desLat.') ) * cos( radians( station_lat ) ) * cos( radians( station_long ) - radians('.$desLong.') ) + sin( radians('.$desLat.') ) * sin( radians( station_lat ) ) ) ) AS distance'))
                            ->having('distance', '<', 100)
                            ->first();


                        if (!empty($stations)){
                            $route = Routes::leftjoin('route_stations', 'route.id', '=', 'route_stations.route_id')
                                ->where('route_stations.station_id', $stations->id)
                                ->first();
                            $selected_for_estimate_dis_time = Utility::timeAndDistance($stations->station_lat, $stations->station_long, $latitude, $longitude);
                            $distance = $selected_for_estimate_dis_time->routes[0]->legs[0]->distance->value/1000;
                            $time = $selected_for_estimate_dis_time->routes[0]->legs[0]->duration->value/60;
                            if ($route){
                                $stat->route_id = $route->route_id;
                            }else{
                                $stat->route_id = 0;
                            }

                            $stat->station_distance = $distance;
                            $stat->station_time = $time;
                            $user_stations[] = $stat;
                        }else{
                            $stations = [];
                            Log::info('app.response', ['response' => $stations, 'statusCode' => 200,'success' => true]);
                            return response()->json(['success' => true,'stations'=>$stations], 200);
                        }
                    }

                    $stations = GetRideResource::collection($user_stations);
                    Log::info('app.response', ['response' => $stations, 'statusCode' => 200,'success' => true]);
                    return response()->json(['success' => true,'stations'=>$stations], 200);
                }else{
                    $stations = [];
                    Log::info('app.response', ['response' => $stations, 'statusCode' => 200,'success' => true]);
                    return response()->json(['success' => true,'stations'=>$stations], 200);
                }
            }else {
                $stations = [];
                Log::info('app.response', ['response' => $stations, 'statusCode' => 200]);
                return response()->json($stations, 200);
            }
        }catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'get_stations','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    public function getAllBuses(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
            ];
            $validator = Validator::make($request->all(), [
                'current_station_id' => 'required',
                'destination_station_id' => 'required',
            ], $messages);
            // validator is fail then return false
            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }

            $current_station = $request->current_station_id;
            $destination_station = $request->destination_station_id;
            $all_buses = array();

            $route = Routes::leftjoin('route_stations', 'route.id', '=', 'route_stations.route_id')
                ->where('route_stations.station_id',$current_station)
                ->first();
            if ($route){
                $routeNew = Routes::leftjoin('route_stations', 'route.id', '=', 'route_stations.route_id')
                    ->where(['route_stations.station_id' => $destination_station, 'route_stations.route_id' => $route->route_id])
                    ->first();
            }else{
                $all_buses = [];
                Log::info('app.response', ['response' => $all_buses, 'statusCode' => 200]);
                return response()->json(['success' => true, 'buses' => $all_buses, 'message' => 'No Buses Found For this route'], 200);
            }
            if ($routeNew){
                $assignBuses = AssignBuses::where(['route_id' => $routeNew->route_id])->get();
            }else{
                $all_buses = [];
                Log::info('app.response', ['response' => $all_buses, 'statusCode' => 200]);
                return response()->json(['success' => true, 'buses' => $all_buses, 'message' => 'No Buses Found For this route'], 200);
            }
            if(isset($assignBuses) && count($assignBuses) > 0) {
                foreach ($assignBuses as $assignBus) {
                    $bus = Buses::where('id', $assignBus->bus_id)->first();
                    $driver = Driver::where('id', $assignBus->driver_id)->first();
                    $assignBus->bus = $bus;
                    $assignBus->driver = $driver;
                    $assignBus->station_id = $request->current_station_id;
                    $all_buses[] = $assignBus;
                }
                $all_buses = GetBusResource::collection($all_buses);
                Log::info('app.response', ['response' => $all_buses, 'statusCode' => 200]);
                return response()->json(['success' => true, 'buses' => $all_buses], 200);
            }else{
                $all_buses = [];
                Log::info('app.response', ['response' => $all_buses, 'statusCode' => 200]);
                return response()->json(['success' => true, 'buses' => $all_buses, 'message' => 'No Buses Found For this route'], 200);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'get_a_bus','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
            }
    }

    public function BookARide(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
            ];
            $validator = Validator::make($request->all(), [
                'route_id' => 'required',
                'bus_id' => 'required',
                'station_id' => 'required',
                'number_of_seats' => 'required',
            ], $messages);
            // validator is fail then return false
            if ($validator->fails()) {
                $errors = $validator->messages();
                return response()->json(compact('errors'), 401);
            }
            $bus = Buses::where('id', $request->bus_id)->first();
            $crrSeats = $bus->current_seats;
            $totalSeats = $crrSeats + $request->number_of_seats;

            if ($bus->total_seats > $totalSeats && $bus->total_seats != $totalSeats)
            {
                $total = $crrSeats + $request->number_of_seats;
                Buses::where('id',$request->bus_id)->update([
                    'current_seats' => $total
                ]);
                $selectedRoute = AssignBuses::where(['route_id' => $request->route_id,'bus_id' => $request->bus_id])->first();
                $totalFare = $bus->per_seat_charge * $request->number_of_seats;

                $ride = RideBooking::create([
                    'passenger_id' => $user->id,
                    'route_id' => $request->route_id,
                    'bus_id' => $request->bus_id,
                    'station_id' => $request->station_id,
                    'number_of_seats' => $request->number_of_seats,
                    'start_time' => $selectedRoute->start_time,
                    'end_time' => $selectedRoute->end_time,
                    'total_fare' =>$totalFare,
                    'payment_status' =>'Pending',
                    'ride_status' =>'inProgress',
                ]);

                $bus = Buses::where('id', $ride->bus_id)->first();
                $route = AssignBuses::where('id',$ride->route_id)->first();
                $driver = Driver::where('id', $route->driver_id)->first();
                $station = BusStations::where('id', $request->station_id)->first();

                $route_station = Routes::with(['routetations'=> function($query) {
                    $query->orderBy('id', 'desc')->first();
                }])->where('id', $ride->route_id)->get();
                foreach ($route_station as $stat){
                    $destination_station = BusStations::where('id', $stat->routetations[0]->station_id)->first();
                    $ride->destination_station = $destination_station;
                }

                $ride->bus = $bus;
                $ride->driver = $driver;
                $ride->station = $station;

                $rideDetails = new GetTripResource($ride);
                return response()->json(['success' => true, 'trip' => $rideDetails], 200);
            }else{
                return response()->json(['success' => true, 'message' => 'Seats are not available'], 200);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'book_a_ride','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    public function getAllRides(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);

            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $rides = RideBooking::where(['passenger_id' => $user->id, 'ride_status' => $request->status])->get();
            $allRides = GetRidesResource::collection($rides);
            return response()->json(['success' => true, 'rides' => $allRides], 200);
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'all_rides','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    public function getRideDetail(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $ride = RideBooking::where(['passenger_id' => $user->id, 'id' => $request->ride_id])->first();
            if (!empty($ride)){
                $rideDetail = new GetRidesResource($ride);
                return response()->json(['success' => true, 'ride' => $rideDetail], 200);
            }else{
                $rideDetail = 'No Ride Found';
                return response()->json(['success' => true, 'ride' => $rideDetail], 200);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'ride_detail','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    public function CompleteRide(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            if ($request->payment_status == 'Paid')
            {
                RideBooking::where(['passenger_id' => $user->id, 'id' => $request->ride_id, 'bus_id' => $request->bus_id, 'ride_status' => 'inProgress'])->update([
                    'payment_status' => $request->payment_status,
                    'ride_status' => 'Completed'
                ]);
                $ride = RideBooking::with('passenger','busDetail','stationDetail')->where(['passenger_id' => $user->id, 'id' => $request->ride_id])->first();
                if (!empty($ride)){
                    CustomerInvoices::create([
                        'user_id' => $ride->passenger_id,
                        'station_id' => $ride->station_id,
                        'bus_id' => $ride->bus_id,
                        'ride_id' => $ride->id,
                        'ride_amount' => $ride->total_fare,
                        'per_seat_amount' => $ride->busDetail->per_seat_charge,
                        'payment_status' => $ride->payment_status,
                        'ride_status' => $ride->ride_status,
                        'number_of_seats' => $ride->number_of_seats,
                    ]);

                    //update wallet
                        $wallet = CustomerWallet::where('user_id', $user->id)->first();
                        if ($wallet){
                            $myCurrentBalance = $wallet->current_balance;
                        }else{
                            $myCurrentBalance = 0;
                        }

                        $crrBalance = $myCurrentBalance - $ride->total_fare;
                        CustomerWallet::updateOrCreate([
                            'user_id' => $user->id
                        ],
                        [
                            'current_balance' => $crrBalance
                        ]);
                    //end
                    return response()->json(['success' => true, 'payment_success' => 'Payment Succeed. Invoice sent to email'], 200);
                }else{
                    return response()->json(['success' => false, 'ride_issue' => 'No Ride Found'], 200);
                }
            }else{
                return response()->json(['success' => true, 'payment_success' => 'Payment Not Succeed. Check your account balance'], 200);
            }
        }
        catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'ride_complete','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    public function getAllStationsData(Request $request){
        try {
            $token = JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url()]);

            $stations = BusStations::all();

            return response()->json(['success' => true, 'stations' => $stations], 200);
        }
            catch(\Exception $e){
            $message = $e->getMessage();
            $error = ['field'=>'ride_complete','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
}
