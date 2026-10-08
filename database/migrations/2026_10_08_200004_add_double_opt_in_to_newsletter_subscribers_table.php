<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table): void {
            $table->string('token', 64)->nullable()->unique()->after('email');
            $table->timestamp('confirmed_at')->nullable()->after('subscribed_at');
        });

        DB::table('newsletter_subscribers')->orderBy('id')->eachById(function (object $subscriber): void {
            DB::table('newsletter_subscribers')
                ->where('id', $subscriber->id)
                ->update([
                    'token' => Str::random(64),
                    'confirmed_at' => $subscriber->subscribed_at,
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table): void {
            $table->dropUnique('newsletter_subscribers_token_unique');
            $table->dropColumn(['token', 'confirmed_at']);
        });
    }
};
