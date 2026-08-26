<?php
declare(strict_types=1);

namespace App\Modules\Events\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterEventAttendeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
        ];
    }
}
