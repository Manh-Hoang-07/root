<?php

namespace App\Http\Requests\Public\Order;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:cod,bank_transfer,credit_card'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'payment_details' => ['nullable', 'array'],
            'payment_details.card_number' => ['required_if:payment_method,credit_card', 'string', 'max:20'],
            'payment_details.card_holder' => ['required_if:payment_method,credit_card', 'string', 'max:100'],
            'payment_details.expiry_date' => ['required_if:payment_method,credit_card', 'string', 'max:10'],
            'payment_details.cvv' => ['required_if:payment_method,credit_card', 'string', 'max:5'],
            'payment_details.bank_name' => ['required_if:payment_method,bank_transfer', 'string', 'max:100'],
            'payment_details.account_number' => ['required_if:payment_method,bank_transfer', 'string', 'max:50'],
            'payment_details.account_holder' => ['required_if:payment_method,bank_transfer', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Phương thức thanh toán là bắt buộc',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'transaction_id.max' => 'Mã giao dịch không được vượt quá 100 ký tự',
            'payment_details.card_number.required_if' => 'Số thẻ là bắt buộc khi thanh toán bằng thẻ tín dụng',
            'payment_details.card_holder.required_if' => 'Tên chủ thẻ là bắt buộc khi thanh toán bằng thẻ tín dụng',
            'payment_details.expiry_date.required_if' => 'Ngày hết hạn là bắt buộc khi thanh toán bằng thẻ tín dụng',
            'payment_details.cvv.required_if' => 'CVV là bắt buộc khi thanh toán bằng thẻ tín dụng',
            'payment_details.bank_name.required_if' => 'Tên ngân hàng là bắt buộc khi thanh toán chuyển khoản',
            'payment_details.account_number.required_if' => 'Số tài khoản là bắt buộc khi thanh toán chuyển khoản',
            'payment_details.account_holder.required_if' => 'Tên chủ tài khoản là bắt buộc khi thanh toán chuyển khoản',
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_method' => 'phương thức thanh toán',
            'transaction_id' => 'mã giao dịch',
            'payment_details' => 'chi tiết thanh toán',
            'payment_details.card_number' => 'số thẻ',
            'payment_details.card_holder' => 'tên chủ thẻ',
            'payment_details.expiry_date' => 'ngày hết hạn',
            'payment_details.cvv' => 'mã CVV',
            'payment_details.bank_name' => 'tên ngân hàng',
            'payment_details.account_number' => 'số tài khoản',
            'payment_details.account_holder' => 'tên chủ tài khoản',
        ];
    }
}