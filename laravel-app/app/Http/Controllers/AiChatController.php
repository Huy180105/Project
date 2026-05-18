<?php

namespace App\Http\Controllers;

use App\Services\GatewayClient;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiChatController extends Controller
{
    public function index(): View
    {
        $history = session('ai_chat_history', []);

        return view('ai-chat.index', [
            'question' => null,
            'answer' => null,
            'sources' => [],
            'history' => $history,
        ]);
    }

    public function store(Request $request, GatewayClient $gateway): View
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
        ]);

        $result = $gateway->chat($data['question'], optional($request->user())->id);
        $answer = $result['answer'] ?? 'The AI gateway did not return an answer.';
        $sources = $result['sources'] ?? [];

        $history = collect(session('ai_chat_history', []))
            ->push([
                'question' => $data['question'],
                'answer' => $answer,
                'sources' => $sources,
                'created_at' => now()->format('H:i'),
            ])
            ->take(-8)
            ->values()
            ->all();

        session(['ai_chat_history' => $history]);

        return view('ai-chat.index', [
            'question' => $data['question'],
            'answer' => $answer,
            'sources' => $sources,
            'history' => $history,
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->forget('ai_chat_history');

        return redirect()->route('ai-chat.index');
    }
}
