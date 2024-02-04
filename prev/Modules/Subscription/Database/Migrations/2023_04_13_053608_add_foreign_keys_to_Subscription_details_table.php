<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToSubscriptionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('Subscription_details', function (Blueprint $table) {
        //     $table->foreign(['package_subscription_id'])->nullable()->references(['id'])->on('package_subscriptions');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Subscription_details', function (Blueprint $table) {

        });
    }
}
