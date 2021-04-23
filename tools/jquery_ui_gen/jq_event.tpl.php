<?= '<?php' ?>

/**
*
* Part of the QCubed PHP framework.
*
* @license MIT
*
*/

namespace QCubed\Jqui\Event;

/**
 * Class <?= $event->eventClassName ?>

 *
 * The abstract <?= $event->eventClassName ?> class defined here is
 * code-generated. The code to generate this file is
 * in the /tools/jquery_ui_gen/jq_control_gen.php file
 * and you can regenerate the files if you need to.
 *
 * The comments in this file are taken from the api reference site, so they do
 * not always make sense with regard to QCubed. They are simply provided
 * as reference.
<?= jq_indent($event->description, 0, true); ?>
 *
 * @was <?= $event->oldEventClassName ?>Event
 */
class <?= $event->eventClassName ?> extends EventBase
{
    const EVENT_NAME = '<?= $event->eventName ?>';
}
