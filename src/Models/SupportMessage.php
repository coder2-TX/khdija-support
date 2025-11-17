<?php

namespace Khdija\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'business_id',
        'sender_role', 
        'sender_id',
        'context_user_id',
        'body',
        'read_by_admin_at',
        'read_by_business_at'
    ];

    protected $casts = [
        'read_by_admin_at' => 'datetime',
        'read_by_business_at' => 'datetime',
    ];

    /**
     * العلاقة مع المنشأة
     */
    public function business(): BelongsTo
    {
        $businessModel = config('khdija-support.business_model');
        return $this->belongsTo($businessModel, 'business_id');
    }

    /**
     * العلاقة مع المرسل
     */
    public function sender(): BelongsTo
    {
        $userModel = config('khdija-support.user_model');
        return $this->belongsTo($userModel, 'sender_id');
    }

    /**
     * العلاقة مع المستخدم المستهدف (لردود الأدمن)
     */
    public function contextUser(): BelongsTo
    {
        $userModel = config('khdija-support.user_model');
        return $this->belongsTo($userModel, 'context_user_id');
    }

    /**
     * تحديد إذا كانت الرسالة مقروءة بناءً على الدور
     */
    public function isReadBy(string $role): bool
    {
        return $role === 'admin' 
            ? !is_null($this->read_by_admin_at)
            : !is_null($this->read_by_business_at);
    }

    /**
     * تحديد إذا كانت الرسالة مرسلة من الأدمن
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_role === 'admin';
    }

    /**
     * تحديد إذا كانت الرسالة مرسلة من المنشأة
     */
    public function isFromBusiness(): bool
    {
        return $this->sender_role === 'business';
    }
}