<?php

namespace Tests\Feature;

use App\Mail\ContactEnquiry;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactEnquiryTest extends TestCase
{
    private function enquiry(): array
    {
        return ['name' => 'Test Seller', 'email' => 'seller@example.test', 'subject' => 'General Inquiry',
            'message' => 'Can I organize products by collection?', 'consent' => '1', 'website' => ''];
    }

    public function test_valid_consent_based_enquiry_is_sent_to_the_public_contact_address(): void
    {
        Mail::fake();
        $this->post('/contact', $this->enquiry())->assertRedirect(route('landing.contact'))
            ->assertSessionHas('contact_sent');
        Mail::assertSent(ContactEnquiry::class, fn ($mail) => $mail->hasTo('metohub@metora.in')
            && $mail->hasReplyTo('seller@example.test') && $mail->enquiryMessage === $this->enquiry()['message']);
    }

    public function test_enquiry_requires_consent_and_rejects_honeypot_input(): void
    {
        Mail::fake();
        $this->post('/contact', array_replace($this->enquiry(), ['consent' => null]))->assertSessionHasErrors('consent');
        $this->post('/contact', array_replace($this->enquiry(), ['website' => 'spam.example']))->assertSessionHasErrors('website');
        $this->post('/contact', array_replace($this->enquiry(), ['subject' => 'unapproved subject']))->assertSessionHasErrors('subject');
        Mail::assertNothingSent();
    }

    public function test_contact_submission_is_rate_limited(): void
    {
        Mail::fake();
        for ($i = 0; $i < 3; $i++) {
            $this->post('/contact', $this->enquiry())->assertRedirect();
        }
        $this->post('/contact', $this->enquiry())->assertStatus(429);
        Mail::assertSentCount(3);
    }

    public function test_mail_failure_does_not_show_a_false_success(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Simulated mail failure'));
        $this->from('/contact')->post('/contact', $this->enquiry())->assertRedirect('/contact')
            ->assertSessionHasErrors('contact')->assertSessionMissing('contact_sent')
            ->assertSessionHasInput('email', 'seller@example.test');
    }

    public function test_enquiry_mail_escapes_untrusted_message_content(): void
    {
        $mail = new ContactEnquiry('Test Seller', 'seller@example.test', 'General Inquiry', '<script>alert(1)</script>');
        $html = $mail->render();
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }
}
