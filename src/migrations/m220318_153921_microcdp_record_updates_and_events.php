<?php

namespace dispositiontools\microcdp\migrations;

use Craft;
use craft\db\Migration;

/**
 * m220318_153921_microcdp_record_updates_and_events migration.
 */
class m220318_153921_microcdp_record_updates_and_events extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...

        $table = '{{%microcdp_records}}';


        if (!$this->db->columnExists($table, 'isEvent')) {
            $this->addColumn($table, 'isEvent', $this->boolean()->defaultValue(NULL)->after('dateActionFirstCompleted'));
        }

        if (!$this->db->columnExists($table, 'eventOwner')) {
            $this->addColumn($table, 'eventOwner', $this->integer()->defaultValue(NULL)->after('isEvent'));
        }

        if (!$this->db->columnExists($table, 'eventUsers')) {
            $this->addColumn($table, 'eventUsers', $this->string(255)->defaultValue(NULL)->after('eventOwner'));
        }

        if (!$this->db->columnExists($table, 'dateEventStart')) {
            $this->addColumn($table, 'dateEventStart', $this->dateTime()->defaultValue(NULL)->after('eventUsers'));
        }

        if (!$this->db->columnExists($table, 'dateEventEnd')) {
            $this->addColumn($table, 'dateEventEnd', $this->dateTime()->defaultValue(NULL)->after('dateEventStart'));
        }



        if (!$this->db->columnExists($table, 'eventDuration')) {
            $this->addColumn($table, 'eventDuration', $this->integer()->defaultValue(NULL)->after('dateEventEnd'));
        }

        if (!$this->db->columnExists($table, 'lastUpdatedBy')) {
            $this->addColumn($table, 'lastUpdatedBy', $this->integer()->defaultValue(NULL)->after('authorId'));
        }


        if (!$this->db->columnExists($table, 'dateActionCompleteBy')) {
            $this->addColumn($table, 'dateActionCompleteBy', $this->dateTime()->defaultValue(NULL)->after('actionDescription'));
        }


        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m220318_153921_microcdp_record_updates_and_events cannot be reverted.\n";
        return false;
    }
}
