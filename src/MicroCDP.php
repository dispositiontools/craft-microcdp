<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * Track user events and get user summary data
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp;

use dispositiontools\microcdp\services\Cdpevents as CdpeventsService;
use dispositiontools\microcdp\services\Cdpstats as CdpstatsService;
use dispositiontools\microcdp\services\Cpanel as CpanelService;
use dispositiontools\microcdp\services\Records as RecordsService;
use dispositiontools\microcdp\variables\MicroCDPVariable;
use dispositiontools\microcdp\models\Settings;
use dispositiontools\microcdp\utilities\MicrocdpUtility;
use dispositiontools\microcdp\widgets\Topevents as TopeventsWidget;
use dispositiontools\microcdp\widgets\Topusers as TopusersWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\elements\User as UserElement;

use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;

use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;

use dispositiontools\microcdp\models\Cdpevents as CdpeventsModel;
use dispositiontools\microcdp\records\Cdpevents as CdpeventsRecord;

use dispositiontools\microcdp\elements\Record as RecordElement;

use dispositiontools\microcdp\fields\CdpRecords as RecordsField;

use yii\base\Event;
use yii\web\User;

use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 *
 * @property  CdpeventsService $cdpevents
 * @property  CdpstatsService $cdpstats
 * @property  CpanelService $cpanel
 * @property  RecordsService $records
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class MicroCDP extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * MicroCDP::$plugin
     *
     * @var MicroCDP
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '0.0.7';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * MicroCDP::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;


        $this->initRoutes();

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'dispositiontools\microcdp\console\controllers';
        }

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'micro-cdp/cdpevents';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'micro-cdp/cdpevents/do-something';
            }
        );

        // Register our utilities
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = MicrocdpUtility::class;
            }
        );

        // Register our widgets
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES,
            function (RegisterComponentTypesEvent $event) {
                //$event->types[] = TopeventsWidget::class;
                //$event->types[] = TopusersWidget::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('microCDP', MicroCDPVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        Event::on(View::class, View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function (RegisterTemplateRootsEvent $event) {
           $event->roots['microcdp'] = __DIR__ . '/templates';
        });


        if (Craft::$app->request->getIsSiteRequest() and !Craft::$app->request->isActionRequest and !Craft::$app->response->getIsNotFound()) {

          $site = Craft::$app->getSites()->getCurrentSite();
          $siteid = $site->id;
          /*
          $actionTrigger = Craft::$app->config->general->actionTrigger;
          substr( Craft::$app->request->getPathInfo() , 0, strlen($actionTrigger)) != $actionTrigger
          */
          if( Craft::$app->getUser()->getIdentity())
          {
            $cdpeventModel  = new CdpeventsModel();
            $cdpeventModel->siteId = $siteid;
            $cdpeventModel->userId = Craft::$app->getUser()->getIdentity()->id;
            //$cdpeventModel->sessionId = Craft::$app->session->getId();
            $cdpeventModel->sessionId = '';
            $cdpeventModel->pageUrl = Craft::$app->request->getPathInfo();
            $cdpeventModel->eventType = 'pageview';

            if ( Craft::$app->getElements()->getElementByUri(Craft::$app->request->getPathInfo()) )
            {
              $element = Craft::$app->getElements()->getElementByUri(Craft::$app->request->getPathInfo());
              $cdpeventModel->elementId = $element->id;
              //$cdpeventModel->elementType= $element->type;
            }
            $cdpeventModel->referrer = Craft::$app->request->referrer;



            MicroCDP::$plugin->cdpevents->saveCdpevents( $cdpeventModel );
          }

        }

        Event::on(User::class, User::EVENT_AFTER_LOGIN, function(Event $e) {

            $site = Craft::$app->getSites()->getCurrentSite();
            $siteid = $site->id;
            $userDetails = $e->identity;
              //print_r($userDetails);

            $user = Craft::$app->users->getUserByUsernameOrEmail($userDetails->username);

            $cdpeventModel  = new CdpeventsModel();
            $cdpeventModel->siteId = $siteid;
            $cdpeventModel->userId = $user->id;
            //$cdpeventModel->sessionId = Craft::$app->session->getId();
            $cdpeventModel->sessionId = '';
            $cdpeventModel->pageUrl = Craft::$app->request->getPathInfo();
            $cdpeventModel->eventType = 'login';
            $cdpeventModel->referrer = Craft::$app->request->referrer;
            MicroCDP::$plugin->cdpevents->saveCdpevents( $cdpeventModel );

            //print_r($e);

        });
        Event::on(User::class, User::EVENT_AFTER_LOGOUT, function(Event $e) {
          $site = Craft::$app->getSites()->getCurrentSite();
          $siteid = $site->id;
          $userDetails = $e->identity;
            //print_r($userDetails);

          $user = Craft::$app->users->getUserByUsernameOrEmail($userDetails->username);

          $cdpeventModel  = new CdpeventsModel();
          $cdpeventModel->siteId = $siteid;
          $cdpeventModel->userId = $user->id;
          //$cdpeventModel->sessionId = Craft::$app->session->getId();
          $cdpeventModel->sessionId ='';
          $cdpeventModel->pageUrl = Craft::$app->request->getPathInfo();
          $cdpeventModel->eventType = 'logout';
          $cdpeventModel->referrer = Craft::$app->request->referrer;
          MicroCDP::$plugin->cdpevents->saveCdpevents( $cdpeventModel );

        });



        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = RecordsField::class;
            }
        );



/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'micro-cdp',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */
    protected function createSettingsModel()
    {
        return new Settings(
          [
          "savePageEvents" => false,
          "saveUserEvents" => true,
          ]
        );
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'micro-cdp/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }





    public function getCpNavItem()
    {

      if (Craft::$app->user->checkPermission('microinsightsAccessInsightModule')) {
            $navItem           = parent::getCpNavItem();
            $navItem['subnav'] = array(
                  'records' => ['label' => 'Records', 'url' => 'micro-cdp/records'],
                  'recordsTypes' => ['label' => 'Record Types', 'url' => 'micro-cdp/records/types'],
                  //'questions' => ['label' => 'Questions', 'url' => 'micro-insight/questions'],

            );

            return $navItem;
      }
    }


    // Protected Methods
    // =========================================================================

    private function initRoutes()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {



                $routes       = include __DIR__ . '/routes.php';
                $event->rules = array_merge($event->rules, $routes);


            }
        );
    }
}
