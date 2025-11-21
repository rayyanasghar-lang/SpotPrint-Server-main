<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;

class GenericMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $template;

    public function __construct(string $templateName, array $data)
    {
        $data['app_name'] = config('app.name');
        $this->data = $data;

        // If order status exists, append it to template name
        if (isset($data['order_status'])) {
            $templateName = $templateName . '-' . $data['order_status'];
        }

        $this->template = EmailTemplate::where('name', $templateName)->first();
        if (!$this->template) {
            throw new \Exception("Email template '{$templateName}' not found.");
        }
    }

    public function build()
    {
        $content = $this->replacePlaceholders($this->template->content, $this->data);
        $subject = $this->replacePlaceholders($this->template->subject, $this->data);

        return $this
            ->subject($subject)
            ->view('emails.generic')
            ->with(['content' => $content, 'subject' => $subject]);
    }

    protected function replacePlaceholders(string $templateContent, array $data): string
    {
        foreach ($data as $key => $value) {
            $templateContent = str_replace("{{" . strtoupper($key) . "}}", $value, $templateContent);
        }

        return $templateContent;
    }
}
