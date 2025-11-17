<?php

namespace Khdija\Support\Controllers;

use App\Http\Controllers\Controller;
use Khdija\Support\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = config('khdija-support.user_model');
    }

    /**
     * عرض شات الدعم للمنشأة
     */
    public function index(Request $request)
    {
        $bizId  = $request->user()->business_id;
        $userId = $request->user()->id;

        SupportMessage::where('business_id', $bizId)
            ->where('sender_role', 'admin')
            ->where('context_user_id', $userId)
            ->whereNull('read_by_business_at')
            ->update(['read_by_business_at' => now()]);

        $messages = SupportMessage::where('business_id', $bizId)
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) use ($userId) {
                    $q2->where('sender_role', 'business')
                       ->where('sender_id', $userId);
                })->orWhere(function ($q3) use ($userId) {
                    $q3->where('sender_role', 'admin')
                       ->where('context_user_id', $userId);
                });
            })
            ->orderBy('id')
            ->get();

        $admin = $this->userModel::where('is_superadmin', 1)->orderBy('id')->first();

        return view('khdija-support::business.support', compact('messages', 'admin'));
    }

    /**
     * إرسال رسالة من المنشأة
     */
    public function store(Request $request)
    {
        $request->validate(['body' => 'required|string|min:1']);

        $bizId = $request->user()->business_id;

        $msg = SupportMessage::create([
            'business_id' => $bizId,
            'sender_role' => 'business',
            'sender_id'   => $request->user()->id,
            'body'        => trim($request->body),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'   => true,
                'item' => [
                    'id'          => $msg->id,
                    'sender_role' => $msg->sender_role,
                    'body'        => $msg->body,
                    'at'          => $msg->created_at->format('Y-m-d H:i'),
                ],
            ]);
        }

        return back();
    }
}