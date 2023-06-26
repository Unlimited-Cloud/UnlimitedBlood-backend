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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

        });

        Schema::create('donors', function (Blueprint $table) {
            $table->string('phoneNumber')->primary();
            $table->string('email')->unique();
            $table->string('fname');
            $table->string('mname')->nullable();
            $table->string('lname');
            $table->string('password');
            $table->string('bloodType');
            $table->string('address');
            $table->string('gender');
            $table->date('birthDate');
            $table->string('profilePicture')->nullable();
            $table->boolean('diabetes')->default(false);
            $table->boolean('loginStatus')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('phoneNumber')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->string('address');
            $table->string('logo')->nullable();
            $table->boolean('loginStatus')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->string('phoneNumber');
            $table->string('bloodType');
            $table->string('donationType');
            $table->integer('quantity');
            $table->date('donationDate');
            $table->unsignedBigInteger('organizationId');
            $table->integer('upperBP')->nullable();
            $table->integer('lowerBP')->nullable();
            $table->decimal('weight')->nullable();
            $table->mediumText('notes')->nullable();
            $table->timestamps();

            $table->foreign('phoneNumber')->references('phoneNumber')->on('donors');
            $table->foreign('organizationId')->references('id')->on('organizations');
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('organizationId');
            $table->string('blood');
            $table->string('donationType');
            $table->integer('quantity');
            $table->foreign('organizationId')->references('id')->on('organizations');
        });

        Schema::create('glossary', function (Blueprint $table) {
            $table->id();
            $table->string('key_en')->unique();
            $table->string('ne')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donors');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('glossary');

    }
};
