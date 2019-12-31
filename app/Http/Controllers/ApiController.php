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
        $values = Store::get($keys);
        $response = array();
        foreach ($values as $key => $value) {
            $response[$key] = $value['value'];
        }
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
        $new_values = array();
        $inputs = $request->all();
        foreach ($inputs as $key => $value) {
            if (array_key_exists($key, $values)) {
                array_push($errors, $key . ' already exists');
                continue;
            }
            $new_values[$key] = array(
                'value' => $value,
                'created_at' => $current_time
            );
        }
        if (!empty($errors)) {
            return response()->json($errors, 400);
        }
        
        $updated_data = array_merge($values, $new_values); //merge new values with prev values
        Store::save($updated_data);
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
        $inputs = $request->all();
        $current_input = array_map(function ($value) use ($current_time) {
            return ['value' => $value, 'created_at' => $current_time];
        }, $inputs);

        $pre_values = Store::get();
        $updated_data = array_merge($pre_values, $current_input); //merge array with prev values
        Store::save($updated_data);
        return response()->json($inputs, 200);
    }
}
