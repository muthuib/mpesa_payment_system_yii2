<?php

use yii\db\Migration;

/**
 * Class m240502_070722_user
 */
class m240502_070722_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'ID' => $this->primaryKey(),
            'PASSWORD' => $this->string(100),
            'EMAIL' => $this->string(100)->unique(),
            'AUTH_KEY' => $this->string(100),
            'ACCESS_TOKEN' => $this->string(100),
            'PASSWORD_RESET_TOKEN' => $this->string(100),
            'STATUS' => $this->smallInteger(6),
            'CREATED_AT' => $this->integer(11),
            'UPDATED_AT' => $this->integer(11),
            'VERIFICATION_TOKEN' => $this->string(100),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240502_070722_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240502_070722_user cannot be reverted.\n";

        return false;
    }
    */
}