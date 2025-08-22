<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
        $data = Service::with('partner')->get();
            return $this->formatResponse(200, 'success', 'Services list successfully', $data);
        }
        catch (Exception $e) {
            return $this->validationError(500,'fail','Something went wrong',$e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'partner_id' => 'required|exists:partners,id',
                'name' => 'required|string|max:255',
                'key_words' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'duration_minutes' => 'required|integer|min:1',
                'active' => 'boolean',
            ]);
            if ($validator->fails())
                return  $this->validationError(422,'fail','Validation errors',$validator->errors());
            $service = Service::create($validator->validated());
            return $this->formatResponse(200,'success','Service created successfully',$service);
        }
        catch (Exception $e) {
            return $this->validationError(500,'fail','Something went wrong',$e->getMessage());
        }




    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $service = Service::with('partner')->find($id);

            if (!$service) {
                return $this->formatResponse(404,'fail','Service not found',null);
            }

            return $this->formatResponse(200,'success','Service show successfully',$service);
        } catch (Exception $e) {
            return $this->validationError(500,'fail','Something went wrong',$e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return $this->formatResponse(404,'fail','Service not found',null);
            }

            $validator = Validator::make($request->all(), [
                'partner_id' => 'sometimes|exists:partners,id',
                'name' => 'sometimes|string|max:255',
                'key_words' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'duration_minutes' => 'sometimes|integer|min:1',
                'active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return  $this->validationError(422,'fail','Validation errors',$validator->errors());
            }

            $service->update($validator->validated());

            return $this->formatResponse(200,'success','Service updated successfully',$service);
        } catch (Exception $e) {
            return $this->validationError(500,'fail','Something went wrong',$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $service = Service::find($id);
            if (!$service) {
                return $this->formatResponse(404,'fail','Service not found',null);
            }
            $service->delete();

            return $this->formatResponse(200,'success','Service deleted successfully',null);
        } catch (Exception $e) {
            return $this->validationError(500,'fail','Something went wrong',$e->getMessage());
        }
    }
}
