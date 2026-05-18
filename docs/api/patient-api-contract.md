# Patient API Contract

Tài liệu này mô tả contract giữa Laravel backend và ứng dụng Android bệnh nhân của hệ thống Smart Queue.

## Thông tin chung

- Base URL khi chạy Android Emulator: `http://10.0.2.2:8000/api/`
- Xác thực: `Authorization: Bearer <token>`
- Định dạng phản hồi chung:

```json
{
  "success": true,
  "message": "Thông báo ngắn",
  "data": {}
}
```

## Quy ước dữ liệu

### Trạng thái phiếu

| Giá trị | Ý nghĩa |
| --- | --- |
| `draft` | Nháp |
| `waiting_payment` | Chờ thanh toán |
| `ready` | Sẵn sàng gọi |
| `calling` | Đang gọi |
| `serving` | Đang khám |
| `missed` | Vắng mặt |
| `completed` | Hoàn thành |
| `cancelled` | Đã hủy |

### Lý do ưu tiên bệnh nhân có thể gửi

| Giá trị | Ý nghĩa |
| --- | --- |
| `normal` | Không thuộc diện ưu tiên |
| `emergency` | Người bệnh trong tình trạng cấp cứu |
| `child_under_6` | Trẻ em dưới 6 tuổi |
| `pregnant` | Phụ nữ có thai |
| `disabled_severe` | Người khuyết tật nặng hoặc đặc biệt nặng |
| `elderly_75` | Người từ đủ 75 tuổi trở lên |
| `meritorious` | Người có công với cách mạng |
| `severe_symptoms` | Triệu chứng nặng cần điều dưỡng đánh giá |

## 1. Đăng ký

### `POST /register`

Request:

```json
{
  "name": "Nguyễn Văn An",
  "email": "an@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

Response `201`:

```json
{
  "success": true,
  "message": "Đăng ký thành công.",
  "data": {
    "token": "1|plain-text-token",
    "user": {
      "id": 1,
      "name": "Nguyễn Văn An",
      "email": "an@example.com",
      "role": "patient"
    }
  }
}
```

## 2. Đăng nhập

### `POST /login`

Request:

```json
{
  "email": "an@example.com",
  "password": "password123"
}
```

Response `200`:

```json
{
  "success": true,
  "message": "Đăng nhập thành công.",
  "data": {
    "token": "2|plain-text-token",
    "user": {
      "id": 1,
      "name": "Nguyễn Văn An",
      "email": "an@example.com",
      "role": "patient"
    }
  }
}
```

Ghi chú:

- Chỉ tài khoản có role `patient` được đăng nhập qua API này.
- Android cần lưu `token` bằng DataStore.

## 3. Danh sách khoa

### `GET /departments`

Response `200`:

```json
{
  "success": true,
  "message": "Tải danh sách khoa thành công.",
  "data": [
    {
      "id": 1,
      "name": "Nội tổng quát",
      "room_number": "Phòng Nội tổng quát 203",
      "current_number": null,
      "average_time_per_patient": 4
    },
    {
      "id": 3,
      "name": "Tim mạch",
      "room_number": "Phòng Tim mạch 205",
      "current_number": "C021",
      "average_time_per_patient": 4
    }
  ]
}
```

## 4. Tạo phiếu khám

### `POST /tickets`

Yêu cầu Bearer token.

Request:

```json
{
  "department": "Nội tổng quát",
  "priority_reason": "normal",
  "patient_phone": "0901234567"
}
```

Response `201`:

```json
{
  "success": true,
  "message": "Tạo phiếu khám thành công.",
  "data": {
    "id": 15,
    "queue_number": "A015",
    "patient_name": "Nguyễn Văn An",
    "patient_phone": "0901234567",
    "department": "Nội tổng quát",
    "room": "Phòng Nội tổng quát 203",
    "status": "waiting_payment",
    "status_label": "Chờ thanh toán",
    "payment_status": "pending",
    "payment_status_label": "Chờ xác nhận HIS/thanh toán",
    "priority_level": 0,
    "estimated_wait": 12,
    "position_in_queue": 0,
    "called_at": null,
    "completed_at": null
  }
}
```

Ghi chú:

- App không gửi `priority_level`.
- Backend tự suy ra mức ưu tiên từ `priority_reason`.
- Nếu là cấp cứu, backend tự chuyển sang khoa cấp cứu, mức ưu tiên `5`, trạng thái `ready`.
- Tên bệnh nhân được lấy từ tài khoản đang đăng nhập, không gửi từ app.

## 5. Phiếu đang hoạt động của tôi

### `GET /my-ticket`

Yêu cầu Bearer token.

Response `200` khi có phiếu:

```json
{
  "success": true,
  "message": "Tải phiếu hiện tại thành công.",
  "data": {
    "id": 15,
    "queue_number": "A015",
    "patient_name": "Nguyễn Văn An",
    "patient_phone": "0901234567",
    "department": "Nội tổng quát",
    "room": "Phòng Nội tổng quát 203",
    "status": "ready",
    "status_label": "Sẵn sàng gọi",
    "payment_status": "paid",
    "payment_status_label": "Đã xác nhận HIS/thanh toán",
    "priority_level": 0,
    "estimated_wait": 8,
    "position_in_queue": 2,
    "called_at": null,
    "completed_at": null
  }
}
```

Response `200` khi chưa có phiếu:

```json
{
  "success": true,
  "message": "Bạn chưa có phiếu đang hoạt động.",
  "data": null
}
```

## 6. Trạng thái một phiếu cụ thể

### `GET /queue-status/{ticket}`

Yêu cầu Bearer token.

Response `200`:

```json
{
  "success": true,
  "message": "Tải trạng thái phiếu thành công.",
  "data": {
    "id": 15,
    "queue_number": "A015",
    "patient_name": "Nguyễn Văn An",
    "patient_phone": "0901234567",
    "department": "Nội tổng quát",
    "room": "Phòng Nội tổng quát 203",
    "status": "calling",
    "status_label": "Đang gọi",
    "payment_status": "paid",
    "payment_status_label": "Đã xác nhận HIS/thanh toán",
    "priority_level": 0,
    "estimated_wait": 0,
    "position_in_queue": 0,
    "called_at": "2026-05-18T08:15:30.000000Z",
    "completed_at": null
  }
}
```

Response `403`:

```json
{
  "message": "This action is unauthorized."
}
```

Ghi chú:

- Bệnh nhân chỉ được xem phiếu của chính mình.

## 7. Màn hình hàng đợi theo khoa

### `GET /display/{department}`

Response `200`:

```json
{
  "success": true,
  "message": "Tải màn hình hàng đợi thành công.",
  "data": {
    "department": "Nội tổng quát",
    "calling": null,
    "serving": null,
    "next": []
  }
}
```

## 8. QR payload

### `GET /tickets/{ticket}/qr`

Yêu cầu Bearer token.

Response `200`:

```json
{
  "success": true,
  "message": "Tạo dữ liệu QR thành công.",
  "data": {
    "ticket_id": 15,
    "queue_number": "A015",
    "qr_payload": "http://127.0.0.1:8000/kiosk/tickets/15"
  }
}
```

## 9. Đăng xuất và hồ sơ

### `POST /logout`

Yêu cầu Bearer token.

Response `200`:

```json
{
  "success": true,
  "message": "Đăng xuất thành công.",
  "data": null
}
```

### `GET /profile`

Yêu cầu Bearer token.

Response `200`:

```json
{
  "success": true,
  "message": "Tải hồ sơ thành công.",
  "data": {
    "id": 1,
    "name": "Nguyễn Văn An",
    "email": "an@example.com",
    "role": "patient"
  }
}
```

## Gợi ý model Android giai đoạn 1

```kotlin
data class ApiResponse<T>(
    val success: Boolean,
    val message: String,
    val data: T?
)

data class UserDto(
    val id: Long,
    val name: String,
    val email: String,
    val role: String
)

data class DepartmentDto(
    val id: Long,
    val name: String,
    val room_number: String,
    val current_number: String?,
    val average_time_per_patient: Int
)

data class QueueTicketDto(
    val id: Long,
    val queue_number: String,
    val patient_name: String,
    val patient_phone: String?,
    val department: String,
    val room: String,
    val status: String,
    val status_label: String,
    val payment_status: String,
    val payment_status_label: String,
    val priority_level: Int,
    val estimated_wait: Int?,
    val position_in_queue: Int,
    val called_at: String?,
    val completed_at: String?
)
```
