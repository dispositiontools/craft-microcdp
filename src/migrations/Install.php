<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * Track user events and get user summary data
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\migrations;

use dispositiontools\microcdp\MicroCDP;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * MicroCDP Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            //$this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // microcdp_cdpevents table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%microcdp_cdpevents}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%microcdp_cdpevents}}',
                [

                    'id'            => $this->primaryKey(),
                    'siteId'        => $this->integer()->defaultValue(NULL),
                    'uid'           => $this->uid(),
                    'dateCreated'   => $this->dateTime()->notNull(),
                    'dateUpdated'   => $this->dateTime()->notNull(),

                    'enabled'       => $this->boolean()->defaultValue(NULL),
                    'archived'      => $this->boolean()->defaultValue(NULL),

                    'userId'        => $this->integer()->defaultValue(NULL),
                    'cdpcookieId'   => $this->integer()->defaultValue(NULL),
                    'sessionId'     => $this->bigInteger()->defaultValue(NULL),
                    'elementId'     => $this->integer()->defaultValue(NULL),
                    'elementType'   => $this->string(64)->defaultValue(NULL),
                    'eventType'     => $this->string(64)->defaultValue(NULL),
                    'eventJson'     => $this->string()->defaultValue(NULL),
                    'referrer'      => $this->string(512)->defaultValue(NULL),
                    'pageUrl'       => $this->string(512)->defaultValue(NULL),

                ]
            );
        }

    // microcdp_cdpstats table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%microcdp_cdpstats}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%microcdp_cdpstats}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'some_field' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

    // microcdp_cdpcookies table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%microcdp_cdpcookies}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%microcdp_cdpcookies}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'some_field' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }

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
    // microcdp_cdpevents table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%microcdp_cdpevents}}', 'siteId'),
            '{{%microcdp_cdpevents}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    // microcdp_cdpstats table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%microcdp_cdpstats}}', 'siteId'),
            '{{%microcdp_cdpstats}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    // microcdp_cdpcookies table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%microcdp_cdpcookies}}', 'siteId'),
            '{{%microcdp_cdpcookies}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(null, '{{%microcdp_recordkinds}}', ['id'],      '{{%elements}}',        ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_records}}', ['id'],      '{{%elements}}',        ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_records}}', ['recordTypeId'],      '{{%microcdp_recordtypes}}',        ['id'], 'CASCADE');
        //$this->addForeignKey(null, '{{%microcdp_recordtypes}}',       ['fieldLayoutId'], '{{%fieldlayouts}}', ['id'], 'SET NULL');
        $this->addForeignKey(null, '{{%microcdp_records}}',       ['authorId'], '{{%users}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_history}}',       ['userId'], '{{%users}}', ['id'], 'CASCADE');
        $this->addForeignKey(null, '{{%microcdp_records}}', ['elementId'],      '{{%elements}}',        ['id'], 'CASCADE');

    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // microcdp_cdpevents table
        $this->dropTableIfExists('{{%microcdp_cdpevents}}');

    // microcdp_cdpstats table
        $this->dropTableIfExists('{{%microcdp_cdpstats}}');

    // microcdp_cdpcookies table
        $this->dropTableIfExists('{{%microcdp_cdpcookies}}');
    }
}
