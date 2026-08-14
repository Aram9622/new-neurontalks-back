<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuditRequest;
use App\Mail\AuditNotificationMail;
use App\Models\Audit;
use App\Models\MailTemplate;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuditController extends Controller
{
    public function store(StoreAuditRequest $request): JsonResponse
    {
        $audit = Audit::create($request->validated());

        $adminEmail = Setting::where('key', 'admin_email')->value('text_value')
            ?? config('mail.from.address');

        try {
            Mail::to($adminEmail)->send(new AuditNotificationMail(
                $audit,
                MailTemplate::auditDefault(),
            ));
        } catch (\Throwable $exception) {
            Log::warning('Unable to send audit notification email.', [
                'audit_id' => $audit->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your audit request has been received. Thank you!',
            'data' => $audit,
        ], 201);
    }
}
