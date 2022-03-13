<?php
/**
 * MicroCDP plugin for Craft CMS 3.x
 *
 * A small micro customer data platform inside craft
 *
 * @link      http://www.disposition.tools/
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\microcdp\fields;

use dispositiontools\microcdp\MicroCDP;
use dispositiontools\microcdp\assetbundles\cdprecordsfield\CdpRecordsFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use craft\base\PreviewableFieldInterface;
use craft\helpers\Html;
use craft\web\View;
use yii\base\Event;
use craft\web\twig\variables\Cp;


/**
 * Email represents an Email field.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
class CdpRecords extends Field implements PreviewableFieldInterface
{
    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Records';
    }

    /**
     * @inheritdoc
     */
    public static function valueType(): string
    {
        return 'string|null';
    }

    /**
     * @var string|null The input’s placeholder text
     */
    public $placeholder;
    public $htmlform;
    public $recordTypeId;

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {

        // get record types

        $recordTypes = MicroCDP::$plugin->records->getRecordTypes();
        $recordTypesOptions = [];
        foreach ($recordTypes as $id => $recordType)
        {

          $recordTypesOptions[]= array(
            'label' => $recordType->title,
            'value' => $id
          );
        }


        $settings['recordTypeId'] = $this->recordTypeId;
        $settings['recordTypeOptions'] = $recordTypesOptions;

        $settingsForm = Craft::$app->getView()->renderTemplate('micro-cdp/fields/CdpRecords_settings.twig',['settings'=>$settings]);

        return $settingsForm;
    }

    /**
     * @inheritdoc
     */
    protected function inputHtml($value, ElementInterface $element = null): string
    {

      $recordType = MicroCDP::$plugin->records->getRecordTypeById($this->recordTypeId);



      $this->htmlform = Craft::$app->getView()->renderTemplate('micro-cdp/fields/CdpRecords_form.twig', [
          'element' => $element,
          'recordType' => $recordType
      ]);

/*
        Craft::$app->getView()->hook('cp.entries.edit.details', function(array &$context){
          return $this->htmlform;
        });

        Craft::$app->getView()->hook('cp.users.edit.details', function(array &$context){
          return $this->htmlform;
        });
*/
      //return "hello " .$element->id;


      // get records associated with


      $id = Craft::$app->getView()->formatInputId($this->handle);
        $nameSpacedId = Craft::$app->getView()->namespaceInputId($id);
          Craft::$app->getView()->registerAssetBundle(CdpRecordsFieldAsset::class);
        $html = Craft::$app->getView()->renderTemplate('micro-cdp/fields/CdpRecords_input.twig', [
            'element' => $element,
            'recordType' => $recordType
        ]);


        return $html;
    }

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return [
            ['trim'],
            ['email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        if (!$value) {
            return '';
        }
        $value = Html::encode($value);
        return "<a href=\"mailto:{$value}\">{$value}</a>";
    }
}
