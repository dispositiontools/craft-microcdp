<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * Track user events and get user summary data
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\console\controllers;

use dispositiontools\microcdp\MicroCDP;

use dispositiontools\microcdp\models\Cdpevents as CdpeventsModel;
use dispositiontools\microcdp\records\Cdpevents as CdpeventsRecord;

use Craft;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Calculate Command
 *
 * The first line of this class docblock is displayed as the description
 * of the Console Command in ./craft help
 *
 * Craft can be invoked via commandline console by using the `./craft` command
 * from the project root.
 *
 * Console Commands are just controllers that are invoked to handle console
 * actions. The segment routing is plugin-name/controller-name/action-name
 *
 * The actionIndex() method is what is executed if no sub-commands are supplied, e.g.:
 *
 * ./craft micro-cdp/calculate
 *
 * Actions must be in 'kebab-case' so actionDoSomething() maps to 'do-something',
 * and would be invoked via:
 *
 * ./craft micro-cdp/calculate/do-something
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class CalculateController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Handle micro-cdp/calculate console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'something';

        echo "Welcome to the console CalculateController actionIndex() method\n";

        return $result;
    }

    /**
     * Handle micro-cdp/calculate/do-something console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'something';

        echo "Welcome to the console CalculateController actionDoSomething() method\n";

        return $result;
    }


     /**
     * Handle a request going to our plugin's actionSaveEvent URL,
     * ./craft micro-cdp/calculate/save-test-event
     *
     * @return mixed
     */
    public function actionSaveTestEvent()
    {
            $cdpeventModel  = new CdpeventsModel();

            $cdpeventModel->siteId = 1;
            $cdpeventModel->pageUrl = "hello";

            MicroCDP::$plugin->cdpevents->saveCdpevents( $cdpeventModel );

            return "hello";

    }


    /**
    * Handle a request going to our plugin's actionSaveEvent URL,
    * ./craft micro-cdp/calculate/save-test-history
    *
    * @return mixed
    */
   public function actionSaveTestHistory()
   {


           MicroCDP::$plugin->history->createTestHistory();

           return "hello";

   }
}
