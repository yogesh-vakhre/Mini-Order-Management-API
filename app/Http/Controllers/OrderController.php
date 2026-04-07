<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiHelpers;
use App\Jobs\ProcessOrderJob;
use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use ApiHelpers; // Using the apiHelpers Trait

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get query string parameters
        $searchKeyword = $request->input('q', ''); // search keyword, default to empty string
        $pageIndex = $request->input('pageIndex', 0); // page index, default to 0
        $pageSize = $request->input('pageSize', 3); // page size, default to 3
        $sortBy = $request->input('sortBy', 'name'); // attribute to sort, default to 'name'
        $sortDirection = $request->input('sortDirection', 'ASC'); // sort direction, default to 'ASC'

        // Query Order
        $query = Order::query();
        $query->with('user');
        // Apply search keyword filter
        if ($searchKeyword) {
            $query->where('id', 'like', '%' . $searchKeyword . '%');
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);

        // Get total count of Healthcare Professional
        $totalCount = $query->count();

        // Apply pagination
        $query->skip($pageIndex * $pageSize)->take($pageSize);

        //$query = $query->where('user_id', auth()->id());

        // Fetch Order
        $order = $query->get();

        // Return response

        return $this->onSuccess(
            [
                'orders' => $order,
                'totalCount' => $totalCount,
                'pageIndex' => $pageIndex,
                'pageSize' => $pageSize,
                'sortBy' => $sortBy,
                'sortDirection' => $sortDirection,
            ],
            'Order All Retrieved'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required|numeric',
            'status' => 'required|in:processing,shipped,delivered,pending',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(404, 'Validation Error.', $errors);
        }

        // Return errors if user not found error occur.
        $product = Product::find($request->product_id);
        if (empty($product)) {
            return $this->onError(404, 'Product not found');
        }

        // Check stock
        if ($product->stock < $request->quantity || $product->stock == 0) {
            return $this->onError(404, "Insufficient stock for product {$product->name}");
        }

        // Reduce stock
        $product->update(['stock' =>  $product->stock - $request->quantity]);
        $total_price = $product->price * $request->quantity;

        $order = Order::create([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $total_price
        ]);

        // Queue order processing
        ProcessOrderJob::dispatch($order);

        // Queue email notification
        Mail::to($order->user->email)->queue(new OrderPlacedMail($order));
        return $this->onSuccess($order, 'Order placed successfully', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return $this->onSuccess($order, 'Order Retrieved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        return $this->onSuccess($order, 'Order Retrieved');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $data = $request->all();

        // Validate request data
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'quantity' => 'required|numeric',
            'status' => 'required|in:processing,shipped,delivered,pending',
        ]);
        // Return errors if validation error occur.
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->onError(400, 'Validation Error.', $errors);
        }

        // Update Order
        $order->update($data);

        return $this->onSuccess($order, 'Order Status Updated');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete(); // Delete the specific Order data
        return $this->onSuccess($order, 'Order Deleted');
    }
}
