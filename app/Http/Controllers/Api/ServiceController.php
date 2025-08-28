<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $type = $request->type;
            if($type == 'product'){
                $product = Product::with('partner','media')->get();
                return $this->formatResponse(200, 'success', 'Services list successfully', $product);
            }
            else{
            $services = Service::with('partner','media')->get();
            return $this->formatResponse(200, 'success', 'Services list successfully', $services);
            }
        } catch (Exception $e) {
            return $this->validationError(500, 'fail', 'Something went wrong', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'name' => 'required|string|max:255',
                'key_words' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'numeric|min:0',
                'sku' => 'string',
                'duration_minutes' => 'integer|min:1',
                'active' => 'boolean',
                'marketplace_id' => 'required',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
            if ($validator->fails())
                return  $this->validationError(422, 'fail', 'Validation errors', $validator->errors());
            $validateData = array_merge($validator->validated(), [
                'partner_id' => Auth::id(),
            ]);

            if ($request->type == 'service') {
                $data = Service::create($validateData);
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $data->addMedia($image)->toMediaCollection('images');
                    }
                }
            } else {
                $data = Product::create($validator->validated());
            }
            return $this->formatResponse(200, 'success', 'Service created successfully', $data->load('media'));
        } catch (Exception $e) {
            return $this->validationError(500, 'fail', 'Something went wrong', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        try {
            if ($request->type == 'service') {
                $service = Service::with('partner','media')->find($id);

                if (!$service) {
                    return $this->formatResponse(404, 'fail', 'Service not found', null);
                }
            }
            else{
                $service = Product::with('partner','media')->find($id);
                if (!$service) {
                    return $this->formatResponse(404, 'fail', 'Service not found', null);
                }

            }


            return $this->formatResponse(200, 'success', 'Service show successfully', $service);
        } catch (Exception $e) {
            return $this->validationError(500, 'fail', 'Something went wrong', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try {
            if($request->type = 'service'){
                $service = Service::find($id);
                if (!$service) {
                    return $this->formatResponse(404, 'fail', 'Service not found', null);
                }

                $service->update($request->all());
                if ($request->hasFile('images')) {
                    $service->clearMediaCollection('images');
                    $service->addMediaFromRequest('images')->toMediaCollection('images');
                }
                return $this->formatResponse(200, 'success', 'Service updated successfully', $service->load('media'));
            }
            else{
                $product = Product::find($id);
                if (!$product) {
                    return $this->formatResponse(404, 'fail', 'Service not found', null);
                }
                $product->update($request->all());
                if ($request->hasFile('images')) {
                    $product->clearMediaCollection('images');
                    $product->addMediaFromRequest('images')->toMediaCollection('images');
                }

                return $this->formatResponse(200, 'success', 'Service updated successfully', $product->load('media'));
            }


        } catch (Exception $e) {
            return $this->validationError(500, 'fail', 'Something went wrong', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            if($request->type=="service"){
                $service = Service::find($id);
                if (!$service) {
                    return $this->formatResponse(404, 'fail', 'Service not found', null);
                }
                $service->delete();
                $service->clearMediaCollection('images');
                return $this->formatResponse(200, 'success', 'Service deleted successfully', null);
            }
            else{
                $product = Product::find($id);
                if (!$product) {
                    return $this->formatResponse(404, 'fail', 'Product not found', null);
                }
                $product->delete();
                $product->clearMediaCollection('images');
                return $this->formatResponse(200, 'success', 'Product deleted successfully', null);
            }

        } catch (Exception $e) {
            return $this->validationError(500, 'fail', 'Something went wrong', $e->getMessage());
        }
    }
}
