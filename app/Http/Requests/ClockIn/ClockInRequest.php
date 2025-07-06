<?php
namespace App\Http\Requests\ClockIn;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Admin\Employee
 */
class ClockInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        // If admin
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
            'work_from_type'  => 'required',
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
