<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * A small micro customer data platform inside craft
 *
 * @link      http://www.disposition.tools/
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\controllers;

use dispositiontools\microcdp\MicroCDP;

use Craft;
use craft\web\Controller;
use craft\base\Element;
use dispositiontools\microcdp\models\Recordtype as RecordtypeModel;
use craft\helpers\StringHelper;
use craft\helpers\DateTimeHelper;

use dispositiontools\microcdp\elements\Record as RecordElement;

/**
 * Records Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class RecordsController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/micro-cdp/records
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the RecordsController actionIndex() method';

        return $result;
    }




    /**
       * Handle a request going to our plugin's actionDoSomething URL,
       * e.g.: actions/micro-insight/quiz/save-quiz
       *
       * @return mixed
       */
      public function actionSaveRecord()
      {

          $currentUser = Craft::$app->getUser()->getIdentity();
          $this->requirePostRequest();
          $request = \Craft::$app->request;

          $postedRecordId = $request->post('recordId');
          $postedSiteId = $request->post('siteId');
            if ($postedRecordId)
            {
                $Record = MicroCDP::$plugin->records->getRecordById($postedRecordId,$postedSiteId);
            }
            else {
              $Record = false;
            }
            if (!$Record) {
                $Record = new RecordElement();
                $Record->siteId = $postedSiteId;

            }

            if($request->post('title'))
            {
              $Record->title = $request->post('title');
            }

             if($request->post('authorId'))
            {
              $Record->authorId = $request->post('authorId');
            }
            elseif($Record->authorId == "" || $Record->authorId == null )
            {
                $Record->authorId = $currentUser->id;
            }

            if($request->post('enabled'))
            {
              $Record->enabled = $request->post('enabled');
            }
            elseif($Record->enabled == null)
            {
              $Record->enabled = 1;
            }

            if($request->post('archived'))
            {
              $Record->archived = $request->post('archived');
            }
            elseif($Record->archived == null)
            {
              $Record->archived = 0;
            }

            if($request->post('fieldLayoutId'))
            {
              $Record->fieldLayoutId = $request->post('fieldLayoutId');
            }

            if($request->post('contents'))
            {
              $Record->contents = $request->post('contents');
            }

            if($request->post('recordTypeId'))
            {
              $Record->recordTypeId = $request->post('recordTypeId');
            }

            if($request->post('elementId'))
            {
              $Record->elementId = $request->post('elementId');
            }

            if($request->post('elementType'))
            {
              $Record->elementType = $request->post('elementType');
            }

            if($request->post('recordKindId'))
            {
              $Record->recordKindId = $request->post('recordKindId');
            }

            if($request->post('statusId'))
            {
              $Record->statusId = $request->post('statusId');
            }

            if($request->post('isAction'))
            {
              $Record->isAction = $request->post('isAction');
            }

            if($request->post('actionTypeId'))
            {
              $Record->actionTypeId = $request->post('actionTypeId');
            }

            if($request->post('actionDescription'))
            {
              $Record->actionDescription = $request->post('actionDescription');
            }

            if($request->post('isCompleted'))
            {
              $Record->isCompleted = $request->post('isCompleted');
            }

            if($request->post('dateActionFirstCompleted'))
            {
              $Record->dateActionFirstCompleted = $request->post('dateActionFirstCompleted');
            }



            if($request->post('isEvent'))
            {
              $Record->isEvent = $request->post('isEvent');
            }

            if($request->post('eventOwner'))
            {
              $Record->eventOwner = $request->post('eventOwner');


            }

            if($request->post('eventUsers'))
            {
              $Record->eventUsers = $request->post('eventUsers');
            }

            $dateEventStart = false;
            if($request->post('dateEventStart'))
            {


              $dateEventStart = DateTimeHelper::toDateTime($request->post('dateEventStart'));

              if($dateEventStart)
              {
                  $Record->dateEventStart = $dateEventStart->format('Y-m-d H:i:s');;
              }
            }



            if($request->post('dateEventEnd'))
            {

              $dateEventEnd = DateTimeHelper::toDateTime($request->post('dateEventEnd'));

              if($dateEventEnd)
              {
                  $Record->dateEventEnd = $dateEventEnd->format('Y-m-d H:i:s');;
                  $diff = $dateEventStart->diff($dateEventEnd);
                  $Record->eventDuration = DateTimeHelper::intervalToSeconds($diff);
              }

            }elseif($request->post('eventDuration'))
            {
              $duration = $request->post('eventDuration');
              $dateEventEnd = clone $dateEventStart;
              $dateEventEnd->modify('+'.$duration.' seconds');
              $Record->dateEventEnd =  $dateEventEnd->format('Y-m-d H:i:s');
              $Record->eventDuration = $request->post('eventDuration');
            }

            if($request->post('dateActionCompleteBy'))
            {
              $Record->dateActionCompleteBy = $request->post('dateActionCompleteBy');
            }

            

  




            if($request->post('lastUpdatedBy'))
            {
              $Record->lastUpdatedBy = $request->post('lastUpdatedBy');
            }
            else
            {
                $Record->lastUpdatedBy = $currentUser->id;
            }




            $Record->setFieldValuesFromRequest('fields');

            

            $Record->setScenario(RecordElement::SCENARIO_RECORDS);
              //$isSaved = \Craft::$app->elements->saveElement($organisation, true);
          // Save it
          if (\Craft::$app->elements->saveElement($Record, false)) {
              \Craft::$app->session->setNotice('Saved');

              return $this->redirectToPostedUrl($Record);
          }



      }







    // actions/microcdp/records/save-record-type
    public function actionSaveRecordType()
    {

          $currentUser = Craft::$app->getUser()->getIdentity();
          $this->requirePostRequest();
          $request = \Craft::$app->request;

          $postedRecordTypeId = $request->post('recordTypeId');
          $recordTypeModel = MicroCDP::$plugin->records->getRecordTypeById($postedRecordTypeId);
          if (!$recordTypeModel) {
              $recordTypeModel = new RecordtypeModel();
          }

          $recordTypeModel->id = $postedRecordTypeId;
          $recordTypeModel->title = $request->post('title');
          if($request->post('handle') != NULL )
          {
            $recordTypeModel->handle = StringHelper::slugify( $request->post('handle') );
          }
          else
          {
            $recordTypeModel->handle = StringHelper::slugify( $recordTypeModel->title );
          }


          $recordTypeModel->description = $request->post('description');
          $recordTypeModel->userId = $currentUser->id;
          $recordTypeModel->siteId = $request->post('siteId');

          if ( ! is_int($recordTypeModel->siteId) )
          {
              $recordTypeModel->siteId = 1;
          }

          $recordTypeModel->enabled = 1;
          $recordTypeModel->archived = 0;



          // Set the field layout
        $fieldLayout       = \Craft::$app->getFields()->assembleLayoutFromPost();
        $fieldLayout->type = Record::class;
        $recordTypeModel->setFieldLayout($fieldLayout);

        if ($fieldLayout) {
            \Craft::$app->getFields()->saveLayout($fieldLayout);
            $recordTypeModel->fieldLayoutId       = $fieldLayout->id;
        }
        // Save it
        if (MicroCDP::$plugin->records->saveRecordType($recordTypeModel)) {
            \Craft::$app->session->setNotice('Saved');
        }
        else
        {
          // code...
            \Craft::$app->session->setNotice('Error saving');
        }

        return $this->redirectToPostedUrl($recordTypeModel);

    }

}
