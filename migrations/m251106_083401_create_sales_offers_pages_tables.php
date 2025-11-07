<?php
use yii\db\Migration;

/**
 * Handles creation of tables: sales, offers, pages.
 */
class m251106_083401_create_sales_offers_pages_tables extends Migration

{
    public function safeUp()
    {
        // sales table
        $this->createTable('{{%sales}}', [
            'id' => $this->primaryKey(),
            'item' => $this->string(255)->notNull(),
            'price' => $this->decimal(12,2)->notNull()->defaultValue(0.00),
            'description' => $this->text(),
            'image' => $this->string(512), // store path or filename
            'created_at' => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->notNull(),
        ]);

        // offers table
        $this->createTable('{{%offers}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'starts_at' => $this->bigInteger()->null(),
            'ends_at' => $this->bigInteger()->null(),
            'image' => $this->string(512),
            'created_at' => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->notNull(),
        ]);

        // pages table (for 'landing' and 'about')
        $this->createTable('{{%pages}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(100)->notNull()->unique(),
            'title' => $this->string(255),
            'content' => $this->text(),
            'created_at' => $this->bigInteger()->notNull(),
            'updated_at' => $this->bigInteger()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%pages}}');
        $this->dropTable('{{%offers}}');
        $this->dropTable('{{%sales}}');
    }
}
