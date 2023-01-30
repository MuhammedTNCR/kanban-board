<?php

use App\Models\Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default(Task::TO_DO);
            $table->string('priority')->default(Task::LOW);
            $table->smallInteger('order')->default(0);
            $table->unsignedBigInteger('assigned_user_id')->nullable()->index();
            $table->unsignedBigInteger('creator_user_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('assigned_user_id')->references('id')->on('users')->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreign('creator_user_id')->references('id')->on('users')->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
