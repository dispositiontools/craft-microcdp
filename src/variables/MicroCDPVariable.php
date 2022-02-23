<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * Track user events and get user summary data
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\variables;

use dispositiontools\microcdp\MicroCDP;
use dispositiontools\microcdp\elements\db\RecordQuery;
use dispositiontools\microcdp\elements\Record as RecordElement;
use Craft;

/**
 * MicroCDP Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.microCDP }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class MicroCDPVariable
{
    // Public Methods
    // =========================================================================


    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.microCDP.records }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.microCDP.records() }}
     *
     * @param null $optional
     * @return string
     */
    public function records($attributes = null): RecordQuery
    {
      return RecordElement::buildQuery($attributes);
    }


    public function newRecord($recordTypeId = null)
    {
      $Record = new RecordElement();
      $Record->recordTypeId = $recordTypeId;

      return $Record;

    }

    public function newRecordForm($elementId = null)
    {
      $Record = new RecordElement();

      return $Record->getEditorHtml();

    }

    // {{ craft.microCDP.getElementsByRecordSearch(searchTerm, siteId) }}
    public function getElementsByRecordSearch($searchTerm, $siteId = 1)
    {
      return MicroCDP::$plugin->records->getElementsByRecordSearch($searchTerm,$siteId);
    }


    // {{ craft.microCDP.eventsByUserId(currentUser.id, $options) }}
    public function eventsByUserId($userId, $options = false)
    {
        $events = MicroCDP::$plugin->cdpevents->getCdpeventsByUserId($userId, $options);
        return $events;
    }

    // {{ craft.microCDP.events($options) }}
    public function events( $options = false)
    {
        $events = MicroCDP::$plugin->cdpevents->getCdpevents( $options);
        return $events;
    }
}
