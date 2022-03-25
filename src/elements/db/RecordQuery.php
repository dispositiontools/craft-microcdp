<?php
namespace dispositiontools\microcdp\elements\db;
use Craft;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use dispositiontools\microcdp\elements\Record;
use dispositiontools\microcdp\records\History as HistoryRecord;

class RecordQuery extends ElementQuery
{
    public $recordTypeId;
    public $elementId;
    public $isEvent;
    public $eventOwner;

    public function recordTypeId($value)
    {
        $this->recordTypeId = $value;

        return $this;
    }

    public function elementId($value)
    {
        $this->elementId = $value;

        return $this;
    }

    public function isEvent($value)
    {
        $this->isEvent = $value;
        return $this;
    }

    public function isCompleted($value)
    {
        $this->isCompleted = $value;
        return $this;
    }

    public function dateEventStart($value)
    {
        $this->dateEventStart = $value;
        return $this;
    }

    public function dateEventEnd($value)
    {
        $this->dateEventEnd = $value;
        return $this;
    }

    public function eventOwner($value)
    {
        $this->eventOwner = $value;
        return $this;
    }


    protected function beforePrepare(): bool
    {
        // join in the records table
        $this->joinElementTable('microcdp_records');

        // select the price column
        $this->query->select([
            'microcdp_records.authorId',
            'microcdp_records.elementId',
            'microcdp_records.elementType',
            'microcdp_records.recordTypeId',
            'microcdp_records.contents',
            'microcdp_records.recordKindId',
            'microcdp_records.statusId',
            'microcdp_records.isAction',
            'microcdp_records.actionTypeId',
            'microcdp_records.actionDescription',
            'microcdp_records.isCompleted',
            'microcdp_records.dateActionFirstCompleted',
            'microcdp_records.isEvent',
            'microcdp_records.eventOwner',
            'microcdp_records.eventUsers',
            'microcdp_records.dateEventStart',
            'microcdp_records.dateEventEnd',
            'microcdp_records.eventDuration',
            'microcdp_records.lastUpdatedBy',
            'microcdp_records.dateActionCompleteBy',
        ]);

        $currentUser = Craft::$app->getUser()->getIdentity();
        if($currentUser)
        {
          $readHistoryQuery = (new Query())
              ->select(['recordId', 'dateUpdated AS isRead'])
              ->from([HistoryRecord::tableName()])
              ->where(Db::parseParam('userId', $currentUser->id))
              ->andWhere(Db::parseParam('historyType', 'read'))
              ->groupBy(['recordId']);

              //Ray( $readHistoryQuery );
              $this->query->addSelect('isRead');
              $this->subQuery->leftJoin(['h'=>$readHistoryQuery], '[[h.recordId]] = [[microcdp_records.id]]');
              $this->subQuery->select('h.isRead AS isRead');




        }




/**/
        if ($this->recordTypeId) {
            $this->subQuery->andWhere(Db::parseParam('microcdp_records.recordTypeId', $this->recordTypeId));
        }

        if ($this->elementId) {
            $this->subQuery->andWhere(Db::parseParam('microcdp_records.elementId', $this->elementId));
        }

        if ($this->isEvent) {
            $this->subQuery->andWhere(Db::parseParam('microcdp_records.isEvent', $this->isEvent));
        }

        if ($this->eventOwner) {
            $this->subQuery->andWhere(Db::parseParam('microcdp_records.eventOwner', $this->eventOwner));
        }

        if ($this->dateEventStart) {
            $this->subQuery->andWhere(Db::parseParam('microcdp_records.dateEventStart', $this->dateEventStart));
        }

        if ($this->dateEventEnd) {
            $this->subQuery->andWhere(Db::parseParam('microcdp_records.dateEventEnd', $this->dateEventEnd));
        }





        return parent::beforePrepare();
    }

}
