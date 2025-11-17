<?php

use yii\db\Migration;

/**
 * Drops the default value that forces every sale to belong to user_id=1.
 */
class m251117_120000_remove_default_user_id_from_sales_table extends Migration
{
    public function safeUp()
    {
        // Remove the default value so user_id must always be provided explicitly.
        $this->alterColumn('{{%sales}}', 'user_id', $this->integer()->notNull());
    }

    public function safeDown()
    {
        // Revert to the previous default of 1 if needed.
        $this->alterColumn('{{%sales}}', 'user_id', $this->integer()->notNull()->defaultValue(1));
    }
}

