<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CategoryController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    //* [GET] /main-category/get
    public function getMainCategory(Request $request)
    {
        try {

            $rules = [
                "main_category_id"   => ["required", "uuid"],
            ];

            $validator = Validator::make($request->all(), $rules);


            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => "Bad request",
                    "data" => [
                        [
                            "validator" => $validator->errors()
                        ]
                    ]
                ], 400);
            }

            $category = Category::select('*')->where('main_category_id', $request->main_category_id)->get();

            return response()->json([
                "status" => 'success',
                "message" => "Get main category by ID successfully",
                "data" =>  $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //* [GET] /main-category/get-all
    public function getAllMainCategory(Request $request)
    {
        try {

            $category = Category::select('*')->get();

            return response()->json([
                "status" => 'success',
                "message" => "Get All main category successfully",
                "data" =>  $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }


    //* [POST] /main-category/create-standalone
    public function createCategoryStandAlone(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    // "main_category_id"  => ["required", "string"],
                    "main_category_name"   => ["required", "string"],
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => "Bad request",
                    "data" => [
                        [
                            "validator" => $validator->errors()
                        ]
                    ]
                ], 400);
            }

            //? check category already exists
            $checkDataMain = Category::where('main_category_name', $request->main_category_name)->get();

            if (count($checkDataMain) !== 0) return response()->json([
                "status" => "error",
                "message" => "The category is already in system",
                "data" => [],
            ], 400);

            $category = Category::insert(
                [
                    "main_category_name"       => $request->main_category_name,
                ]
            );


            return response()->json([
                "status" => 'success',
                "message" => "Created stand alone category successfully",
                "data" =>  $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }


    //* [POST] /sub-category/create-leaf
    public function createSubCategory(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    "main_category_id"  => ["required", "string"],
                    "sub_category_name"   => ["required", "string"],
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    "status" => "error",
                    "message" => "Bad request",
                    "data" => [
                        [
                            "validator" => $validator->errors()
                        ]
                    ]
                ], 400);
            }

            $checkData = Category::where('main_category_id', $request->main_category_id)->get();

            if ($checkData->isEmpty()) {
                return response()->json([
                    "status" => "error",
                    "message" => "Main category not found",
                    "data" => [],
                ], 404);
            }

            $category = SubCategory::insert([
                "main_category_id"       => $request->main_category_id,
                "sub_category_name"     => json_encode($request->sub_category_name, JSON_UNESCAPED_UNICODE),

            ]);


            return response()->json([
                "status" => 'success',
                "message" => "Created sub category successfully",
                "data" =>  $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }


    //* [DELETE] /delete/category
    public function deleteCategory(Request $request)
    {
        try {
            $rules = [
                "category_id"      => ["required", "uuid"],
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) return response()->json([
                "status" => "error",
                "message" => "Bad request",
                "data" => [
                    ["validator" => $validator->errors()]
                ]
            ], 400);


            $checkDataMain = Category::where('main_category_id', $request->category_id)->get();
            $checkDataSub = SubCategory::where('main_category_id', $request->category_id)->get();

            $resultMain = DB::table("main_categories")->where("main_category_id", $request->category_id)->delete();

            $resultSub = DB::table("sub_categories")->where("main_categories", $request->category_id)->delete();

            if (count($checkDataSub)  === 0 || count($checkDataMain) === 0) return response()->json([
                "status" => "error",
                "message" => "category id does not exists",
                "data" => [],
            ], 400);



            return response()->json([
                "status" => "success",
                "message" => "Deleted category successfully",
                "data" => [],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status"    => "error",
                "message"   => $e->getMessage(),
                "data"      => [],
            ], 500);
        }
    }
}
