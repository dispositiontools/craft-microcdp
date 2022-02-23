<?php

namespace dispositiontools\microcdp\migrations;

use Craft;
use craft\db\Migration;

/**
 * m201212_143848_microcdp_notes_and_history migration.
 */
class m201212_143848_microcdp_notes_and_history extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...

        if ($this->createTables()) {
            // $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            //$this->insertDefaultData();
        }



    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m201212_143848_microcdp_notes_and_history cannot be reverted.\n";
        return false;
    }



    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

        // microcdp_records table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%microcdp_records}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%microcdp_records}}',
                [

                    'id'                        => $this->primaryKey(),
                    'uid'                       => $this->uid(),
                    'dateCreated'               => $this->dateTime()->notNull(),
                    'dateUpdated'               => $this->dateTime()->notNull(),

                    'authorId'                  => $this->integer()->defaultValue(NULL),
                    'elementId'                  => $this->integer()->defaultValue(NULL),
                    'elementType'               => $this->string()->defaultValue(NULL),
                    'recordTypeId'              => $this->integer(),
                    'contents'                  => $this->text()->defaultValue(NULL),

                    'recordKindId'              => $this->integer()->defaultValue(NULL),

                    'statusId'                  =>  $this->integer()->defaultValue(NULL),

                    'isAction'                  => $this->boolean()->defaultValue(NULL),

                    'actionTypeId'              => $this->integer()->defaultValue(NULL),

                    'actionDescription'         => $this->text()->defaultValue(NULL),
                    'isCompleted'               => $this->boolean()->defaultValue(NULL),
                    'dateActionFirstCompleted'  => $this->dateTime()->defaultValue(NULL),

                ]
            );
        }


        // microcdp_records table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%microcdp_recordkinds}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%microcdp_recordkinds}}',
                [

                    'id'                        => $this->primaryKey(),
                    'uid'                       => $this->uid(),
                    'dateCreated'               => $this->dateTime()->notNull(),
                    'dateUpdated'               => $this->dateTime()->notNull(),
                    'handle' 	            => $this->string()->defaultValue(NULL),
                    'userId'                  => $this->integer()->defaultValue(NULL),
                    'recordTypeId'              => $this->integer(),
                    'description'                  => $this->text()->defaultValue(NULL),

                ]
            );
        }



        // microcdp_records table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%microcdp_recordtypes}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%microcdp_recordtypes}}',
                [

                    'id'                        => $this->primaryKey(),
                    'uid'                       => $this->uid(),
                    'dateCreated'               => $this->dateTime()->notNull(),
                    'dateUpdated'               => $this->dateTime()->notNull(),

                    'userId'                    => $this->integer()->defaultValue(NULL),
                    'siteId'                    => $this->integer()->notNull(),
                    'enabled' 	                => $this->boolean()->defaultValue(NULL),
                    'archived' 	                => $this->boolean()->defaultValue(NULL),

                    'recordCount'               => $this->integer()->defaultValue(NULL),
                    'actionCount'               => $this->integer()->defaultValue(NULL),
                    'actionCompletedCount'               => $this->integer()->defaultValue(NULL),

                    'title' 	                  => $this->string()->defaultValue(NULL),
                    'description' 	            => $this->string()->defaultValue(NULL),
                    'handle' 	            => $this->string()->defaultValue(NULL),

                    'fieldLayoutId'             => $this->integer()->notNull(),

                ]
            );
        }

      // microcdp_history table
      $tableSchema = Craft::$app->db->schema->getTableSchema('{{%microcdp_history}}');
      if ($tableSchema === null) {
          $tablesCreated = true;
          $this->createTable(
              '{{%microcdp_history}}',
              [

                  'id'                        => $this->primaryKey(),
                  'uid'                       => $this->uid(),
                  'dateCreated'               => $this->dateTime()->notNull(),
                  'dateUpdated'               => $this->dateTime()->notNull(),

                  'userId'                    => $this->integer()->defaultValue(NULL),
                  'historyType'             => $this->text(20)->defaultValue(NULL),
                  'recordId'                  => $this->integer(),
                  'notes'                  => $this->text()->defaultValue(NULL),

                  'status'                  =>  $this->integer()->defaultValue(NULL),
                  'isAction'                  => $this->boolean()->defaultValue(NULL),
                  'isCompleted'               => $this->boolean()->defaultValue(NULL),


              ]
          );
      }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(null, '{{%microcdp_records}}', ['elementId']);
        $this->createIndex(null, '{{%microcdp_records}}', ['authorId']);
        $this->createIndex(null, '{{%microcdp_records}}', ['isAction']);
        $this->createIndex(null, '{{%microcdp_records}}', ['isCompleted']);
        $this->createIndex(null, '{{%microcdp_records}}', ['recordTypeId']);
        $this->createIndex(null, '{{%microcdp_history}}', ['recordId']);
        $this->createIndex(null, '{{%microcdp_history}}', ['userId']);
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {

        $this->addForeignKey(null, '{{%microcdp_recordkinds}}', ['id'],      '{{%elements}}',        ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_records}}', ['id'],      '{{%elements}}',        ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_records}}', ['recordTypeId'],      '{{%microcdp_recordtypes}}',        ['id'], 'CASCADE');
        //$this->addForeignKey(null, '{{%microcdp_recordtypes}}',       ['fieldLayoutId'], '{{%fieldlayouts}}', ['id'], 'SET NULL');
        $this->addForeignKey(null, '{{%microcdp_records}}',       ['authorId'], '{{%users}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_history}}',       ['userId'], '{{%users}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_records}}', ['elementId'],      '{{%elements}}',        ['id'], 'CASCADE');

    }

}
