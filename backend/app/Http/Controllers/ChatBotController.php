<?php

namespace App\Http\Controllers;

use App\Services\ChatBotService;
use App\Http\Requests\ChatBot\StoreRequest;

class ChatBotController extends Controller
{
    protected $service;

    public function __construct(
        ChatBotService $service
    ) {
        $this->service = $service;
    }

    public function store(
        StoreRequest $request
    ) {
        return response()->json(
            $this->service->chat(
                $request->validated()
            )
        );
    }
}