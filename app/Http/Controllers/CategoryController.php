<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\CategoryKey;
use App\Models\Log;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use stdClass;

class CategoryController extends Controller
{
    // Common method to get user data
    private function getUserData(string $menuTitle, $sectionName, string $title, object $pageData = new stdClass()): array
    {
        $user = User::find(Auth::id());
        $userName = $user ? $user->name : 'Guest';
        $userId = $user ? $user->id : 'Guest';
        $reminder = $user->reminders()
            ->where('is_completed', 0)
            ->orderBy('priority', 'asc')
            ->orderBy('reminder_time', 'asc')
            ->get();
       
        return [
            'title' => $title,
            'menuTitle' => $menuTitle,
            'sectionName' => $sectionName,
            'userName' => $userName,
            'userId' => $userId,
            "user" => $user,
            'pageData' => $pageData,
            'displayReminder' => $reminder
        ];
    }

    public function view(Request $request,string $role){
        if($role != 'admin' && $role != 'manager' ){
            abort(404); 
        }
        if (Auth::check() && Auth::user()->role != $role) {
            abort(403, 'Unauthorized action.');
        }
        
        $categorys = Categories::withTrashed()->whereNull('parent_id')->orderBy('parent_id')->get();

        $data = [
            'category' => $categorys,
            'role'=>$role
        ];

        $pageData = new stdClass();
        $pageData->category = $categorys;
        $pageData->role = $role;

        $data = $this->getUserData('Settings',null, 'Category', $pageData); 
        return view('common.category',$data);
    }


    public function createCategory(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'category' => 'required|string|unique:categories,name',
                'type' => 'required|string',
            ]);
            
    
            if ($validator->fails()) {
                if ($request->input('returnType') === 'json') {
                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()], 422);
                    }
                }
                return back()->withErrors($validator)->withInput();
            }

            // Create a new category instance
            $category = Categories::create([
                'name' => $request->category,
                'type' => $request->type,
            ]);

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Category created successfully',
                    'category' => $category,
                ], 200);
            }

            return redirect()->back()->with('message', 'Category created successfully');
        } catch (QueryException $e) {
            $this->logError($request, 'Failed to create category.', $e);

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create category: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to create category: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($request, 'Failed to create category.', $e);

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    public function createSubCategory(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'subcategory' => [
                    'required',
                    'string',
                    Rule::unique('categories', 'name')->where(function ($query) use ($request) {
                        return $query->where('parent_id', $request->category_id);
                    })
                ],
                'category_id' => 'required|numeric',
                'type' => 'required|string',
            ]);
            
            
    
            if ($validator->fails()) {
                if ($request->input('returnType') === 'json') {
                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()], 422);
                    }
                }
                return back()->withErrors($validator)->withInput();
            }

            // Create a new subcategory instance
            $subcategory = Categories::create([
                'name' => $request->subcategory,
                'parent_id' => $request->category_id,
                'type' => $request->type,
            ]);

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Subcategory created successfully',
                    'subcategory' => $subcategory,
                ], 200);
            }

            return redirect()->back()->with('message', 'Subcategory created successfully');
        } catch (QueryException $e) {
            $this->logError($request, 'Failed to create subcategory.', $e);

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create subcategory: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to create subcategory: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($request, 'Failed to create subcategory.', $e);

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function softDeleteCategory($encodedId)
    {
        try {
            $categoryId = base64_decode($encodedId);
            // Soft delete category
            $category = Categories::findOrFail($categoryId);
            $category->delete();

            return redirect()->back()->with('message', $category->name . ' soft deleted successfully');
        } catch (\Exception $e) {
            $this->logError(null, 'Failed to soft delete category.', $e);

            return redirect()->back()->with('error', 'Failed to soft delete category');
        }
    }

    public function restoreCategory($encodedId)
    {
        try {
            $categoryId = base64_decode($encodedId);
            // Restore soft deleted category
            $category = Categories::withTrashed()->findOrFail($categoryId);
            $category->restore();

            return redirect()->back()->with('message', $category->name . ' restored successfully');
        } catch (\Exception $e) {
            $this->logError(null, 'Failed to restore category.', $e);

            return redirect()->back()->with('error', 'Failed to restore category');

        }
    }

    public function getByID($encodedId){
        try {
            $categoryId = base64_decode($encodedId);
            $category = Categories::findOrFail($categoryId);
            return response()->json([
                'status' => 'success',
                'message' => 'Category retrieved successfully',
                'category' => $category,
                ], 200);
            } catch (\Exception $e) {
                $this->logError(null, 'Failed to retrieve category.', $e);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to get category: ' . $e->getMessage(),
                ], 500);
        }
    } 

    public function update($encodedId, Request $request)
    {
        try {
            // Decode the encoded ID to get the actual category ID
            $categoryId = base64_decode($encodedId);

            $category = Categories::findOrFail($categoryId);

            $request->validate([
                'category' => 'required|string',
                'type' => 'required|string',
                'parent_id' => 'nullable|numeric',
            ]);


            // Update the category 
            $category->name = $request->category;
            $category->type = $request->type;
            $category->parent_id = $request->parent_id;
            $category->save();

            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Category updated successfully',
                    'category' => $category,
                ], 200);
            }

            return redirect()->back()->with('message', 'Category updated successfully');
        } catch (\Exception $e) {

            $this->logError($request, 'Failed to Update category.', $e);
            if ($request->input('returnType') === 'json') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update category: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    private function logError($request, $message, $exception)
    {
        Log::create([
            'message' => $message,
            'level' => 'warning',
            'type' => 'error',
            'ip_address' => $request ? $request->ip() : null,
            'context' => 'web',
            'source' => 'category_controller',
            'extra_info' => json_encode([
                'user_agent' => $request ? $request->header('User-Agent') : null,
                'error' => $exception,
            ]),
        ]);
    }
}
