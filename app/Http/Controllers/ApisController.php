<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\CategoryKey;
use App\Models\Customers;
use App\Models\Designs;
use App\Models\LaborCategory;
use App\Models\Products;
use Illuminate\Http\Request;

class ApisController extends Controller
{

    protected function getRequestParam($key, $default = null) {
        return request()->input($key, $default);
    }
    public function index(string $action, string $encodedUserID, string $name, string $searchTerm)
    {

        // Decode the user ID
        $decodedUserId = base64_decode($encodedUserID);

        // Handle GET actions
        if ($action == "get") {

            // Fetch customer by ID
            if ($name == "customer-by-id") {
                $customerId = base64_decode($searchTerm);
                $customer = Customers::where('id', $customerId)->first();
                if ($customer) {
                    return response()->json([
                        'id' => $customer->id,
                        'name' => $customer->name,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Customer not found.'
                    ], 404);
                }
            }

            // Fetch category by ID
            else if ($name == "category-by-id") {
                $categoryId = base64_decode($searchTerm);
                $category = Categories::where('id', $categoryId)
                    ->first();
                if ($category) {
                    return response()->json([
                        'id' => $category->id,
                        'name' => $category->name,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Category not found.'
                    ], 404);
                }
            }

            // Fetch subcategory by ID
            else if ($name == "subcategory-by-id") {
                $subcategoryId = base64_decode($searchTerm);
                $subcategory = Categories::where('id', $subcategoryId)
                    ->whereNotNull('parent_id')
                    ->first();
                if ($subcategory) {
                    return response()->json([
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'parent_id' => $subcategory->parent_id,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Subcategory not found.'
                    ], 404);
                }
            }

            // Fetch category key by ID
            else if ($name == "categorykey-by-id") {
                $categoryKeyId = base64_decode($searchTerm);
                $categoryKey = CategoryKey::where('id', $categoryKeyId)
                    ->first();
                if ($categoryKey) {
                    return response()->json([
                        'id' => $categoryKey->id,
                        'key' => $categoryKey->key,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Category key not found.'
                    ], 404);
                }
            }

            // Fetch design by ID
            else if ($name == "design-by-id") {
                $designId = base64_decode($searchTerm);
                $design = Designs::where('id', $designId)->first();
                if ($design) {
                    return response()->json([
                        'id' => $design->id,
                        'name' => $design->name,
                        'category_id' => $design->category_id,
                        'category_key_id' => $design->category_key_id,
                        'image_url' => $design->image_url,
                    ]);
                } else {
                    return response()->json([
                        'error' => 'Design not found.'
                    ], 404);
                }
            }
        }

        // Default response for unsupported actions or missing data
        return response()->json([]);
    }

    public function search(string $encodedUserID, string $name, string $searchTerm)
    {
        $returnData = [];

        $decodedUserId = base64_decode($encodedUserID);

        switch ($name) {
            
            // Search customers by various fields
            case 'customers':
                $returnData = Customers::where('user_id', $decodedUserId)
                    ->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('phone', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('address', 'LIKE', '%' . $searchTerm . '%');
                    })
                    ->get();
                break;

            // Search top-level categories by name
            case 'categories':
                if($searchTerm == "-"){
                    $returnData = Categories::whereNull('parent_id')->get();
                }else{
                    $returnData = Categories::whereNull('parent_id')
                        ->where('name', 'LIKE', '%' . $searchTerm . '%')
                        ->get();
                }
                break;

            // Search Labor categories by name
            case 'laborCategories':
                $returnData = LaborCategory::where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->get();
                break;

            // Search subcategories, possibly filtered by parent category
            case 'subcategories':
                $query = Categories::query();
                $categoryParam = request()->input('category');
                $categoryIDParam = request()->input('categoryID');
                if ($categoryParam) {
                    $parentCategory = Categories::where('name', 'LIKE', '%' . $categoryParam . '%')
                        ->whereNull('parent_id')
                        ->first();
                    if ($parentCategory) {
                        $query->where('parent_id', $parentCategory->id);
                    } else {
                        $query->whereNotNull('parent_id');
                    }
                } else if ($categoryIDParam) {
                    $query->where('parent_id', $categoryIDParam);
                } else {
                    $query->whereNotNull('parent_id');
                }
                if ($searchTerm != "-") {
                    $query->where('name', 'LIKE', '%' . $searchTerm . '%');
                }
                $returnData = $query->get();
                break;

            // Search category keys by key name
            case 'categorykey':
                $returnData = CategoryKey::where('key', 'LIKE', '%' . $searchTerm . '%')
                    ->get();
                break;

            // Search designs with multiple filters
            case 'designs':
                $categoryParam = $this->getRequestParam('category', '');
                $categoryIDParam = $this->getRequestParam('categoryID', 0);
                $subcategoryParam = $this->getRequestParam('subcategory', '');
                $subcategoryIDParam = $this->getRequestParam('subcategoryID', 0);
                $searchKeyParam = $this->getRequestParam('searchKey', '');

                // Ensure numeric parameters are cast to integers
                $categoryIDParam = intval($categoryIDParam);
                $subcategoryIDParam = intval($subcategoryIDParam);

                $designsQuery = Designs::query();
                $categoryKeys = CategoryKey::all();

                // Handle subcategory ID and filter category keys based on subcategory name
                $filteredCategoryKeys = [];
                if ($subcategoryIDParam) {
                    $subcategory = Categories::find($subcategoryIDParam);
                    if ($subcategory) {
                        $subcategoryName = $subcategory->name;
                        $filteredCategoryKeys = $categoryKeys->filter(function ($categoryKey) use ($subcategoryName) {
                            return stripos($subcategoryName, $categoryKey->key) !== false;
                        });
                    }
                } else {
                    $filteredCategoryKeys = $categoryKeys->filter(function ($categoryKey) use ($subcategoryParam) {
                        return stripos($subcategoryParam, $categoryKey->key) !== false;
                    });
                }

                // Get the first matching category key ID or null if none found
                $categoryKeyId = $filteredCategoryKeys->first()->id ?? null;    


                // Filter the designs query based on input parameters
                if (!$categoryKeyId) {
                    if ($categoryParam) {
                        // Filter by parent category
                        $category = Categories::where('name', 'LIKE', '%' . $categoryParam . '%')
                            ->whereNull('parent_id')
                            ->first();

                        if ($category) {
                            // Get all subcategory IDs under the parent category
                            $subcategories = Categories::where('parent_id', $category->id)->pluck('id');
                            $designsQuery->whereIn('category_id', $subcategories);
                        }
                    }

                    if ($subcategoryParam) {
                        // Filter by subcategory
                        $subcategory = Categories::where('name', 'LIKE', '%' . $subcategoryParam . '%')
                            ->whereNotNull('parent_id')
                            ->first();

                        if ($subcategory) {
                            $designsQuery->where('category_id', $subcategory->id);
                        }
                    }

                    if ($searchKeyParam !== 'undefined' && $searchKeyParam !== '') {
                        // Filter by design name
                        $designsQuery->where('name', 'LIKE', '%' . $searchKeyParam . '%');
                    }
                } else {
                    // Filter by category key ID
                    $designsQuery->where('category_key_id', $categoryKeyId);
                }

                // Retrieve data based on search term
                if ($searchTerm === 'all') {
                    $returnData = $designsQuery->get();
                } else {
                    $returnData = $designsQuery->find($searchTerm);
                }
                break;

            // Handle invalid search types
            default:
                return response()->json(['error' => 'Invalid search type'], 400);
        }

        // Return the search results as JSON
        return response()->json($returnData);
    }

}