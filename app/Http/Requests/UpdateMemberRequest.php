<?php

namespace App\Http\Requests;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title'             => 'sometimes|nullable|string',
            'first_name'        => 'sometimes|required|string',
            'last_name'         => 'sometimes|required|string',
            'hebrew_first_name' => 'sometimes|required|string',
            'hebrew_last_name'  => 'sometimes|required|string',
            'wife_name'         => 'sometimes|nullable|string',
            'address'           => 'sometimes|nullable|string',
            'city'              => 'sometimes|nullable|string',
            'postal_code'       => 'sometimes|nullable|string',
            'email'             => 'sometimes|nullable|email',
            'home_phone'        => 'sometimes|nullable|string',
            'cell_phone'        => 'sometimes|nullable|string',
            'wife_cell_phone'   => 'sometimes|nullable|string',
            'shtibel'           => 'sometimes|nullable|string',
            'father'            => 'sometimes|nullable|string',
            'father_in_law'     => 'sometimes|nullable|string',
            'active_membership' => 'sometimes|boolean',
            'membership_type'   => 'sometimes|required|in:' . Member::TYPE_MEMBERSHIP . ',' . Member::TYPE_PEKUDON,
            'plan_type_id'      => 'sometimes|required_if:membership_type,' . Member::TYPE_MEMBERSHIP
        ];
    }
}
