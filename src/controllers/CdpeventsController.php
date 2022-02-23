<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * Track user events and get user summary data
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\controllers;

use dispositiontools\microcdp\MicroCDP;

use dispositiontools\microcdp\models\Cdpevents as CdpeventsModel;
use dispositiontools\microcdp\records\Cdpevents as CdpeventsRecord;

use Craft;
use craft\web\Controller;

/**
 * Cdpevents Controller
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
class CdpeventsController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something','js-event'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/micro-cdp/cdpevents
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the CdpeventsController actionIndex() method';

        return $result;
    }



    /**
     * Handle a request going to our plugin's actionSaveEvent URL,
     * e.g.: actions/micro-cdp/cdpevents/js-event
     *
     * @return mixed
     */
    public function actionJsEvent()
    {

            if ( ! Craft::$app->request->isAjax )
            {
              return $this->asErrorJson("error");
            }
            $cdpeventModel  = new CdpeventsModel();

            if ( null !== Craft::$app->getRequest()->post('id') )
            {
                $cdpeventModel->id = Craft::$app->getRequest()->post('id');
            }

            if ( null !== Craft::$app->getRequest()->post('siteId') )
            {
                $cdpeventModel->siteId = Craft::$app->getRequest()->post('siteId');
            }

            if ( null !== Craft::$app->getRequest()->post('uid') )
            {
                $cdpeventModel->uid = Craft::$app->getRequest()->post('uid');
            }

            if ( null !== Craft::$app->getRequest()->post('dateCreated') )
            {
                $cdpeventModel->dateCreated = Craft::$app->getRequest()->post('dateCreated');
            }

            if ( null !== Craft::$app->getRequest()->post('dateUpdated') )
            {
                $cdpeventModel->dateUpdated = Craft::$app->getRequest()->post('dateUpdated');
            }

            if ( null !== Craft::$app->getRequest()->post('enabled') )
            {
                $cdpeventModel->enabled = Craft::$app->getRequest()->post('enabled');
            }

            if ( null !== Craft::$app->getRequest()->post('archived') )
            {
                $cdpeventModel->archived = Craft::$app->getRequest()->post('archived');
            }

            if ( null !== Craft::$app->getRequest()->post('userId') )
            {
                $cdpeventModel->userId = Craft::$app->getRequest()->post('userId');
            }

            if ( null !== Craft::$app->getRequest()->post('cdpcookieId') )
            {
                $cdpeventModel->cdpcookieId = Craft::$app->getRequest()->post('cdpcookieId');
            }

            if ( null !== Craft::$app->getRequest()->post('sessionId') )
            {
                $cdpeventModel->sessionId = Craft::$app->getRequest()->post('sessionId');
            }

            if ( null !== Craft::$app->getRequest()->post('eventType') )
            {
                $cdpeventModel->eventType = Craft::$app->getRequest()->post('eventType');
            }

            if ( null !== Craft::$app->getRequest()->post('eventJson') )
            {
                $cdpeventModel->eventJson = Craft::$app->getRequest()->post('eventJson');
            }

            if ( null !== Craft::$app->getRequest()->post('referrer') )
            {
                $cdpeventModel->referrer = Craft::$app->getRequest()->post('referrer');
            }

            if ( null !== Craft::$app->getRequest()->post('pageUrl') )
            {
                $cdpeventModel->pageUrl = Craft::$app->getRequest()->post('pageUrl');
            }


            MicroCDP::$plugin->cdpevents->saveCdpevents( $cdpeventModel );

            return "hello";
    }





    /**
     * Handle a request going to our plugin's actionSaveEvent URL,
     * e.g.: actions/micro-cdp/cdpevents/save-event
     *
     * @return mixed
     */
    public function actionSaveEvent()
    {
            $cdpeventModel  = new CdpeventsModel();

            if ( null !== Craft::$app->getRequest()->post('id') )
            {
                $cdpeventModel->id = Craft::$app->getRequest()->post('id');
            }

            if ( null !== Craft::$app->getRequest()->post('siteId') )
            {
                $cdpeventModel->siteId = Craft::$app->getRequest()->post('siteId');
            }

            if ( null !== Craft::$app->getRequest()->post('uid') )
            {
                $cdpeventModel->uid = Craft::$app->getRequest()->post('uid');
            }

            if ( null !== Craft::$app->getRequest()->post('dateCreated') )
            {
                $cdpeventModel->dateCreated = Craft::$app->getRequest()->post('dateCreated');
            }

            if ( null !== Craft::$app->getRequest()->post('dateUpdated') )
            {
                $cdpeventModel->dateUpdated = Craft::$app->getRequest()->post('dateUpdated');
            }

            if ( null !== Craft::$app->getRequest()->post('enabled') )
            {
                $cdpeventModel->enabled = Craft::$app->getRequest()->post('enabled');
            }

            if ( null !== Craft::$app->getRequest()->post('archived') )
            {
                $cdpeventModel->archived = Craft::$app->getRequest()->post('archived');
            }

            if ( null !== Craft::$app->getRequest()->post('userId') )
            {
                $cdpeventModel->userId = Craft::$app->getRequest()->post('userId');
            }

            if ( null !== Craft::$app->getRequest()->post('cdpcookieId') )
            {
                $cdpeventModel->cdpcookieId = Craft::$app->getRequest()->post('cdpcookieId');
            }

            if ( null !== Craft::$app->getRequest()->post('sessionId') )
            {
                $cdpeventModel->sessionId = Craft::$app->getRequest()->post('sessionId');
            }

            if ( null !== Craft::$app->getRequest()->post('eventType') )
            {
                $cdpeventModel->eventType = Craft::$app->getRequest()->post('eventType');
            }

            if ( null !== Craft::$app->getRequest()->post('eventJson') )
            {
                $cdpeventModel->eventJson = Craft::$app->getRequest()->post('eventJson');
            }

            if ( null !== Craft::$app->getRequest()->post('referrer') )
            {
                $cdpeventModel->referrer = Craft::$app->getRequest()->post('referrer');
            }

            if ( null !== Craft::$app->getRequest()->post('pageUrl') )
            {
                $cdpeventModel->pageUrl = Craft::$app->getRequest()->post('pageUrl');
            }


            MicroCDP::$plugin->cdpevents->saveCdpevents( $cdpeventModel );

            return "hello";
    }
}
