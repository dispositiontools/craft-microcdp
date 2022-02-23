<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * Track user events and get user summary data
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\services;

use dispositiontools\microcdp\MicroCDP;
use dispositiontools\microcdp\models\Cdpevents as CdpeventsModel;
use dispositiontools\microcdp\records\Cdpevents as CdpeventsRecord;
use Craft;
use craft\base\Component;

/**
 * Cdpevents Service
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
class Cdpevents extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     MicroCDP::$plugin->cdpevents->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (MicroCDP::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }


    // MicroCDP::$plugin->cdpevents->saveCdpevents( $cdpeventModel );

      public function saveCdpevents( $cdpeventModel )
    	{

    		$record = CdpeventsRecord::findOne(
    			[
    				'id'     => $cdpeventModel->id,
    			]
    		);

    		if (!$record) {
    			$record              = new CdpeventsRecord();
    		}

    		$record->setAttributes($cdpeventModel->getAttributes(), false);

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



      // MicroCDP::$plugin->cdpevents->getCdpeventsById($cdpeventModel);
    	public function getCdpeventsById($cdpeventModel)
    	{
    		$record = CdpeventsRecord::findOne(
    			[
    				'id'     => $cdpeventModel,
    			]
    		);

    		$model = new CdpeventsModel($record);

    		return $model;
    	}


      // MicroCDP::$plugin->cdpevents->getCdpeventsByUserId($cdpeventModel);
    	public function getCdpeventsByUserId($userId, $options = false)
    	{

    		$recordQuery = CdpeventsRecord::find()
        ->where([
          'userId'=>$userId
        ])
        ->orderBy('dateCreated desc');

        if ( $options && array_key_exists('eventType',$options) )
        {
          $recordQuery->andWhere([
              'eventType' => $options['eventType']
          ]);
        }

        if ($options && array_key_exists('limit',$options))
        {
          $recordQuery->limit($options['limit']);
        }

        $records = $recordQuery->all();

        $models = array();
        foreach ($records as $record)
        {
            $model = new CdpeventsModel($record);
            $models[$model->id] = $model;
        }

    		return $models;
    	}

      // MicroCDP::$plugin->cdpevents->getCdpevents($options);
    	public function getCdpevents($options = false)
    	{

    		$recordQuery = CdpeventsRecord::find()
        ->where(['!=','userId','1'])
        ->orderBy('dateCreated desc');

        if ( $options && array_key_exists('eventType',$options) )
        {
          $recordQuery->andWhere([
              'eventType' => $options['eventType']
          ]);
        }

        if ($options && array_key_exists('limit',$options))
        {
          $recordQuery->limit($options['limit']);
        }

        $records = $recordQuery->all();

        $models = array();
        foreach ($records as $record)
        {
            $model = new CdpeventsModel($record);
            $models[$model->id] = $model;
        }

    		return $models;
    	}




}
