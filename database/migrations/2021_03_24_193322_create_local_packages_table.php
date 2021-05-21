<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('url');
            $table->enum('type', ['direct', 'indirect', 'none']);
            $table->string('original_version')->nullable();
            $table->string('branch');
            $table->unsignedBigInteger('feature_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_packages');
    }
}
