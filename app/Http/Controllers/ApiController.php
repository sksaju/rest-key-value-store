<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    /**
     * filename of local storage
     */
    private $storename;

    /**
     * value expiration time in minutes
     */
    private $expiration_time;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->storename = 'db.json';
        $this->expiration_time = 5;
        $this->filter_storage();
    }

    /**
     * Display a listing of the values.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keys = $request->query('keys');
        $response = array();
        if (!empty($keys)) {
            $response = $this->get_values_by_keys($keys);
        } else {
            $values = $this->get_values();
            foreach ($values as $key => $value) {
                $response[$key] = $value['value'];
            }
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
        $values = $this->get_values();
        $current_input = $request->all();

        foreach ($current_input as $key => $value) {
            if (array_key_exists($key, $values)) {
                array_push($errors, $key . ' already exists');
                continue;
            }
            $current_input[$key] = array(
                'value' => $value,
                'created_at' => $current_time
            );
        }

        if (!empty($errors)) {
            return response()->json($errors, 400);
        }

        $updatedData = array_merge($values, $current_input); //merge array with prev values
        Storage::put($this->storename, json_encode($updatedData)); //update storage values
        return response()->json($current_input, 201);
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

        $pre_values = $this->get_values();
        $updatedData = array_merge($pre_values, $current_input); //merge array with prev values
        Storage::put($this->storename, json_encode($updatedData)); //update storage values
        return response()->json($current_input, 200);
    }

    /**
     * Filter the values in storage.
     *
     * @return array
     */
    public function get_values()
    {
        $values = Storage::get($this->storename);
        $values = json_decode($values, true);
        return $values;
    }


    /**
     * Filter the values in storage.
     *
     * @return array
     */
    public function get_values_by_keys($keys)
    {
        $response = array();
        $current_time = time();
        $values = $this->get_values();
        $keys_array = explode(',', $keys);
        foreach ($values as $key => $value) {
            if (in_array($key, $keys_array)) {
                $response[$key] = $value['value'];
                $values[$key]['created_at'] = $current_time;
            }
        }
        //Update storage values
        Storage::put($this->storename, json_encode($values));
        return $response;
    }


    /**
     * Filter the values in storage.
     *
     * @return void
     */
    private function filter_storage()
    {
        $filter_values = [];
        $storename = $this->storename;
        if (Storage::exists($storename)) {
            $values = $this->get_values();
            $current_time = time();
            $filter_values = array_filter($values, function ($value) use ($current_time) {
                $difference = ($current_time - $value['created_at']);
                $difference = $difference / 60; //difference in minutes
                return ($difference < $this->expiration_time);
            }, ARRAY_FILTER_USE_BOTH);
        }
        //Update storage values
        Storage::put($storename, json_encode($filter_values));
    }
}
