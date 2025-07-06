<?php

namespace App\Http\Requests\Attendance;

use App\Http\Requests\CoreRequest;

class StoreAttendance extends CoreRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $clockOutTime = $this->input('clock_out_time');
        $clockOutTimeWorkFromType = $this->input('clock_out_time_work_from_type');


        $rules = [
            'clock_in_time' => 'required',
            'clock_in_ip' => 'required|ip',
            'clock_out_ip' => 'ip',
            'working_from'  => 'required_if:work_from_type,==,other',
        ];

        if ($clockOutTime){

            $rules['clock_out_time_work_from_type'] = 'required';

            if($clockOutTimeWorkFromType == 'other') {

                $rules['clock_out_time_working_from'] = 'required';
            }
        }

        return $rules;

    }

}
