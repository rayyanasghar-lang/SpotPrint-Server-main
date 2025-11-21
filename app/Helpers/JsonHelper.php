<?php

namespace App\Helpers;

use Carbon\Carbon;


class JsonHelper
{
    public static function update_json_index($row, $column, $key, $value) // update_json_index
    {
        $json_array = $row->$column;
        $json_array[$key] = $value;
        $row->$column = $json_array;
        $row->save();
    }

    public static function append_log($row, $column, $log) // update_json_index
    {
        $user = auth()->user();
        $created_by = [
            'id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role,
        ];
        $created_at = Carbon::now()->utc()->format('Y-m-d H:i:s');


        $json_array = $row->$column;
        $json_array[] = ['log'=>$log, 'created_by'=>$created_by, 'created_at'=>$created_at];
        $row->$column = $json_array;
        $row->save();
    }
}
