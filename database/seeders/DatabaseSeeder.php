<?php

namespace Database\Seeders;

use App\Enums\GenerationStatus;
use App\Enums\GenerationType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Idempotent admin account, driven by env so it can be seeded safely on
        // every deploy (including free tiers where the DB resets on each build).
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@example.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ],
        );

        $this->seedExampleProduct();
    }

    /**
     * One worked example so the studio is never empty.
     *
     * The demo's SQLite file is ephemeral — it resets on every deploy and every
     * free-tier spin-down — and the entrypoint re-seeds on boot. Without this a
     * visitor arriving cold lands on an empty list with nothing to click.
     */
    private function seedExampleProduct(): void
    {
        $product = Product::firstOrCreate(
            ['name' => 'Ceramic pour-over dripper'],
            [
                'source_url' => 'https://example.com/products/pour-over-dripper',
                'notes' => 'Matte glaze stoneware, 1–2 cups, spiral ribs for even extraction. For filter-coffee drinkers.',
            ],
        );

        if ($product->generations()->exists()) {
            return;
        }

        $product->generations()->create([
            'type' => GenerationType::ProductDescription,
            'status' => GenerationStatus::Completed,
            'prompt' => 'Seeded example — see App\Services\Llm\PromptBuilder for the real template.',
            'result' => "Some coffee gear asks you to fuss. This one just gets out of the way. The spiral ribs hold the filter off the wall so water drains evenly instead of channelling down one side, which is the difference between a cup that tastes balanced and one that tastes thin.\n\nMatte stoneware, glazed inside and out, sized for one or two cups. It holds heat through the pour, wipes clean, and looks like it belongs on the counter rather than in a cupboard.",
            'provider' => 'seed',
            'model' => 'example',
            'input_tokens' => 96,
            'output_tokens' => 118,
            'duration_ms' => 2400,
            'completed_at' => now(),
        ]);
    }
}
