# SmartQueuePatient

Ứng dụng Android dành cho bệnh nhân của hệ thống Smart Queue.

## Cấu hình giai đoạn đầu

- Template tương đương: Empty Activity
- Ngôn ngữ: Kotlin
- Minimum SDK: API 26
- Package: `com.huy.smartqueue`

## Backend

- API base URL cho Android Emulator: `http://10.0.2.2:8000/api/`
- Laravel local server nên chạy bằng:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Công nghệ đã thêm

- Kotlin
- Retrofit + Gson converter
- OkHttp logging interceptor
- Lifecycle ViewModel + LiveData
- DataStore Preferences
- Navigation Component
- Material Design

## Package layout dự kiến

```text
com.huy.smartqueue
├── data
│   ├── api
│   ├── model
│   └── repository
├── datastore
├── ui
│   ├── auth
│   ├── departments
│   └── ticket
└── viewmodel
```
