<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Models\Conversation;
use App\Models\Message;


class createConversation extends Controller
{
    //receiverId is the id of the user you started a conversation with! the other user
    public function createConversation(Request $request, $receiverId)
    {
        $user = $request->user();
        $checkConversation = Conversation::where('receiver_id',  $user->id)->where('sender_id', $receiverId)->orWhere('receiver_id', $receiverId)->where('sender_id', $user->id)->get();
        if(count($checkConversation)==0)
        {
            $createdConversation = Conversation::create(['receiver_id' => $receiverId , 'sender_id' => $user->id, 'last_message' => 'Click to start chat']);

            $createMessage = Message::create(['conversation_id'=> $createdConversation->id, 'sender_id' => $user->id, 'receiver_id'=> $receiverId]);

            $createdConversation->last_time_message = $createMessage->created_at;
            $createdConversation->save();

            return response()->json(['SUCCESS', 'CONVERSATION CREATED'], 201);
        }
         
        elseif(count($checkConversation) >= 1)
        {
            return response()->json(['MESSAGE', 'CONVERSATION EXISTS'], 200);
        }
    }

    public function getConversations(Request $request)
    {
        $user = $request->user();
        $conversations = Conversation::with('receiverInverseRelation','senderInverseRelation')->where('receiver_id',  $user->id)->orWhere('sender_id', $user->id)->get();
        return response()->json([
            'chats' => $conversations,
        ], 200);
    }

    public function searchUser($query)
    {
        $users = User::where('name', 'like', '%' . $query . '%')->get();
    
        if ($users->isEmpty()) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        return response()->json(['users' => $users], 200);
    }

    public function sendMessage(Request $request)
    {
        $user = $request->user();
        $conversationId = $request->header('conversation_id');
        $receiverId = $request->header('receiver_id');
        $message = $request->header('body');

        if($message == null)
        {
            return null;
        }

        $createdMessage = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'receiver_id' => $request->header('receiver_id'),
            'body' => $message,
        ]);

        $conversation = Conversation::find($conversationId);
        $countWords = strlen($message);
        if($countWords <= 52)
        {
        $conversation->update(['last_message' => $message]);
        }
        elseif($countWords > 52)
        {
        $cuttedMessage = Str::limit($message, 35);
        $conversation->update(['last_message' => $cuttedMessage]);
        }
        if($conversation->receiver_id != $user->id)
        {
            broadcast(new MessageSent($user, $conversation, $receiverId, $createdMessage));      
        }
        elseif($conversation->receiver_id == $user->id)
        {
            $receiverId = $user->id;
            broadcast(new MessageSent(auth()->user(), $this->selectedConversation, $this->receiverInstance, $this->messageCreated));      
        }
    }

    public function showChat(Request $request)
    {
        $user = $request->user();
        $conversationId = $request->header('conversation_id');
        $messages = Message::where('conversation_id', $conversationId)->get();

       
        return response()->json(['messages' => $messages], 200);
    }



}
