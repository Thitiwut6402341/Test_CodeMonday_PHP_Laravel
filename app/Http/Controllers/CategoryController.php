<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    //* [POST] /category/create-standalone
    public function createCategoryStandAlone(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    "category_name"   => ["required", "string"],
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
            $checkDataMain = Category::where('category_name', $request->category_name)->get();

            if (count($checkDataMain) !== 0) return response()->json([
                "status" => "error",
                "message" => "The category is already in system",
                "data" => [],
            ], 400);

            $category = Category::create(
                [
                    "category_name"       => $request->category_name,
                ]
            );

            return response()->json([
                "status" => 'success',
                "message" => "Created stand alone category successfully",
                "data" =>  [$category],
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
                    "parent_id"  => ["required", "uuid"],
                    "category_name"   => ["required", "string"],
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

            $document = [
                "parent_id"         => $request->parent_id,
                "category_name"     => $request->category_name,
            ];

            $checkData = Category::where('category_id', $request->parent_id)->get();

            if (count($checkData) === 0) return response()->json([
                "status" => "error",
                "message" => "Category id does not found",
                "data" => [],
            ], 400); {
            }


            $category = Category::create($document);

            return response()->json([
                "status" => 'success',
                "message" => "Created sub category successfully",
                "data" =>  [$category],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //* [GET] /category/get-stand-alone
    public function getStanAloneCategory(Request $request)
    {
        try {

            $rules = [
                "category_id"   => ["required", "uuid"],
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

            $category = Category::select('*')->where('category_id', $request->category_id)->get();

            if (count($category) === 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "Category not found",
                    "data" => [],
                ], 404);
            }


            return response()->json([
                "status" => 'success',
                "message" => "Get category by ID successfully",
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

    //* [GET] /category/get-tree
    public function getTreeCategory(Request $request)
    {
        try {

            $rules = [
                "category_id"   => ["required", "uuid"],
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

            $category = Category::select('*')->where('category_id', $request->category_id)->get();

            if (count($category) === 0) {
                return response()->json([
                    "status" => "error",
                    "message" => "Category not found",
                    "data" => [],
                ], 404);
            }


            $tree = [
                'category_id' => $category[0]->category_id,
                'category_name' => $category[0]->category_name,
                'sub_category' => $category[0]->children->map(function ($child) {
                    return $this->buildTree($child);
                })->toArray()
            ];


            return response()->json([
                "status" => 'success',
                "message" => "Get main category by ID successfully",
                "data" =>  [$tree],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }

    //* [GET] /category/get-all
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

    //* [GET] /category/get-array
    public function getArrayCategory(Request $request)
    {
        try {

            $queryData = Category::select('category_id')->distinct()->get();

            $queyDataSub = Category::select('parent_id')->distinct()->whereNotNull('parent_id')->get();


            $dataCategory = array();
            foreach ($queryData as $doc) {
                $dataCategory[] = $doc->category_id;
            }

            $dataSubCategory = array();
            foreach ($queyDataSub as $doc) {
                $dataSubCategory[] = $doc->parent_id;
            }


            return response()->json([
                "status" => "success",
                "message" => "Get category array",
                "data" => [[

                    "category_id" => $dataCategory,
                    "parent_id" => $dataSubCategory,

                ]]
            ], 200);
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


            $checkData = Category::where('category_id', $request->category_id)->get();

            if (count($checkData) === 0) return response()->json([
                "status" => "error",
                "message" => "category id does not found",
                "data" => [],
            ], 400);

            $resultCategory = Category::where("category_id", $request->category_id)->delete();

            return response()->json([
                "status" => "success",
                "message" => "Deleted category successfully",
                "data" => $resultCategory,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status"    => "error",
                "message"   => $e->getMessage(),
                "data"      => [],
            ], 500);
        }
    }

    //? function for response sub_category in Main category
    private function buildTree($category)
    {
        $tree = [
            'category_id' => $category->category_id,
            'category_name' => $category->category_name,
            'sub_category' => $category->children->map(function ($child) {
                return $this->buildTree($child);
            })->toArray()
        ];
        return $tree;
    }


    //? function for create tree X node categories
    public function createDeepTreeCategories(Request $request)
    {
        try {

            $validator = Validator::make(
                $request->all(),
                [
                    "max_node"   => ["required", "integer", "min:1"],
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

            $maxNodes = $request->max_node;

            $rootCategory = Category::create([
                'category_name' => 'Root Category',
            ]);

            $checkCatID = Category::where('category_name', 'Root Category')->get();
            $previousCategoryId = $checkCatID[0]->category_id;


            for ($i = 0; $i < $maxNodes; $i++) {

                $category = Category::create([
                    'category_name' => 'Category Level ' . ($i + 1),
                    'parent_id' => $previousCategoryId
                ]);

                $checkID = Category::where('category_name', $category->category_name)->get();

                $previousCategoryId = $checkID[0]->category_id;
            }

            return response()->json([
                "status" => 'success',
                "message" => "Created nested categories successfully",
                "data" => $rootCategory,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage(),
                "data" => [],
            ], 500);
        }
    }
}
