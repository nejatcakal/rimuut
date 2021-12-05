<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Faker\Generator as Faker;
use App\Post;
class InvoiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_can_create_invoice()
    {
        $data = [
            'description' => Faker::sentence,
            'date' => Faker::date,
            'send_to' => factory(App\User::class),
            'currency' => 'TRY',
            'total' => 100,
            'tax_rate' => 5,
            'tax_amount' => 5,
            'grand_total' => 105,
        ];

        $this->post(route('invoices.store'), $data)
            ->assertStatus(201)
            ->assertJson($data);
    }

    public function test_can_update_invoices()
    {
        $post = factory(Invoice::class)->create();

        $data = [
            'description' => Faker::sentence,
            'grand_total' => 120
        ];

        $this->put(route('invoices.update', $post->id), $data)
            ->assertStatus(200)
            ->assertJson($data);
    }

    public function test_can_list_invoices()
    {
        $posts = factory(Invoice::class, 2)->create()->map(function ($post) {
            return $post->only(['id', 'description', 'grand_total']);
        });

        $this->get(route('posts'))
            ->assertStatus(200)
            ->assertJson($posts->toArray())
            ->assertJsonStructure([
                '*' => [ 'id', 'description', 'grand_total' ]
            ]);
    }
}
