<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Facades\App\Libraries\Store;

class ApiController extends Controller
{
    /**
     * Display a listing of the values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keys = $request->query('keys');
        $response = Store::get($keys);
        return response()->json($response, 200);
    }

    /**
     * Store a newly created values in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errors = array();
        $current_time = time();
        $values = Store::get();
        $inputs = $request->all();
        foreach ($inputs as $key => $value) {
            if (array_key_exists($key, $values)) {
                array_push($errors, $key . ' already exists');
                continue;
            }
            $inputs[$key] = array(
                'value' => $value,
                'created_at' => $current_time
            );
        }
        if (!empty($errors)) {
            return response()->json($errors, 400);
        }
        $updatedData = array_merge($values, $inputs); //merge array with prev values
        Store::save($updatedData);
        return response()->json($inputs, 201);
    }

    /**
     * Update the specified values in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $current_time = time();
        $current_input = array_map(function ($value) use ($current_time) {
            return ['value' => $value, 'created_at' => $current_time];
        }, $request->all());

        $pre_values = Store::get();
        $updatedData = array_merge($pre_values, $current_input); //merge array with prev values
        Store::save($updatedData);
        return response()->json($current_input, 200);
    }
}
