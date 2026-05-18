# n8n Queue Notification Workflow

## Mục đích

Workflow này mô phỏng luồng thông báo đa kênh cho hệ thống Smart Queue:

`Laravel event -> notification_logs -> n8n webhook -> provider giả lập`

Hiện tại workflow chưa gửi Zalo hay SMS thật. Nó nhận webhook từ Laravel, phân nhánh theo loại thông báo, rồi phản hồi thành công để phục vụ demo kiến trúc automation.

## Thiết lập

1. Mở n8n.
2. Chọn `Import from File`.
3. Import file `n8n-workflows/queue-notification.json`.
4. Kích hoạt workflow.
5. Lấy webhook production URL từ node `Queue Notification Webhook`.
6. Cập nhật file `.env` của Laravel:

```env
N8N_NOTIFICATION_WEBHOOK_URL=http://localhost:5678/webhook/queue/notification
```

7. Chạy lại config nếu cần:

```bash
php artisan config:clear
```

## Webhook URL ví dụ

```text
http://localhost:5678/webhook/queue/notification
```

Khi dùng Docker Compose, URL từ Laravel container sang n8n thường sẽ là dạng:

```text
http://n8n:5678/webhook/queue/notification
```

## Payload Laravel gửi sang n8n

```json
{
  "notification_id": 15,
  "ticket_id": 42,
  "type": "near_turn",
  "title": "Sắp đến lượt khám",
  "message": "Phiếu A023 còn 5 lượt nữa. Vui lòng di chuyển gần phòng khám.",
  "patient_name": "Nguyễn Văn An",
  "patient_phone": "0901234567",
  "payload": {
    "queue_number": "A023",
    "remaining_before_me": 5,
    "department": "Nội tổng quát"
  }
}
```

Các loại `type` hiện hỗ trợ:

- `near_turn`
- `calling`
- `missed`
- `completed`

## Response n8n

```json
{
  "success": true,
  "provider": "n8n-simulated",
  "message": "Notification accepted"
}
```

## Test nhanh bằng curl

```bash
curl -X POST http://localhost:5678/webhook/queue/notification \
  -H "Content-Type: application/json" \
  -d '{
    "type": "near_turn",
    "channel": "n8n_webhook",
    "title": "Sắp đến lượt khám",
    "message": "Phiếu A023 còn 5 lượt nữa. Vui lòng di chuyển gần phòng khám.",
    "patient_name": "Nguyễn Văn An",
    "patient_phone": "0901234567",
    "queue_number": "A023",
    "department": "Nội tổng quát",
    "room_number": "Phòng Nội tổng quát 203",
    "payload": {
      "remaining_before_me": 5
    }
  }'
```

## Nâng cấp tiếp theo

Sau khi demo luồng giả lập ổn định, có thể thay node mô phỏng bằng provider thật:

1. `Zalo OA`
   - gửi tin nhắn chăm sóc khách hàng hoặc thông báo lịch khám.
2. `SMS provider`
   - tích hợp eSMS, Twilio hoặc nhà cung cấp trong nước.
3. `Firebase Cloud Messaging`
   - đẩy thông báo tới Android ngay cả khi app không mở.

Nên giữ `notification_logs` làm nguồn truy vết chính để biết:

- thông báo nào đã được tạo,
- gửi qua kênh nào,
- thành công hay thất bại,
- lúc nào đã gửi.
