<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GatewayClient
{
    public function chat(string $question, ?int $userId = null): array
    {
        return $this->post('/api/ai/chat', [
            'question' => $question,
            'user_id' => $userId,
        ], [
            'answer' => "AI Gateway hiện chưa kết nối, đây là phản hồi mô phỏng cho Smart Queue Hospital.\n\nTôi có thể hỗ trợ bạn theo 3 hướng:\n\n1. Giải thích quy trình lấy số, chờ khám và chuẩn bị giấy tờ.\n2. Gợi ý khoa khám phù hợp dựa trên triệu chứng ở mức tham khảo.\n3. Nhắc các dấu hiệu nguy hiểm cần báo điều dưỡng hoặc đến khu cấp cứu.\n\nNếu có triệu chứng nặng như đau ngực, khó thở, yếu liệt, chảy máu nhiều, ngất hoặc sốt cao kéo dài, hãy liên hệ nhân viên y tế ngay. AI không thay thế bác sĩ.",
            'sources' => ['Hospital Queue Procedure Mock', 'Emergency Triage Safety Rule'],
        ]);
    }

    public function ingestDocument(array $payload): array
    {
        return $this->post('/api/documents/ingest', $payload, [
            'status' => 'queued',
            'chunks' => 0,
        ]);
    }

    public function wellnessInsights(array $signals): array
    {
        return $this->post('/api/wellness/insights', ['signals' => $signals], [
            'summary' => 'The AI gateway is offline. Start the Lumen API to generate health insights.',
        ]);
    }

    private function post(string $path, array $payload, array $fallback): array
    {
        try {
            $response = Http::timeout((int) config('services.lumen_api.timeout', 30))
                ->acceptJson()
                ->post($this->baseUrl().$path, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Lumen gateway returned an error.', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Lumen gateway request failed.', [
                'path' => $path,
                'message' => $exception->getMessage(),
            ]);
        }

        return $fallback;
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.lumen_api.url'), '/');
    }
}
