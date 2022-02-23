<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * A small micro customer data platform inside craft
 *
 * @link      http://www.disposition.tools/
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\models;

use dispositiontools\microcdp\MicroCDP;

use Craft;
use craft\base\Model;

/**
 * Historytypes Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class History extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var string
     */
      public $id;
     	public $uid;
     	public $dateCreated;
     	public $dateUpdated;
      public $userId;
      public $recordId;
      public $notes;
      public $isAction;
      public $isCompleted;
      public $status;
      public $historyType;


    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['recordId', 'integer']
        ];
    }


  




}
