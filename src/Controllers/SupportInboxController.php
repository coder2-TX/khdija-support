<?php

namespace Khdija\Support\Controllers;

use App\Http\Controllers\Controller;
use Khdija\Support\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportInboxController extends Controller
{
    protected $businessModel;
    protected $userModel;

    public function __construct()
    {
        $this->businessModel = config('khdija-support.business_model');
        $this->userModel = config('khdija-support.user_model');
    }

    /**
     * عرض جميع المحادثات مع المنشآت
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));

        $rows = SupportMessage::select(
                'business_id',
                DB::raw('MAX(id) as last_id'),
                DB::raw('SUM(CASE WHEN sender_role="business" AND read_by_admin_at IS NULL THEN 1 ELSE 0 END) as unread')
            )
            ->groupBy('business_id')
            ->orderByDesc('last_id')
            ->get();

        $items = $rows->map(function ($r) {
            $business = $this->businessModel::find($r->business_id);
            return (object) [
                'business' => $business,
                'last'     => SupportMessage::find($r->last_id),
                'unread'   => (int) $r->unread,
            ];
        });

        if ($q !== '') {
            $qLower = mb_strtolower($q);
            $items = $items->filter(function ($it) use ($qLower) {
                return str_contains(mb_strtolower($it->business->name ?? ''), $qLower)
                    || str_contains(mb_strtolower($it->business->slug ?? ''), $qLower);
            })->values();
        }

        return view('khdija-support::admin.index', compact('items'));
    }

    /**
     * عرض محادثات مستخدمين محددين داخل منشأة
     */
    public function users($businessId)
    {
        $business = $this->businessModel::findOrFail($businessId);

        $userRows = SupportMessage::where('business_id', $business->id)
            ->where('sender_role', 'business')
            ->select(
                'sender_id',
                DB::raw('MAX(id) AS last_id'),
                DB::raw('SUM(CASE WHEN read_by_admin_at IS NULL THEN 1 ELSE 0 END) AS unread')
            )
            ->groupBy('sender_id')
            ->orderByDesc('last_id')
            ->get();

        $items = $userRows->map(function ($r) use ($business) {
            $user = $this->userModel::find($r->sender_id);

            $thread = SupportMessage::where('business_id', $business->id)
                ->where(function ($q) use ($r) {
                    $q->where(fn ($q2) => $q2->where('sender_role', 'business')->where('sender_id', $r->sender_id))
                      ->orWhere(fn ($q3) => $q3->where('sender_role', 'admin')->where('context_user_id', $r->sender_id));
                })
                ->orderBy('id')
                ->get();

            return (object) [
                'user'     => $user,
                'last'     => SupportMessage::find($r->last_id),
                'unread'   => (int) $r->unread,
                'messages' => $thread,
            ];
        });

        return view('khdija-support::admin.users', compact('business', 'items'));
    }

    /**
     * تأكيد قراءة رسائل مستخدم
     */
    public function ackUser(Request $request, $businessId, $userId)
    {
        SupportMessage::where('business_id', $businessId)
            ->where('sender_role', 'business')
            ->where('sender_id', $userId)
            ->whereNull('read_by_admin_at')
            ->update(['read_by_admin_at' => now()]);

        $total = SupportMessage::where('sender_role', 'business')
            ->whereNull('read_by_admin_at')
            ->count();

        return response()->json(['ok' => true, 'total_unread' => $total]);
    }

    /**
     * الرد على مستخدم محدد
     */
    public function replyToUser(Request $request, $businessId, $userId)
    {
        $request->validate(['body' => 'required|string|min:1']);

        $msg = SupportMessage::create([
            'business_id'     => $businessId,
            'sender_role'     => 'admin',
            'sender_id'       => $request->user()->id,
            'context_user_id' => $userId,
            'body'            => trim($request->body),
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

    /**
     * الحصول على عدد الرسائل غير المقروءة
     */
    public function counters()
    {
        $total = SupportMessage::where('sender_role', 'business')
            ->whereNull('read_by_admin_at')
            ->count();

        return response()->json(['total_unread' => $total]);
    }

    /**
     * خريطة العدادات لكل منشأة
     */
    public function countersMap()
    {
        $rows = SupportMessage::where('sender_role', 'business')
            ->whereNull('read_by_admin_at')
            ->select('business_id', DB::raw('COUNT(*) as unread'))
            ->groupBy('business_id')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r->business_id] = (int) $r->unread;
        }

        $total = array_sum($map);

        return response()->json([
            'total_unread' => $total,
            'businesses'   => $map,
        ]);
    }

    /**
     * بث الرسائل الجديدة
     */
    public function stream(Request $request, $businessId, $userId)
    {
        $after = (int) $request->query('after', 0);

        $messages = SupportMessage::where('business_id', $businessId)
            ->where(function ($q) use ($userId) {
                $q->where(fn ($q2) => $q2->where('sender_role', 'business')->where('sender_id', $userId))
                  ->orWhere(fn ($q3) => $q3->where('sender_role', 'admin')->where('context_user_id', $userId));
            })
            ->when($after > 0, fn ($q) => $q->where('id', '>', $after))
            ->orderBy('id')
            ->get(['id', 'sender_role', 'body', 'created_at']);

        $userUnread = SupportMessage::where('business_id', $businessId)
            ->where('sender_role', 'business')
            ->where('sender_id', $userId)
            ->whereNull('read_by_admin_at')
            ->count();

        $totalUnread = SupportMessage::where('sender_role', 'business')
            ->whereNull('read_by_admin_at')
            ->count();

        return response()->json([
            'items' => $messages->map(fn ($m) => [
                'id'          => $m->id,
                'sender_role' => $m->sender_role,
                'body'        => $m->body,
                'at'          => $m->created_at->format('Y-m-d H:i'),
            ]),
            'max_id'       => $messages->max('id') ?? $after,
            'user_unread'  => $userUnread,
            'total_unread' => $totalUnread,
        ]);
    }
}