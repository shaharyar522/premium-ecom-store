<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Subscriber;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', ['messages' => Message::latest()->paginate(20)]);
    }

    public function show(Message $message)
    {
        $message->update(['is_read' => true]);

        return view('admin.messages.show', ['message' => $message]);
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('ok', 'Message deleted.');
    }

    public function subscribers()
    {
        return view('admin.messages.subscribers', ['subscribers' => Subscriber::latest()->paginate(50)]);
    }

    public function exportSubscribers()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Joined']);
            foreach (Subscriber::latest()->cursor() as $s) fputcsv($out, [$s->email, $s->created_at->format('Y-m-d')]);
            fclose($out);
        }, 'subscribers.csv', ['Content-Type' => 'text/csv']);
    }
}
