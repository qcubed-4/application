<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Css;

/**
 * Class Cursor
 *
 * @package QCubed\Css
 * @was QCursor
 */
abstract class CursorType
{
    /** Undefined */
    const NOT_SET = 'NotSet';
    /** Auto */
    const AUTO = 'auto';
    /** Cell selection cursor (like one used in MS Excel) */
    const CELL = 'cell';
    /** Right click context menu icon */
    const CONTEXT_MENU = 'context-menu';
    /** The cursor indicates that the column can be resized horizontally */
    const COL_RESIZE = 'col-resize';
    /** Indicates something is going to be copied */
    const COPY = 'copy';
    /** Frag the damn enemy! */
    const CROSS_HAIR = 'crosshair';
    /** Whatever the browser wants to */
    const CURSOR_DEFAULT = 'default';
    /** Indicating that something can be grabbed (like hand control when reading a PDF) */
    const GRAB = 'grab';
    /** Indicating that something is being grabbed (closed hand control when you drag a page in a PDF reader) */
    const GRABBING = 'grabbing';
    /** When you feel like running for your life! (the cursor usually is a '?' symbol) */
    const HELP = 'help';
    /** When a dragged element cannot be dropped */
    const NO_DROP = 'no-drop';
    /** No cursor at all - cursor gets invisible */
    const NONE = 'none';
    /** When an action is not allowed (can appear on disabled controls) */
    const NOT_ALLOWED = 'not-allowed';
    /** For links (usually creates the 'hand') */
    const POINTER = 'pointer';
    /** Indicates an event in progress */
    const PROGRESS = 'progress';
    /** The icon to move things across */
    const MOVE = 'move';
    /** Creates the 'I' cursor usually seen over editable controls */
    const TEXT = 'text';
    /** The text editing (I) cursor rotated 90 degrees for editing vertically written text */
    const VERTICAL_TEXT = 'vertical-text';
    /** Hourglass */
    const WAIT = 'wait';
    /** Magnification glass style zoom in (+) cursor */
    const ZOOM_IN = 'zoom-in';
    /** Magnification glass style zoom out (-) cursor */
    const ZOOM_OUT = 'zoom-out';
    // Resize cursors
    /** Right edge resize */
    const E_RESIZE = 'e-resize';
    /** Horizontal bi-directional resize cursor */
    const EW_RESIZE = 'ew-resize';
    /** Top edge resize */
    const N_RESIZE = 'n-resize';
    /** Top-right resize */
    const NE_RESIZE = 'ne-resize';
    /** Bidirectional North-East or South-West resize */
    const NESW_RESIZE = 'nesw-resize';
    /** Bidirectional vertical resize cursor */
    const NS_RESIZE = 'ns-resize';
    /** Top-left resize */
    const NW_RESIZE = 'nw-resize';
    /** Bidirectional North-West or South-East resize cursor */
    const NWSE_RESIZE = 'nwse-resize';
    /** Row can be resized (you might see it when trying to alter height of a row in MS Excel) */
    const ROW_RESIZE = 'row-resize';
    /** Bottom edge resize */
    const S_RESIZE = 's-resize';
    /** Bottom-right resize */
    const SE_RESIZE = 'se-resize';
    /** Bottom-left resize */
    const SW_RESIZE = 'sw-resize';
    /** Left edge resize */
    const W_RESIZE = 'w-resize';
}
