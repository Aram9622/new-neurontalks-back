<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\MailTemplate;
use App\Models\Setting;
use App\Mail\AdminContactNotificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Валидация данных
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        // Сохранение в базу
        $contact = Contact::create($validatedData);

        // Получаем Email админа из настроек или из .env
        $adminEmail = Setting::where('key', 'admin_email')->value('text_value') ?? config('mail.from.address');

        try {
            // 1. Уведомление администратору
            Mail::to($adminEmail)->send(new AdminContactNotificationMail(
                $contact,
                MailTemplate::preferredFor(MailTemplate::TYPE_CONTACT_NOTIFICATION),
            ));
            
            // 2. (Опционально) Подтверждение клиенту (если вы хотите отдельное письмо "Мы получили ваше сообщение")
            // Здесь можно создать отдельный Mailable, если нужно.
        } catch (\Throwable $exception) {
            Log::warning('Unable to send contact notification email.', [
                'contact_id' => $contact->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received. Thank you!',
            'data' => $contact
        ], 201);
    }
}
