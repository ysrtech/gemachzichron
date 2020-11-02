<?php

namespace App\Http\Requests;

use App\Models\Membership;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMembershipRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type' => 'required|in:' . Membership::TYPE_MEMBERSHIP . ',' . Membership::TYPE_PEKUDON,
            'plan_type_id' => 'required_if:type,'.Membership::TYPE_MEMBERSHIP
        ];
    }

    public function messages()
    {
        return [
            'plan_type_id.required_if' => 'The plan type is required when membership type is Membership',
        ];
    }

    public function validated()
    {
        $attributes = parent::validated();

        if ($attributes['type'] == Membership::TYPE_PEKUDON) {
            $attributes['plan_type_id'] = null;
        }

        return $attributes;
    }
}
