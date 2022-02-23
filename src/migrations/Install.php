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

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    // microcdp_cdpevents table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%microcdp_cdpevents}}',
                'some_field',
                true
            ),
            '{{%microcdp_cdpevents}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

    // microcdp_cdpstats table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%microcdp_cdpstats}}',
                'some_field',
                true
            ),
            '{{%microcdp_cdpstats}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }

    // microcdp_cdpcookies table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%microcdp_cdpcookies}}',
                'some_field',
                true
            ),
            '{{%microcdp_cdpcookies}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
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
