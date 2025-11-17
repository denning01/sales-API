<?php

use yii\db\Migration;

/**
 * Handles adding user_id column to table `{{%sales}}`.
 */
class m251114_110524_add_user_id_to_sales_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = $this->db->getTableSchema('{{%sales}}', true);
        
        // Check if user_id column already exists
        if (!isset($tableSchema->columns['user_id'])) {
            $this->addColumn('{{%sales}}', 'user_id', $this->integer()->notNull()->after('id'));
            // Refresh schema after adding column
            $tableSchema = $this->db->getTableSchema('{{%sales}}', true);
        }
        
        // Check if foreign key already exists
        $fkExists = false;
        if ($tableSchema) {
            $foreignKeys = $tableSchema->foreignKeys;
            foreach ($foreignKeys as $fk) {
                if (isset($fk['user_id']) || (isset($fk[0]) && $fk[0] === 'user')) {
                    $fkExists = true;
                    break;
                }
            }
        }
        
        if (!$fkExists) {
            try {
                $this->addForeignKey(
                    'fk-sales-user_id',
                    '{{%sales}}',
                    'user_id',
                    '{{%user}}',
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tableSchema = $this->db->getTableSchema('{{%sales}}', true);
        
        // Check if foreign key exists before dropping
        $fkExists = false;
        if ($tableSchema) {
            $foreignKeys = $tableSchema->foreignKeys;
            foreach ($foreignKeys as $fk) {
                if (isset($fk['user_id']) || (isset($fk[0]) && $fk[0] === 'user')) {
                    $fkExists = true;
                    break;
                }
            }
        }
        
        if ($fkExists) {
            try {
                $this->dropForeignKey('fk-sales-user_id', '{{%sales}}');
            } catch (\Exception $e) {
                // Foreign key might not exist, ignore
            }
        }
        
        // Check if column exists before dropping
        if (isset($tableSchema->columns['user_id'])) {
            $this->dropColumn('{{%sales}}', 'user_id');
        }
    }
}

