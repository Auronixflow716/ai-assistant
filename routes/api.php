<?php

use App\Http\Controllers\Api\AgentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->post('/agent', function (Request $request) {
    $command = strtolower($request->command);

    if (str_contains($command, 'notepad')) {
        return response()->json([
            'reply' => 'Opening Notepad...',
            'action' => 'open_notepad'
        ]);
    }

    return response()->json([
        'reply' => "You said: {$command}. I'm still learning to perform that."
    ]);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/agent/command', [AgentController::class, 'createCommand']); // create from UI
    Route::post('/agent/map-text-to-command', [AgentController::class, 'mapTextToCommand']); // later for LLM mapping
    Route::get('/agent/commands/poll', [AgentController::class, 'pollCommands']); // agent polls
    Route::post('/agent/command/{id}/result', [AgentController::class, 'submitResult']); // agent posts result
});


Route::post('/assistant', function (Request $request) {
    $text = $request->input('text');

    // Simple logic for now
    if (stripos($text, 'open notepad') !== false) {
        return response()->json(['reply' => 'Opening Notepad.']);
    } elseif (stripos($text, 'hello') !== false) {
        return response()->json(['reply' => 'Hello there! How can I help you today?']);
    } else {
        return response()->json(['reply' => "I'm not sure what you mean by '$text'."]);
    }
});

Route::post('/assistant', function (Request $request) {
    $text = strtolower($request->input('text'));

    if (str_contains($text, 'notepad')) {
        return response()->json(['reply' => 'Opening Notepad.', 'action' => 'open_notepad']);
    }

    if (str_contains($text, 'this pc') || str_contains($text, 'my computer')) {
        return response()->json(['reply' => 'Opening This PC.', 'action' => 'open_this_pc']);
    }

    if (str_contains($text, 'microsoft word')) {
        return response()->json(['reply' => 'Opening Microsoft Word.', 'action' => 'open_word']);
    }

    if (str_contains($text, 'chrome')) {
        return response()->json(['reply' => 'Opening Chrome.', 'action' => 'open_chrome']);
    }

    return response()->json([
        'reply' => "You said: {$text}. I'm still learning to perform that."
    ]);
});

