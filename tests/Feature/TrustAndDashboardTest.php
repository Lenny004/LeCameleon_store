<?php

namespace Tests\Feature;

use App\Enums\OfferStatus;
use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\ReturnRequestStatus;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_and_contact_pages_are_public(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Le Cameleon');

        $this->get(route('contact.show'))
            ->assertOk()
            ->assertSee('Contacto');

        $this->get(route('faq'))
            ->assertOk()
            ->assertSee('Preguntas frecuentes')
            ->assertSee('grados de condición');
    }

    public function test_newsletter_subscribes_email(): void
    {
        $this->post(route('newsletter.store'), [
            'email' => 'vintage@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'vintage@example.com',
        ]);
    }

    public function test_newsletter_rejects_duplicate_email(): void
    {
        NewsletterSubscriber::query()->create([
            'email' => 'vintage@example.com',
            'subscribed_at' => now(),
        ]);

        $this->from(route('home'))
            ->post(route('newsletter.store'), [
                'email' => 'vintage@example.com',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('email');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post(route('contact.store'), [
            'name' => 'María López',
            'email' => 'maria@example.com',
            'message' => '¿Tienen esta chaqueta en otra talla?',
        ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'María López',
            'email' => 'maria@example.com',
        ]);
    }

    public function test_admin_can_list_and_mark_contact_messages_read(): void
    {
        $admin = User::factory()->admin()->create();
        $message = ContactMessage::query()->create([
            'name' => 'Cliente',
            'email' => 'cliente@example.com',
            'message' => 'Hola, necesito ayuda con mi pedido.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee('cliente@example.com');

        $this->actingAs($admin)
            ->patch(route('admin.messages.read', $message))
            ->assertRedirect();

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_homepage_shows_top_categories_with_product_counts(): void
    {
        $category = Category::factory()->create(['name' => 'Denim Vintage', 'slug' => 'denim-vintage']);
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'status' => ProductStatus::Published,
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Colecciones')
            ->assertSee('Denim Vintage')
            ->assertSee('2 piezas')
            ->assertSee(route('shop.index', ['category' => 'denim-vintage']), false);
    }

    public function test_operational_kpis_reflect_pending_workloads(): void
    {
        Offer::factory()->create(['status' => OfferStatus::Pending]);
        Order::factory()->create(['status' => OrderStatus::Pending]);
        ReturnRequest::query()->create([
            'order_id' => Order::factory()->create()->id,
            'order_item_id' => null,
            'user_id' => User::factory()->create()->id,
            'reason' => 'Too small',
            'status' => ReturnRequestStatus::Pending,
        ]);
        Product::factory()->create([
            'status' => ProductStatus::Published,
            'is_authenticated' => true,
            'published_at' => now(),
        ]);

        $kpis = app(AnalyticsService::class)->operationalKpis();

        $this->assertSame(1, $kpis['pending_offers_count']);
        $this->assertSame(2, $kpis['pending_orders_count']);
        $this->assertSame(1, $kpis['pending_returns_count']);
        $this->assertSame(1, $kpis['authenticated_products_count']);
    }
}
