<?php

use Illuminate\Support\Facades\Route;
use Khdija\Support\Controllers\SupportInboxController;
use Khdija\Support\Controllers\SupportChatController;

$config = config('khdija-support');

Route::middleware($config['middleware']['admin'])
    ->prefix($config['routes']['admin_prefix'])
    ->group(function () {
        Route::get('/conversations', [SupportInboxController::class, 'index'])
            ->name('admin.conversations');
        Route::get('/conversations/{business}', [SupportInboxController::class, 'users'])
            ->name('admin.conversations.show');
        Route::post('/conversations/{business}/user/{user}/reply', [SupportInboxController::class, 'replyToUser'])
            ->name('admin.conversations.reply_user');
        Route::post('/conversations/{business}/user/{user}/ack', [SupportInboxController::class, 'ackUser'])
            ->name('admin.conversations.ack_user');
        Route::get('/conversations/counters', [SupportInboxController::class, 'counters'])
            ->name('admin.conversations.counters');
        Route::get('/conversations/counters-map', [SupportInboxController::class, 'countersMap'])
            ->name('admin.conversations.counters_map');
        Route::get('/conversations/{business}/user/{user}/stream', [SupportInboxController::class, 'stream'])
            ->name('admin.conversations.stream');
    });

Route::middleware($config['middleware']['business'])
    ->prefix($config['routes']['business_prefix'])
    ->group(function () {
        Route::get('/support', [SupportChatController::class, 'index'])
            ->name('business.support');
        Route::post('/support', [SupportChatController::class, 'store'])
            ->name('business.support.store');
    });