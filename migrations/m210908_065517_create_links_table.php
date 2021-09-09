<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%links}}`.
 */
class m210908_065517_create_links_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%links}}', [
            'id' => $this->primaryKey(),
            'long_url'=>$this->string()->notNull(),
            'short_code'=>$this->string()->notNull(),
            'limit'=> $this->integer()->unsigned()->notNull(),
            'hits'=> $this->integer()->defaultValue(0)->unsigned(),
            'lifetime'=> $this->integer()->unsigned()->notNull(),
            'created_dt' => $this->timestamp()->notNull(),
            'updated_dt' => $this->timestamp()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%links}}');
    }
}
