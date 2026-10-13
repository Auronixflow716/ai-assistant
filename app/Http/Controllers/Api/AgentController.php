<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgentCommand;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    // Create a command from the UI (user)
    public function createCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'args' => 'nullable|array',
            'target_device' => 'nullable|string',
        ]);

        $cmd = AgentCommand::create([
            'user_id' => $request->user()->id,
            'target_device' => $request->input('target_device'),
            'command' => $request->input('command'),
            'args' => $request->input('args', []),
            'status' => 'pending',
        ]);

        return response()->json(['status' => true, 'id' => $cmd->id, 'command' => $cmd->command]);
    }

    // Agent polls for next pending command for its device
    public function pollCommands(Request $request)
    {
        $device = $request->query('device_id');

        $cmd = AgentCommand::where('status', 'pending')
            ->where(function ($q) use ($device) {
                $q->whereNull('target_device')->orWhere('target_device', $device);
            })
            ->orderBy('created_at')
            ->first();

        if (!$cmd) {
            return response()->json(['has' => false]);
        }

        // mark as running
        $cmd->status = 'running';
        $cmd->save();

        return response()->json([
            'has' => true,
            'id' => $cmd->id,
            'command' => $cmd->command,
            'args' => $cmd->args ?? []
        ]);
    }

    // Agent submits result
    public function submitResult(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'result' => 'nullable|string',
        ]);

        $cmd = AgentCommand::findOrFail($id);
        $cmd->status = $request->input('status');
        $cmd->result = $request->input('result');
        $cmd->save();

        return response()->json(['status' => true]);
    }

    // placeholder for future LLM map endpoint (we will add logic in later steps)
    public function mapTextToCommand(Request $request)
    {
        // will be implemented in Step: LLM mapping
        return response()->json(['status' => false, 'message' => 'Not implemented yet'], 501);
    }
}
