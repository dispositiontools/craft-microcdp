<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * A small micro customer data platform inside craft
 *
 * @link      http://www.disposition.tools/
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\elements;

use dispositiontools\microcdp\MicroCDP;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use dispositiontools\microcdp\elements\db\RecordQuery;
use craft\helpers\UrlHelper;
use craft\elements\actions\Delete;
use craft\helpers\Html;
use craft\i18n\Locale;
use dispositiontools\microcdp\elements\actions\MarkAsRead;
/**
 * Records Element
 *
 * Element is the base class for classes representing elements in terms of objects.
 *
 * @property FieldLayout|null      $fieldLayout           The field layout used by this element
 * @property array                 $htmlAttributes        Any attributes that should be included in the element’s DOM representation in the Control Panel
 * @property int[]                 $supportedSiteIds      The site IDs this element is available in
 * @property string|null           $uriFormat             The URI format used to generate this element’s URL
 * @property string|null           $url                   The element’s full URL
 * @property \Twig_Markup|null     $link                  An anchor pre-filled with this element’s URL and title
 * @property string|null           $ref                   The reference string to this element
 * @property string                $indexHtml             The element index HTML
 * @property bool                  $isEditable            Whether the current user can edit the element
 * @property string|null           $cpEditUrl             The element’s CP edit URL
 * @property string|null           $thumbUrl              The URL to the element’s thumbnail, if there is one
 * @property string|null           $iconUrl               The URL to the element’s icon image, if there is one
 * @property string|null           $status                The element’s status
 * @property Element               $next                  The next element relative to this one, from a given set of criteria
 * @property Element               $prev                  The previous element relative to this one, from a given set of criteria
 * @property Element               $parent                The element’s parent
 * @property mixed                 $route                 The route that should be used when the element’s URI is requested
 * @property int|null              $structureId           The ID of the structure that the element is associated with, if any
 * @property ElementQueryInterface $ancestors             The element’s ancestors
 * @property ElementQueryInterface $descendants           The element’s descendants
 * @property ElementQueryInterface $children              The element’s children
 * @property ElementQueryInterface $siblings              All of the element’s siblings
 * @property Element               $prevSibling           The element’s previous sibling
 * @property Element               $nextSibling           The element’s next sibling
 * @property bool                  $hasDescendants        Whether the element has descendants
 * @property int                   $totalDescendants      The total number of descendants that the element has
 * @property string                $title                 The element’s title
 * @property string|null           $serializedFieldValues Array of the element’s serialized custom field values, indexed by their handles
 * @property array                 $fieldParamNamespace   The namespace used by custom field params on the request
 * @property string                $contentTable          The name of the table this element’s content is stored in
 * @property string                $fieldColumnPrefix     The field column prefix this element’s content uses
 * @property string                $fieldContext          The field context this element’s content uses
 *
 * http://pixelandtonic.com/blog/craft-element-types
 *
 * @author    Disposition Tools
 * @package   MicroCDP
 * @since     1.0.0
 */
class Record extends Element
{

    const TABLE     = '{{%microcdp_records}}';
    const TABLE_STD = 'microcdp_records';
    const STATUS_LIVE = 'live';
    const SCENARIO_RECORDS = 'record';


    // Public Properties
    // =========================================================================

    /**
     * Some attribute
     *
     * @var string
     */

    public $authorId;
    public $elementId;
    public $elementType;
    public $recordTypeId;
    public $contents;
    public $recordKindId;
    public $statusId;
    public $isAction;
    public $actionTypeId;
    public $actionDescription;
    public $isCompleted;
    public $dateActionFirstCompleted;
    public $recordTypeTitle;
    public $isRead = false;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('micro-cdp', 'Records');
    }




    /**
     * @inheritdoc
     */
    public static function lowerDisplayName(): string
    {
        return Craft::t('micro-cdp', 'record');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('micro-cdp', 'Records');
    }

    /**
     * @inheritdoc
     */
    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('micro-cdp', 'records');
    }

    /**
     * @inheritdoc
     */
    public static function refHandle()
    {
        return 'cdprecord';
    }

    /**
     * @inheritdoc
     */
    public static function trackChanges(): bool
    {
        return true;
    }










    /**
     * Returns whether elements of this type will be storing any data in the `content`
     * table (tiles or custom fields).
     *
     * @return bool Whether elements of this type will be storing any data in the `content` table.
     */
    public static function hasContent(): bool
    {
        return true;
    }

    /**
     * Returns whether elements of this type have traditional titles.
     *
     * @return bool Whether elements of this type have traditional titles.
     */
    public static function hasTitles(): bool
    {
        return true;
    }

    /**
     * Returns whether elements of this type have statuses.
     *
     * If this returns `true`, the element index template will show a Status menu
     * by default, and your elements will get status indicator icons next to them.
     *
     * Use [[statuses()]] to customize which statuses the elements might have.
     *
     * @return bool Whether elements of this type have statuses.
     * @see statuses()
     */
    public static function isLocalized(): bool
    {
        return true;
    }




    /**
     * @inheritdoc
     */
    public static function hasStatuses(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_LIVE => Craft::t('micro-cdp', 'Live'),
            self::STATUS_DISABLED => Craft::t('micro-cdp', 'Disabled')
        ];
    }


    public function getRecordType()
    {
        $recordTypeModel = MicroCDP::$plugin->records->getRecordTypeById($this->recordTypeId);
        return $recordTypeModel;
    }



    protected static function defineSearchableAttributes(): array
    {
        return ['id', 'contents'];
    }


    public function getHistory()
    {
      $history = MicroCDP::$plugin->history->getHistoryByRecordId($this->id, $options = null);
      return $history;
    }


    public function getReadStatus()
    {
      $currentUser = Craft::$app->getUser()->getIdentity();
      $history = MicroCDP::$plugin->history->getHistoryByRecordId($this->id, ['historyType'=>'read','userId'=> $currentUser->id] );
      if (count($history)>0)
      {
        return true;
      }
      return false;



    }


    /**
     * Creates an [[ElementQueryInterface]] instance for query purpose.
     *
     * The returned [[ElementQueryInterface]] instance can be further customized by calling
     * methods defined in [[ElementQueryInterface]] before `one()` or `all()` is called to return
     * populated [[ElementInterface]] instances. For example,
     *
     * ```php
     * // Find the entry whose ID is 5
     * $entry = Entry::find()->id(5)->one();
     *
     * // Find all assets and order them by their filename:
     * $assets = Asset::find()
     *     ->orderBy('filename')
     *     ->all();
     * ```
     *
     * If you want to define custom criteria parameters for your elements, you can do so by overriding
     * this method and returning a custom query class. For example,
     *
     * ```php
     * class Product extends Element
     * {
     *     public static function find()
     *     {
     *         // use ProductQuery instead of the default ElementQuery
     *         return new ProductQuery(get_called_class());
     *     }
     * }
     * ```
     *
     * You can also set default criteria parameters on the ElementQuery if you don’t have a need for
     * a custom query class. For example,
     *
     * ```php
     * class Customer extends ActiveRecord
     * {
     *     public static function find()
     *     {
     *         return parent::find()->limit(50);
     *     }
     * }
     * ```
     *
     * @return ElementQueryInterface The newly created [[ElementQueryInterface]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new RecordQuery(static::class);
    }



    /**
       * Defines the sources that elements of this type may belong to.
       *
       * @param string|null $context The context ('index' or 'modal').
       *
       * @return array The sources.
       * @see sources()
       */
      protected static function defineSources(string $context = null): array
      {
        $sources = array();

        $sources[] = [
                      'key'      => '*',
                      'label'    => 'All',
                      'criteria' => [],
                  ];

        $recordTypes = MicroCDP::$plugin->records->getRecordTypes();
        //$quizTypes = [];
        foreach ($recordTypes as $id => $recordType)
        {
          $sources[] = [
                'key' => 'recordTypeId:'.$id,
                'label' => $recordType->title,
                'data' => ['handle' => $recordType->handle],
                'criteria' => ['recordTypeId' => $id],
                'structureId' => null,
                'structureEditable' => null,
            ];
        }

          return $sources;



      }


      /**
       * @inheritDoc
       *
       * @param string|null $source
       *
       * @return array|null
       */
      protected static function defineActions(string $source = null): array
      {
          $actions = [];

          // Delete
          $actions[] = Delete::class;
          $actions[] = MarkAsRead::class;

          return $actions;
      }




    /**
     * @inheritdoc
     */
    protected static function defineSortOptions(): array
    {
        return [
            'title' => Craft::t('app', 'Title'),
            [
                'label' => Craft::t('app', 'Date Created'),
                'orderBy' => 'elements.dateCreated',
                'attribute' => 'dateCreated'
            ],
            [
                'label' => Craft::t('app', 'Date Updated'),
                'orderBy' => 'elements.dateUpdated',
                'attribute' => 'dateUpdated'
            ],
            [
                'label' => Craft::t('app', 'ID'),
                'orderBy' => 'elements.id',
                'attribute' => 'id',
            ]


        ];
    }

    /**
     * @inheritdoc
     */
    protected static function defineTableAttributes(): array
    {
        $attributes = [
           'id' => ['label' => Craft::t('app', 'Title')],

            'recordTypeId' => ['label' =>  'Record Type Id'],
            'recordTypeTitle' => ['label' => 'Record Type'],

            'author' => ['label' => 'Author'],
            'element' => ['label' => 'Element'],
            'isRead' => ['label' => 'Read'],
            //'uid' => ['label' => Craft::t('app', 'UID')],
            'dateCreated' => ['label' => Craft::t('app', 'Date Created')],
            'dateUpdated' => ['label' => Craft::t('app', 'Date Updated')],
        ];


        return $attributes;
    }



    public function getElement()
    {

      $elementId = (int)$this->elementId;
      if ( ! is_int($elementId)  )
      {
        return false;
      }
      $element = Craft::$app->elements->getElementById($elementId);

      if (!$element)
      {
        return false;
      }
      return $element;
    }

    public function getAuthor()
    {

      $authorId = (int)$this->authorId;
      if ( ! is_int($authorId)  )
      {
        return false;
      }
      $author = Craft::$app->users->getUserById($authorId);

      if (!$author)
      {
        return false;
      }
      return $author;
    }


    /**
    * @inheritDoc
    * @param string $attribute
    * @return string
    * @throws InvalidConfigException
    * @throws Exception
    */
   protected function tableAttributeHtml(string $attribute): string
   {
       switch ($attribute) {

           case 'recordTypeTitle':

               $recordType = $this->getRecordType();

               // If no group, bail
               if (!$recordType) {
                   return '';
               }

               return $recordType->title;
           case 'isRead':

               //$isRead = $this->getReadStatus();
               if($this->isRead)
               {
                 return '<span class="status enabled"></span>';
               }
               else
               {
                 return '<span class="status disabled"></span>';
               }


               return $this->isRead;

          case "author":

              $author = $this->getAuthor();
              if ( !$author )
              {
                return '';
              }

              if($author->name)
              {
                if($author->isEditable)
                {

                  return '<a href="'.$author->cpEditUrl.'">'.$author->name."</a>";
                }
                else
                {
                  return $author->name;
                }

              }

             return '';


           case 'element':

               $element= $this->getElement();
               if ( !$element )
               {
                 return '';
               }

               if($element->title and $element->title != "")
               {
                 return '<a href="'.$element->cpEditUrl.'">'.$element->title."</a>";
               }
               if($element->name)
               {
                  return '<a href="'.$element->cpEditUrl.'">'.$element->name."</a>";
               }

              return '';


           case 'id':
           case 'recordTypeId':
                 return $this->$attribute;

           case 'dateCreated':
           case 'dateUpdated':
              //return "date";
               $date = $this->$attribute;

               // If no date object, bail
               if (!$date) {
                   return '';
               }

               $dateStr = $this->_normalizeDate($date);
              return  Craft::$app->getFormatter()->asDate($dateStr, Locale::LENGTH_SHORT);



       }

       // If layout exists, return the value of matching field
       if ($layout = $this->getFieldLayout()) {
           foreach ($layout->getFields() as $field) {
               if ("field:{$field->id}" == $attribute) {
                   return parent::tableAttributeHtml($attribute);
               }
           }
       }

       return false;
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


     /**
      * @inheritdoc
      */
     protected function defineRules(): array
     {
         $rules = parent::defineRules();

         $rules[] = [['siteId'], 'integer', 'on' => [self::SCENARIO_RECORDS]];

         return $rules;
     }

    /**
     * Returns whether the current user can edit the element.
     *
     * @return bool
     */
    public function getIsEditable(): bool
    {
        return true;
    }

    /**
     * Returns the field layout used by this element.
     *
     * @return FieldLayout|null
     */
    public function getFieldLayout()
    {
        $recordTypeModel = MicroCDP::$plugin->records->getRecordTypeById($this->recordTypeId);
        if ($recordTypeModel and $recordTypeModel->fieldLayoutId != NULL) {

          $recordTypeModel->fieldLayoutId;
            return \Craft::$app->getFields()->getLayoutById($recordTypeModel->fieldLayoutId);
        }

        return null;
    }

    public function getGroup()
    {
        $group = [];
        return $group;
    }



    public function getCpEditUrl()
    {

        // The slug *might* not be set if this is a Draft and they've deleted it for whatever reason
        $path = 'micro-cdp/records/' . $this->getSourceId();

        return UrlHelper::cpUrl($path);
    }


    /**
     * @inheritdoc
     */

    public function getSupportedSites(): array
    {
        // limit to just the one site this element is set to so that we don't propagate when saving
        return [$this->siteId];
    }

    // Indexes, etc.
    // -------------------------------------------------------------------------

    /**
     * Returns the HTML for the element’s editor HUD.
     *
     * @return string The HTML for the editor HUD
     */
    public function getEditorHtml(): string
    {

      $html = "";


        $html .= Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => Craft::t('app', 'Title'),
                'siteId' => $this->siteId,
                'id' => 'title',
                'name' => 'title',
                'value' => $this->title,
                'errors' => $this->getErrors('title'),
                'first' => true,
                'autofocus' => true,
                'required' => true
            ]
        ]);







        $html .= parent::getEditorHtml();

        return $html;
    }

    // Events
    // -------------------------------------------------------------------------

    /**
     * Performs actions before an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     * @return bool Whether the element should be saved
     */
    public function beforeSave(bool $isNew): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is saved.
     *
     * @param bool $isNew Whether the element is brand new
     *
     * @return void
     */
    public function afterSave(bool $isNew)
    {



        $insertData = [
          'authorId'   => $this->authorId,
          'elementId'   => $this->elementId,
          'elementType'   => $this->elementType,
          'recordTypeId'   => $this->recordTypeId,
          'contents'  => $this->contents,
          'recordKindId'  => $this->recordKindId,
          'statusId'   => $this->statusId,
          'isAction' => $this->isAction,
          'actionTypeId' => $this->actionTypeId,
          'actionDescription' => $this->actionDescription,
          'isCompleted' => $this->isCompleted,
          'dateActionFirstCompleted' => $this->dateActionFirstCompleted,
        ];

          if ($isNew) {
              $insertData['id'] = $this->id;

              \Craft::$app->db
                  ->createCommand()
                  ->insert(self::TABLE, $insertData)
                  ->execute();
          } else {
              \Craft::$app->db
                  ->createCommand()
                  ->update(self::TABLE, $insertData, ['id' => $this->id])
                  ->execute();
          }

          parent::afterSave($isNew);


    }

    /**
     * Performs actions before an element is deleted.
     *
     * @return bool Whether the element should be deleted
     */
    public function beforeDelete(): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is deleted.
     *
     * @return void
     */
    public function afterDelete()
    {
    }

    /**
     * Performs actions before an element is moved within a structure.
     *
     * @param int $structureId The structure ID
     *
     * @return bool Whether the element should be moved within the structure
     */
    public function beforeMoveInStructure(int $structureId): bool
    {
        return true;
    }

    /**
     * Performs actions after an element is moved within a structure.
     *
     * @param int $structureId The structure ID
     *
     * @return void
     */
    public function afterMoveInStructure(int $structureId)
    {
    }




    /**
     * @param array $config
     *
     * @return EventQuery|ElementQueryInterface
     */
    public static function buildQuery(array $config = null): ElementQueryInterface
    {
        $query = self::find();

        if (null !== $config) {
            $propertyAccessor = new PropertyAccessor();

            foreach ($config as $key => $value) {
                if ($propertyAccessor->isWritable($query, $key)) {
                    $propertyAccessor->setValue($query, $key, $value);
                }
            }
        }

        $query->siteId = $query->siteId ?? \Craft::$app->sites->currentSite->id;

        return $query;
    }



    /**
     * Properly format datetime for database.
     *
     * @param $date
     * @return DateTime|mixed
     * @throws Exception
     */
    private function _normalizeDate($date)
    {
        // If it's an array, create a DateTime object
        if (is_array($date) && isset($date['timezone'])) {

            // If no date or time, bail
            if (!$date['date'] && !$date['time']) {
                return null;
            }

            // Get datetime
            $datetime = new DateTime(
                $date['date'].' '.$date['time'],
                new DateTimeZone($date['timezone'])
            );

            // If datetime was determined, return formatted string
            if ($datetime) {
                return DateTimeHelper::toIso8601($datetime);
            }

        }

        // Return unchanged value
        return $date;
    }


}
