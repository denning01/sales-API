<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m251112_083226_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
   public function safeUp()
{
    $this->createTable('{{%user}}', [
        'id' => $this->primaryKey(),
        'username' => $this->string(100)->notNull()->unique(),
        'email' => $this->string(150)->notNull()->unique(),
        'password_hash' => $this->string()->notNull(),
        'auth_key' => $this->string(),
        'access_token' => $this->string(),
        'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
    ]);
}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
{
    $this->dropTable('{{%user}}');
}
}
