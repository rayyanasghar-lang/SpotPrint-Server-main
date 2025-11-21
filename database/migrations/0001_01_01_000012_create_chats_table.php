<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->boolean('is_group')->default(false);
            $table->string('participants')->comment('Comma separated user IDs');
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->text('content');
            $table->json('message_status_logs')->nullable()->comment('JSON object to track message status for each user');
            /* [
                'received_by' => ['user_id3' => datetime, 'user_id2' => datetime,],
                'read_by' => ['user_id3' => datetime, 'user_id2' => datetime,],
            ] */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('chat_messages');
    }
}

