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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('phoneNumber')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->decimal('latitude', 12, 8);
            $table->decimal('longitude', 12, 8);
            $table->string('address');
            $table->string('website')->nullable();
            $table->binary('logo')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('name');
            $table->string('phoneNumber')->unique();
            $table->string('password');
            $table->unsignedBigInteger('organizationId')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('organizationId')->references('id')->on('organizations');
        });

        Schema::create('donors', function (Blueprint $table) {
            $table->string('phoneNumber')->primary();
            $table->string('email')->unique();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('fname');
            $table->string('mname')->nullable();
            $table->string('lname');
            $table->string('password');
            $table->string('bloodGroup');
            $table->string('address');
            $table->string('gender');
            $table->date('birthDate');
            $table->binary('profilePicture')->nullable();
            $table->boolean('diabetes')->default(false);
            $table->boolean('loginStatus')->default(false);
            $table->boolean('phoneVerified')->default(false);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('phoneNumber');
            $table->string('otp');
            $table->timestamps();
        });

        Schema::create('camps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizationId');
            $table->string('name');
            $table->string('address');
            $table->decimal('latitude', 12, 8);
            $table->decimal('longitude', 12, 8);
            $table->dateTime('startDate');
            $table->dateTime('endDate');
            $table->integer('attendees')->nullable();
            $table->binary('pictures')->nullable();
            $table->timestamps();

            $table->foreign('organizationId')->references('id')->on('organizations');

        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('phoneNumber');
            $table->string('bloodGroup');
            $table->string('bloodType')->nullable();
            $table->integer('quantity');
            $table->date('donationDate');
            $table->unsignedBigInteger('organizationId');
            $table->integer('upperBP')->nullable();
            $table->integer('lowerBP')->nullable();
            $table->decimal('weight')->nullable();
            $table->mediumText('notes')->nullable();
            $table->unsignedBigInteger('campId')->nullable();
            $table->string('status')->default('interested');
            $table->timestamps();

            $table->foreign('campId')->references('id')->on('camps');
            $table->foreign('phoneNumber')->references('phoneNumber')->on('donors');
            $table->foreign('organizationId')->references('id')->on('organizations');
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizationId');
            $table->string('bloodGroup');
            $table->string('bloodType');
            $table->integer('quantity');
            $table->integer('price');
            $table->timestamps();

            $table->foreign('organizationId')->references('id')->on('organizations');
        });

        Schema::create('glossary', function (Blueprint $table) {
            $table->id();
            $table->string('key_en')->unique();
            $table->string('ne')->nullable();
            $table->timestamps();
        });

        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('phoneNumber');
            $table->string('bloodGroup');
            $table->string('bloodType');
            $table->integer('quantity');
            $table->dateTime('requestDate');
            $table->dateTime('needByDate');
            $table->string('address');
            $table->unsignedBigInteger('fulfilled_by')->nullable();
            $table->timestamps();

            $table->foreign('phoneNumber')->references('phoneNumber')->on('donors');
            $table->foreign('fulfilled_by')->references('id')->on('organizations');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   //order matters
        Schema::dropIfExists('donations');
        Schema::dropIfExists('requests');
        Schema::dropIfExists('sms');
        Schema::dropIfExists('donors');
        Schema::dropIfExists('users');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('camps');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('glossary');
        Schema::dropIfExists('users');


    }
};
