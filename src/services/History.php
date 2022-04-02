<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * A small micro customer data platform inside craft
 *
 * @link      http://www.disposition.tools/
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\services;

use dispositiontools\microcdp\MicroCDP;
use dispositiontools\microcdp\records\History as HistoryRecord;
use dispositiontools\microcdp\models\History as HistoryModel;

use Craft;
use craft\base\Component;

/**
 * History Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class History extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     MicroCDP::$plugin->history->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';

        return $result;
    }


    // MicroCDP::$plugin->history->createTestHistory();
    public function createTestHistory()
    {

        $historyModel = new HistoryModel();

        $historyModel->userId = 1;
        $historyModel->recordId = 54673;
        $historyModel->notes = "Yo Yo YO YO Yo YO";

        $testRecord = $this->saveHistory( $historyModel );
        print_r($testRecord);

        return $result;
    }


    public function viewHistory($recordId, $historyType="viewed",$userId = null )
    {

    }


    // MicroCDP::$plugin->history->createHistory($recordId,$historyType="viewed",$notes=null,$userId = null)
    public function createHistory($recordId,$historyType="viewed",$notes=null,$userId = null)
    {

        if(!$userId)
        {
          $currentUser = Craft::$app->getUser()->getIdentity();
          if($currentUser)
          {
            $userId = $currentUser->id;
          }
          else {
              $userId = null;
          }
        }

        $historyModel = new HistoryModel();

        $historyModel->userId = $userId;
        $historyModel->recordId = $recordId;
        $historyModel->historyType = 'read';
        $historyModel->notes = $notes;

        $historyRecord = $this->saveHistory( $historyModel );

        return $historyRecord;
    }


    // MicroCDP::$plugin->history->markRecordAsRead($recordId, $userId);
    public function markRecordAsRead($recordId,$notes=null,$userId = null)
    {

        if(!$userId)
        {
          $currentUser = Craft::$app->getUser()->getIdentity();
          $userId = $currentUser->id;
        }

        $historyModel = new HistoryModel();

        $historyModel->userId = $userId;
        $historyModel->recordId = $recordId;
        $historyModel->historyType = 'read';
        $historyModel->notes = $notes;

        $historyRecord = $this->saveHistory( $historyModel );

        return $historyRecord;
    }

    // MicroCDP::$plugin->history->getHistoryById($recordId);
    public function getHistoryById($recordId)
    {


        $historyModel = new HistoryModel();

        $historyModel->userId = 1;
        $historyModel->recordId = 54673;
        $historyModel->notes = "Yo Yo YO YO Yo YO";

        $testRecord = $this->saveHistory( $historyModel );
        print_r($testRecord);

        return $result;
    }

    // MicroCDP::$plugin->history->getHistoryByRecordId(recordId, $options = null);
    public function getHistoryByRecordId($recordId, $options = null)
    {

          if ( ! $recordId )
          {
            return [];
          }

          if(!$options)
          {
            $searchArray = [];
          }
          else
          {
            $searchArray = $options;
          }

          $searchArray['recordId'] = $recordId;
          $records = HistoryRecord::findAll($searchArray);

          $models = [];

          foreach ($records as $record)
          {
              $model = new HistoryModel($record);
              $models[$model->id] = $model;
          }

          return $models;
    }




    // MicroCDP::$plugin->history->saveHistory( $historyModel );

    public function saveHistory( $historyModel )
    {

      $record = HistoryRecord::findOne(
        [
          'id'     => $historyModel->id,
        ]
      );

      if (!$record) {
        $record              = new HistoryRecord();
      }

      $record->setAttributes($historyModel->getAttributes(), false);

      //print_r($record);
      $save = $record->save();

      if (!$save) {
        \Craft::getLogger()->log(
          $record->getErrors(),
          LOG_ERR,
          'microcdp'
        );
      }

      return $record;
    }

}
