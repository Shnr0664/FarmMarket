<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    // Send a message
    public function sendMessage(Request $request) : JsonResponse
    {
        // Validate incoming request
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        // Create a new message
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }

    // Get messages between two users (buyer and farmer)
    public function getMessages(Request $request, $userId) : JsonResponse
    {
        // Fetch all messages between the authenticated user and the given user (farmer)
        $messages = Message::where(function ($query) use ($userId) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $userId);
        })
            ->orWhere(function ($query) use ($userId) {
                $query->where('receiver_id', auth()->id())
                    ->where('sender_id', $userId);
            })
            ->get();

        // If no messages exist between the two users, return their data without messages
        if ($messages->isEmpty()) {
            $user = User::findOrFail($userId);  // Get the user data (e.g., farmer)

            // Return user info with a message saying there are no conversations yet
            return response()->json([
                'status' => 'success',
                'message' => null,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->personalInfo->name,
                    'email' => $user->email,
                    'profile_pic' => $user->profile_pic,
                ],
            ]);
        }

        // Return the messages if they exist
        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }


    // Clear messages older than 24 hours
    public function clearMessages() : JsonResponse
    {
        Message::where('created_at', '<', now()->subHours(24))->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Old messages deleted successfully.'
        ]);
    }

    public function getAllChats(Request $request)
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Retrieve all distinct chat pairs (sender_id, receiver_id), normalized
        $chats = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get();

        // Normalize chat pairs to avoid duplicates
        $normalizedChats = $chats->map(function ($message) use ($userId) {
            $senderId = $message->sender_id;
            $receiverId = $message->receiver_id;

            // Ensure that sender_id is always the lower ID and receiver_id is the higher one
            if ($senderId > $receiverId) {
                $senderId = $message->receiver_id;
                $receiverId = $message->sender_id;
            }

            return [
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
            ];
        })->unique(function ($chat) {
            // Return the unique sender/receiver pair
            return $chat['sender_id'] . '-' . $chat['receiver_id'];
        });

        // Initialize an array to store the chat details
        $chatDetails = [];

        foreach ($normalizedChats as $chat) {
            // Determine the other user in the chat
            $otherUserId = ($chat['sender_id'] == $userId) ? $chat['receiver_id'] : $chat['sender_id'];

            // Retrieve the other user and their personal information
            $otherUser = User::with('personalInfo')->find($otherUserId);

            if (!$otherUser || !$otherUser->personalInfo) {
                continue; // Skip if the user or their personal info is missing
            }

            // Extract the user's details including their name from personalInfo
            $otherUserData = [
                'id' => $otherUser->id,
                'email' => $otherUser->email,
                'name' => $otherUser->personalInfo->name,
                'profile_pic' => $otherUser->profile_pic
            ];

            // Retrieve the latest message in the chat
            $latestMessage = Message::where(function ($query) use ($userId, $otherUserId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $otherUserId);
            })
                ->orWhere(function ($query) use ($userId, $otherUserId) {
                    $query->where('sender_id', $otherUserId)
                        ->where('receiver_id', $userId);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            // Add the chat details to the array
            $chatDetails[] = [
                'user' => $otherUserData,
                'latest_message' => $latestMessage,
            ];
        }

        return response()->json([
            'status' => 'success',
            'chats' => $chatDetails,
        ]);
    }

}
