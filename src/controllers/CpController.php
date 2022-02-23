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

use dispositiontools\microcdp\models\Recordtype as RecordtypeModel;
use dispositiontools\microcdp\records\Recordtype as RecordtypeRecord;

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
class CpController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

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
     * Handle a request going to our plugin's actionQuizzesEntryAdd URL,
     * e.g.: actions/micro-cdp/cp/dashboard
     *
     * @return mixed
     */
    public function actionDashboard()
    {
        $title = "MicroCDP Dashboard";

        $unreadRecords=[];

        return $this->renderTemplate(
            'micro-cdp/_cp/dashboard',
            [
                'title'            => $title,
                'unreadRecords'    =>   $unreadRecords
            ]
        );
    }



    /**
     * Handle a request going to our plugin's actionQuizzesEntryAdd URL,
     * e.g.: actions/micro-cdp/cp/records-index
     *
     * @return mixed
     */
    public function actionRecordsIndex()
    {
        $title = "Records";

        $recordTypes = MicroCDP::$plugin->records->getRecordTypes();

        return $this->renderTemplate(
            'micro-cdp/_cp/records/index',
            [
                'title'            => $title,
                'recordTypes' =>   $recordTypes
            ]
        );
    }





    /**
     * Handle a request going to our plugin's actionQuizzesEntryAdd URL,
     * e.g.: actions/micro-cdp/cp/rrecords-entry-edit
     *
     * @return mixed
     */

    //
    public function actionRecordsEntryEdit($recordId)
    {
        $title = "Records";


        $record =  MicroCDP::$plugin->records->getRecordById( $recordId );
        if (! $record){
          exit();
        }

        $recordTypeModel = MicroCDP::$plugin->records->getRecordTypeById( $record->recordTypeId );

        return $this->renderTemplate(
            'micro-cdp/_cp/records/edit',
            [
                'title'            => $title,
                'record'           => $record,
                'recordType'      => $recordTypeModel,
            ]
        );
    }


    /**
     * Handle a request going to our plugin's actionQuizzesEntryAdd URL,
     * e.g.: actions/micro-cdp/cp/records-entry-add
     *
     * @return mixed
     */
    public function actionRecordsEntryAdd()
    {
        $title = "Records";

        $recordTypeRequestId = Craft::$app->request->getQueryParam('recordTypeId', false);

        if ( ! $recordTypeRequestId )
        {
            $recordTypeRequestId = 1;
        }

        $recordTypeModel = MicroCDP::$plugin->records->getRecordTypeById( $recordTypeRequestId );


        $record = new RecordElement();
        $record->siteId = 1;
        $record->recordTypeId = $recordTypeModel->id;

        return $this->renderTemplate(
            'micro-cdp/_cp/records/edit',
            [
                'title'            => $title,
                'record'           => $record,
                'recordType'      => $recordTypeModel,
            ]
        );
    }









    /**
     * Handle a request going to our plugin's actionQuizzesEntryAdd URL,
     * e.g.: actions/micro-cdp/cp/recordtypes-index
     *
     * @return mixed
     */
    public function actionRecordtypesIndex()
    {
      $recordTypes = MicroCDP::$plugin->records->getRecordTypes();
      return $this->renderTemplate(
          'micro-cdp/_cp/records/types/index',
          [
              'recordTypes'              => $recordTypes
          ]
      );
    }


    /**
     * Handle a request going to our plugin's actionQuizzesEntryAdd URL,
     * e.g.: actions/micro-cdp/cp/recordtypes-add
     *
     * @return mixed
     */
    public function actionRecordtypesAdd()
    {
      $recordType = new RecordtypeModel();
      return $this->renderTemplate(
          'micro-cdp/_cp/records/types/edit',
          [
              'recordType'              => $recordType
          ]
      );
    }


    /**
     * Handle a request going to our plugin's actionQuizzesEntryAdd URL,
     * e.g.: actions/micro-cdp/cp/recordtypes-edit
     *
     * @return mixed
     */
    public function actionRecordtypesEdit($recordTypeId)
    {

      if(!$recordTypeId)
      {
        return $this->redirect('microcdp/records/types');
      }

      $recordTypeModel = MicroCDP::$plugin->records->getRecordTypeById( $recordTypeId );

      if(!$recordTypeModel)
      {
         return $this->redirect('microcdp/records/types');
      }

      return $this->renderTemplate(
          'micro-cdp/_cp/records/types/edit',
          [
              'recordType'              => $recordTypeModel
          ]
      );
    }




}
