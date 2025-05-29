<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $token;
    protected $phoneId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneId = config('services.whatsapp.phone_id');
    }

    public function sendTemplate($to, $name, $link)
    {
        return Http::withToken($this->token)->post("https://graph.facebook.com/v19.0/{$this->phoneId}/messages", [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => "daily_task_update",
                "language" => [ "code" => "fr" ],
                "components" => [[
                    "type" => "body",
                    "parameters" => [
                        ["type" => "text", "text" => $name],
                        ["type" => "text", "text" => $link],
                    ]
                ]]
            ]
        ]);
    }
}