<?php
namespace dispositiontools\microcdp\elements\actions;

use Craft;
use dispositiontools\microcdp\MicroCDP;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;

/**
 * Restore represents a Restore element action.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.1.0
 */
class MarkAsRead extends ElementAction
{
    /**
     * @var string|null The message that should be shown after the elements get restored
     */
    public $successMessage ="All done";

    /**
     * @var string|null The message that should be shown after some elements get restored
     */
    public $partialSuccessMessage = "Some of that worked";

    /**
     * @var string|null The message that should be shown if no elements get restored
     */
    public $failMessage = "Unfortunately that didn't work";

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return 'Mark as read';
    }

    /**
     * @inheritdoc
     */
    public function getTriggerHtml()
    {
        return '<div class="btn formsubmit">' . $this->getTriggerLabel() . '</div>';
    }

    /**
     * @inheritdoc
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        $anySuccess = false;
        $anyFail = false;
        $elementsService = Craft::$app->getElements();
        foreach ($query->all() as $element) {
            if (MicroCDP::$plugin->history->markRecordAsRead($element->id)) {
                $anySuccess = true;
            } else {
                $anyFail = true;
            }
        }

        if (!$anySuccess && $anyFail) {
            $this->setMessage($this->failMessage);
            return false;
        }

        if ($anyFail) {
            $this->setMessage($this->partialSuccessMessage);
        } else {
            $this->setMessage($this->successMessage);
        }

        return true;
    }
}
