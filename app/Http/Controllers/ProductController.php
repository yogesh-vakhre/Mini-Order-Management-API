<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Traits\ApiHelpers;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use ApiHelpers; // Using the apiHelpers Trait

    /**
     * Get all Product with their associated tasks and users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $result = Cache::remember('products.all', 3600, function () use ($request) {
            // Get query string parameters
            $searchKeyword = $request->input('q', ''); // search keyword, default to empty string
            $pageIndex = $request->input('pageIndex', 0); // page index, default to 0
            $pageSize = $request->input('pageSize', 3); // page size, default to 3
            $sortBy = $request->input('sortBy', 'name'); // attribute to sort, default to 'name'
            $sortDirection = $request->input('sortDirection', 'ASC'); // sort direction, default to 'ASC'

            // Query product
            $query = Product::query();

            // Apply search keyword filter
            if ($searchKeyword) {
                $query->where('id', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('name', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('price', 'like', '%' . $searchKeyword . '%')
                    ->orWhere('sku', 'like', '%' . $searchKeyword . '%');
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortDirection);

            // Get total count of product
            $totalCount = $query->count();

            // Apply pagination
            $query->skip($pageIndex * $pageSize)->take($pageSize);

            // Fetch product
            $product = $query->get();
            return [
                'product' => $product,
                'totalCount' => $totalCount,
                'pageIndex' => $pageIndex,
                'pageSize' => $pageSize,
                'sortBy' => $sortBy,
                'sortDirection' => $sortDirection,
            ];
        });
        // Return response

        return $this->onSuccess(
            $result,
            'Product All Retrieved'
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'stock' => 'required|integer'
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(404, 'Validation Error.', $errors);
        }

        $product = Product::create($request->all());

        return $this->onSuccess($product, 'Product Created', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $this->onSuccess($product, 'Product Retrieved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return $this->onSuccess($product, 'Product Retrieved');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->all();

        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'stock' => 'required|integer'
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(400, 'Validation Error.', $errors);
        }

        // Update Product
        $product->update($data);

        return $this->onSuccess($product, 'Product Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete(); // Delete the specific Product data
        return $this->onSuccess($product, 'Product Deleted');
    }
}
