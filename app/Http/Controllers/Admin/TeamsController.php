<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests\CountryStoreRequest;
use App\Models\Categories;
use App\Models\Country;
use App\Models\CountryTranslation;
use App\Models\Language;
use App\Models\Teams;
use Illuminate\Contracts\Foundation\App\Modelslication;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Modelslication|Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $categories = Teams::with('category')->get();
            return Datatables::of($categories)
                ->addColumn('image', function($categories){
                    $image = $categories->image;
                    return '<img src="'. url($image).'" height="70px" />';
                })
                ->addColumn('category', function($categories){
                    $category_name = Categories::where('id',$categories->category_id)->first();
                    return $category_name->name;
                })
                ->addColumn('action', function($categories){
                    $edit_button = '<a href="' . route('admin.teams.edit', [$categories->id]) . '" class="btn btn-info btn-icon" data-toggle="tooltip" data-placement="top" title="' . config('languageString.edit') . '"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
//                    $delete_button = '<button data-id="' . $categories->id . '" class="delete-single btn btn-danger btn-icon" data-toggle="tooltip" data-placement="top" title="' . config('languageString.delete') . '"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
//                    $extra_button = '<a href="#" class="driver-requirement btn btn-icon btn-secondary" data-toggle="tooltip" data-placement="top" title="' . config('languageString.extra') . '"><i class="bx bx-bullseye font-size-16 align-middle"></i></a>';
                    return '<div class="btn-icon-list">' . $edit_button . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.teams.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\Http\Response|View
     */
    public function create()
    {
        $categories = Categories::all();
        return view('admin.teams.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        $id = $request->input('edit_value');

        if($id == NULL){
            if ($request->hasFile('image')) {
                $mime = $request->image->getMimeType();
                $logo = $request->file('image');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/user/teams/', $logoName);
                $cat_img = 'assets/user/teams/' . $logoName;
            }
            Teams::create([
                'name'   => $request->name,
                'image'  => $cat_img,
                'category_id'  => $request->category_id,
            ]);
            return response()->json(['success' => true, 'message' => 'Team Added Successfully!']);
        } else{

            if ($request->hasFile('image')) {
                $mime = $request->image->getMimeType();
                $logo = $request->file('image');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/user/teams/', $logoName);
                $cat_img = 'assets/user/teams/' . $logoName;
            }

            Teams::where('id', $id)->update([
                'name'   => $request->name,
                'image'  => $cat_img,
                'category_id'  => $request->category_id,
            ]);

            return response()->json(['success' => true, 'message' => 'Team Updated Successfully!']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Modelslication|Factory|View
     */
    public function edit($id)
    {
        $team = Teams::find($id);
        $categories = Categories::all();
        if($team){
            return view('admin.teams.edit', ['team' => $team,'categories' => $categories]);
        } else{
            abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        Teams::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Team Deleted Successfully!']);
    }
}
