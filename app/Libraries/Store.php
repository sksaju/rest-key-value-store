<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;

class Store
{
    /**
     * filename of local storage
     */
    private $storename;

    /**
     * filename of local storage
     */
    private $storevalues;

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
        $this->set_storevalues();
    }

    /**
     * Get all value from store
     *
     * @param  string keys
     * @return array of data
     */
    public function get($keys=null)
    {
        $data = array();
        if (!empty($keys)) {
            $data = $this->value_by_keys($keys);
        } else {
            $values = $this->storevalues;
            foreach ($values as $key => $value) {
                $data[$key] = $value['value'];
            }
        }
        return $data;
    }

    /**
     * save values into store
     *
     * @param  json values
     * @return void
     */
    public function save($values)
    {
        $values = json_encode($values);
        Storage::put($this->storename, $values);
    }

    /**
     * Get specific values by keys and update created_at
     *
     * @return array of values
     */
    public function value_by_keys($keys)
    {
        $data = array();
        $current_time = time();
        $values = $this->storevalues;
        $keys_array = explode(',', $keys);
        foreach ($values as $key => $value) {
            if (in_array($key, $keys_array)) {
                $data[$key] = $value['value'];
                $values[$key]['created_at'] = $current_time;
            }
        }
        $this->save($values);
        return $data;
    }

    /**
     * Set storevalues propery
     *
     * @return void
     */
    public function set_storevalues()
    {
        $values = array();
        $storename = $this->storename;
        if (Storage::exists($storename)) {
            $values = Storage::get($storename);
            $values = json_decode($values, true);
        } else {
            Storage::put($storename, json_encode($values));
        }
        $this->storevalues = $values;
    }

    /**
     * Filter all values in storage.
     *
     * @return void
     */
    public function filter_values()
    {
        $current_time = time();
        $values = $this->storevalues;
        if (!empty($values)) {
            $filter_values = array_filter($values, function ($value) use ($current_time) {
                $difference = ($current_time - $value['created_at']);
                $difference = $difference / 60; //difference in minutes
                return ($difference < $this->expiration_time);
            }, ARRAY_FILTER_USE_BOTH);
            //Save the filtered values
            $this->save($filter_values);
        }
    }
}
