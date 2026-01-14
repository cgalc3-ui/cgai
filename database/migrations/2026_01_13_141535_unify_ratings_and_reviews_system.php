<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Drop old unique constraint if it exists (it was named ratings_booking_id_customer_id_unique usually)
            // But let's be safe and just add the new columns first
            $table->unsignedBigInteger('ratable_id')->nullable()->after('id');
            $table->string('ratable_type')->nullable()->after('ratable_id');
            $table->text('comment_en')->nullable()->after('comment');
            $table->boolean('is_approved')->default(true)->after('comment_en');
            $table->foreignId('booking_id')->nullable()->change();

            $table->index(['ratable_id', 'ratable_type']);
        });

        // Migrate existing ratings to polymorphic
        \Illuminate\Support\Facades\DB::table('ratings')->get()->each(function ($rating) {
            $booking = \Illuminate\Support\Facades\DB::table('bookings')->where('id', $rating->booking_id)->first();
            if ($booking) {
                $ratableType = $booking->booking_type === 'consultation'
                    ? 'App\\Models\\Consultation'
                    : 'App\\Models\\Service';
                $ratableId = $booking->booking_type === 'consultation'
                    ? $booking->consultation_id
                    : $booking->service_id;

                \Illuminate\Support\Facades\DB::table('ratings')->where('id', $rating->id)->update([
                    'ratable_id' => $ratableId,
                    'ratable_type' => $ratableType,
                ]);
            }
        });

        // Migrate AI Service reviews
        if (Schema::hasTable('ai_service_reviews')) {
            \Illuminate\Support\Facades\DB::table('ai_service_reviews')->get()->each(function ($review) {
                \Illuminate\Support\Facades\DB::table('ratings')->insert([
                    'ratable_id' => $review->ai_service_id,
                    'ratable_type' => 'App\\Models\\AiService',
                    'customer_id' => $review->user_id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'comment_en' => $review->comment_en,
                    'is_approved' => $review->is_approved,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                ]);
            });
            Schema::dropIfExists('ai_service_reviews');
        }

        // Migrate Ready App reviews
        if (Schema::hasTable('ready_app_reviews')) {
            \Illuminate\Support\Facades\DB::table('ready_app_reviews')->get()->each(function ($review) {
                \Illuminate\Support\Facades\DB::table('ratings')->insert([
                    'ratable_id' => $review->ready_app_id,
                    'ratable_type' => 'App\\Models\\ReadyApp',
                    'customer_id' => $review->user_id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'comment_en' => $review->comment_en,
                    'is_approved' => $review->is_approved,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                ]);
            });
            Schema::dropIfExists('ready_app_reviews');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropColumn(['ratable_id', 'ratable_type', 'comment_en', 'is_approved']);
            $table->foreignId('booking_id')->nullable(false)->change();
        });
    }
};
