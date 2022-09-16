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
use craft\helpers\ArrayHelper;
use craft\helpers\UrlHelper;
use craft\models\FieldLayout;


/**
 * Recordtypes Model
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
class Recordtype extends Model
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
    public $siteId;
    public $enabled;
    public $archived;

    public $recordCount;
    public $actionCount;
    public $actionCompletedCount;

    public $title;
    public $description;
    public $handle;

    public $fieldLayoutId;


    /** @var FieldLayout */
    private $fieldLayout;



    /**
   * Returns the owner's field layout.
   *
   * @return FieldLayout|null
   */
  public function getFieldLayout()
  {
      if ($this->fieldLayout === null && $this->fieldLayoutId) {
          $this->fieldLayout = \Craft::$app->getFields()->getLayoutById($this->fieldLayoutId);

          if ($this->fieldLayout === null) {
              $this->fieldLayout       = new FieldLayout();
              $this->fieldLayout->type = Event::class;
          }
      }

      return $this->fieldLayout;
  }

  /**
   * Sets the owner's field layout.
   *
   * @param FieldLayout $fieldLayout
   *
   * @return void
   */
  public function setFieldLayout(FieldLayout $fieldLayout)
  {
      $this->fieldLayout = $fieldLayout;
  }




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
            ['userId', 'integer'],
        ];
    }
}
