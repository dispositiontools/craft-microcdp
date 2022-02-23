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

/**
 * History Controller
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
class HistoryController extends Controller
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
     * e.g.: actions/micro-cdp/history
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = 'Welcome to the HistoryController actionIndex() method';

        return $result;
    }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/micro-cdp/history/do-something
     *
     * @return mixed
     */
    public function actionSaveHistoryJson()
    {
        $result = 'Welcome to the HistoryController actionDoSomething() method';

        return $result;
    }
}
