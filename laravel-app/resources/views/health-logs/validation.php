<?php

return [
    'required' => 'Trường :attribute không được bỏ trống.',
    'string' => 'Trường :attribute phải là một chuỗi ký tự.',
    'email' => 'Trường :attribute phải là một địa chỉ email hợp lệ.',
    'date' => 'Trường :attribute không phải là một ngày hợp lệ.',
    'before_or_equal' => 'Trường :attribute phải là một ngày trước hoặc bằng :date.',
    'integer' => 'Trường :attribute phải là một số nguyên.',
    'numeric' => 'Trường :attribute phải là một số.',
    'confirmed' => 'Giá trị xác nhận của :attribute không khớp.',
    
    'max' => [
        'numeric' => 'Trường :attribute không được lớn hơn :max.',
        'file' => 'Trường :attribute không được lớn hơn :max kilobytes.',
        'string' => 'Trường :attribute không được dài hơn :max ký tự.',
        'array' => 'Trường :attribute không được có nhiều hơn :max phần tử.',
    ],
    'min' => [
        'numeric' => 'Trường :attribute tối thiểu phải là :min.',
        'file' => 'Trường :attribute tối thiểu phải là :min kilobytes.',
        'string' => 'Trường :attribute tối thiểu phải là :min ký tự.',
        'array' => 'Trường :attribute tối thiểu phải có :min phần tử.',
    ],

    // Việt hóa tên các trường dữ liệu ở đây
    // VD: "Trường nhịp tim tối thiểu phải là 40." thay vì "Trường heart_rate..."
    'attributes' => [
        'email' => 'địa chỉ email',
        'password' => 'mật khẩu',
        'name' => 'tên',
        'log_date' => 'ngày giờ ghi nhận',
        'heart_rate' => 'nhịp tim',
        'sleep_hours' => 'giấc ngủ',
        'water_intake' => 'lượng nước',
        'calories' => 'calo',
        'symptoms' => 'triệu chứng',
        'mood' => 'tâm trạng',
        'token' => 'mã xác nhận',
    ],
];