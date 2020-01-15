<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffDocumentsPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_document_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_document_id')->unsigned();
            $table->mediumText('annotation')->nullable();
            $table->string('image_url');
            $table->string('mime_type');
            $table->string('size');
            $table->timestamps();

            $table->foreign('staff_document_id')->references('id')->on('staff_documents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('staff_document_pages');
    }
}
