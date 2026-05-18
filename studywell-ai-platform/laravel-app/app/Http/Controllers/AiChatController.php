<?php

namespace App\Http\Controllers;

use App\Services\GatewayClient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiChatController extends Controller
{
    public function index(): View
    {
        return view('ai-chat.index', [
            'question' => null,
            'answer' => null,
            'sources' => [],
        ]);
    }

    public function store(Request $request, GatewayClient $gateway): View
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        $result = $gateway->chat($data['question'], optional($request->user())->id);

        return view('ai-chat.index', [
            'question' => $data['question'],
            'answer' => $result['answer'] ?? 'The AI gateway did not return an answer.',
            'sources' => $result['sources'] ?? [],
        ]);
    }
}

