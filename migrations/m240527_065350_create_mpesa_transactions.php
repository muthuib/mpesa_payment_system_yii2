<?php

use yii\db\Migration;

/**
 * Class m240527_065350_create_mpesa_transactions
 */
class m240527_065350_create_mpesa_transactions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%mpesa_transaction}}', [
            'id' => $this->primaryKey(),
            'phone_number' => $this->string(15)->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'transaction_status' => $this->string(20)->notNull(),
            'request_id' => $this->string(100)->notNull(),
            'response_code' => $this->string(10)->notNull(),
            'response_description' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240527_065350_create_mpesa_transactions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240527_065350_create_mpesa_transactions cannot be reverted.\n";

        return false;
    }
    */
}