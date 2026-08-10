<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders(): void
    {
        $this->get(route('contact.create'))
            ->assertOk()
            ->assertSee('Get in');
    }

    public function test_a_valid_submission_is_stored_and_redirects_with_status(): void
    {
        $payload = [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'message' => 'I would love to talk about your project.',
        ];

        $response = $this->post(route('contact.store'), $payload);

        $response->assertRedirect(route('contact.create'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'message' => 'I would love to talk about your project.',
        ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $response = $this->from(route('contact.create'))
            ->post(route('contact.store'), [
                'name' => '',
                'email' => '',
                'message' => '',
            ]);

        $response->assertRedirect(route('contact.create'));
        $response->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_email_must_be_valid(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => 'Grace Hopper',
            'email' => 'not-an-email',
            'message' => 'This message is definitely long enough.',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_message_must_meet_minimum_length(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => 'Alan Turing',
            'email' => 'alan@example.com',
            'message' => 'too short',
        ]);

        $response->assertSessionHasErrors('message');
        $this->assertDatabaseCount('contact_messages', 0);
    }
}
