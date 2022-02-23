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

use Craft;
use craft\base\Component;


use dispositiontools\microcdp\models\Recordtype as RecordtypeModel;
use dispositiontools\microcdp\records\Recordtype as RecordtypeRecord;


use dispositiontools\microcdp\elements\Record as RecordElement;

/**
 * Records Service
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
class Records extends Component
{
    // Public Methods
    // =========================================================================


    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     MicroCDP::$plugin->records->getRecordTypes()
     *
     * @return mixed
     */
    public function getRecordTypes()
    {
        $records = RecordtypeRecord::findAll(
            [
                'enabled'   => 1
            ]
        );
        $models = array();
        foreach ($records as $record)
        {
            $model = new RecordtypeModel($record);
            $models[$model->id] = $model;
        }

        return $models;
    }



    /**
     * Returns record element by Record Id and site id
     *
     * From any other plugin file, call it like this:
     *
     *     MicroCDP::$plugin->records->getRecordById($recordId,$siteId);
     *
     * @return mixed
     */
    public function getRecordById($recordId,$siteId = 1)
    {
      $query = RecordElement::find()->id($recordId)->siteId($siteId);

      return $query->one();
    }


    /**
     * Returns record element by Record Id and site id
     *
     * From any other plugin file, call it like this:
     *
     *     MicroCDP::$plugin->records->getElementsByRecordSearch($searchTerm,$siteId);
     *
     * @return mixed
     */
    public function getElementsByRecordSearch($searchTerm,$siteId = 1)
    {
      $query = RecordElement::find()->search($searchTerm)->siteId($siteId);



      $records =  $query->all();
      $elementIds = [];

      foreach($records as $record)
      {
          if(!in_array($record->elementId, $elementIds) )
          {
              $elementIds[] = $record->elementId;
          }
      }

      $elements = [];
      foreach ($elementIds as $elementId)
      {
          $element = Craft::$app->elements->getElementById($elementId);
          if($element)
          {
              $elements[] = $element;
          }
      }

      return $elements;

    }


    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     MicroCDP::$plugin->records->getRecordTypeById($recordTypeId)
     *
     * @return mixed
     */
    public function getRecordTypeById($recordTypeId)
    {
        $record = RecordtypeRecord::findOne(
              [
                  'enabled'   => 1,
                  'id'  =>$recordTypeId
              ]
          );
          if (!$record)
          {
            return false;
          }
          $model = new RecordtypeModel($record);

          return $model;
    }



    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     MicroCDP::$plugin->records->saveRecordType($recordTypeModel)
     *
     * @return mixed
     */
    public function saveRecordType($recordTypeModel)
    {
        $record = RecordtypeRecord::findOne(
            [
                'id'     => $recordTypeModel->id,
            ]
        );
        if (!$record) {
            $record              = new RecordtypeRecord();
        }
        $record->setAttributes($recordTypeModel->getAttributes(), false);

        //print_r($record);
        $save = $record->save();

        if (!$save) {
            \Craft::getLogger()->log(
                $record->getErrors(),
                LOG_ERR,
                'cdpmodule'
            );
        }

        return $save;
    }






}
