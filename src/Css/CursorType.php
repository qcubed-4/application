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
        const string NOT_SET = 'NotSet';
        /** Auto */
        const string AUTO = 'auto';
        /** Cell selection cursor (like the one used in MS Excel) */
        const string CELL = 'cell';
        /** Right click context menu icon */
        const string CONTEXT_MENU = 'context-menu';
        /** The cursor indicates that the column can be resized horizontally */
        const string COL_RESIZE = 'col-resize';
        /** Indicates something is going to be copied */
        const string COPY = 'copy';
        /** Frag the damn enemy! */
        const string CROSS_HAIR = 'crosshair';
        /** Whatever the browser wants to */
        const string CURSOR_DEFAULT = 'default';
        /** Indicating that something can be grabbed (like hand control when reading a PDF) */
        const string GRAB = 'grab';
        /** Indicating that something is being grabbed (closed hand control when you drag a page in a PDF reader) */
        const string GRABBING = 'grabbing';
        /** When you feel like running for your life! (the cursor usually is a '?' symbol) */
        const string HELP = 'help';
        /** When a dragged element cannot be dropped */
        const string NO_DROP = 'no-drop';
        /** No cursor at all - cursor gets invisible */
        const string NONE = 'none';
        /** When an action is not allowed (can appear on disabled controls) */
        const string NOT_ALLOWED = 'not-allowed';
        /** For links (usually creates the 'hand') */
        const string POINTER = 'pointer';
        /** Indicates an event in progress */
        const string PROGRESS = 'progress';
        /** The icon to move things across */
        const string MOVE = 'move';
        /** Creates the 'I' cursor usually seen over editable controls */
        const string TEXT = 'text';
        /** The text editing (I) cursor rotated 90 degrees for editing vertically written text */
        const string VERTICAL_TEXT = 'vertical-text';
        /** Hourglass */
        const string WAIT = 'wait';
        /** Magnification glass style zoom in (+) cursor */
        const string ZOOM_IN = 'zoom-in';
        /** Magnification glass style zoom out (-) cursor */
        const string ZOOM_OUT = 'zoom-out';
        // Resize cursors
        /** Right-edge resize */
        const string E_RESIZE = 'e-resize';
        /** Horizontal bi-directional resize cursor */
        const string EW_RESIZE = 'ew-resize';
        /** Top edge resize */
        const string N_RESIZE = 'n-resize';
        /** Top-right resize */
        const string NE_RESIZE = 'ne-resize';
        /** Bidirectional North-East or South-West resize */
        const string NESW_RESIZE = 'nesw-resize';
        /** Bidirectional vertical resize cursor */
        const string NS_RESIZE = 'ns-resize';
        /** Top-left resize */
        const string NW_RESIZE = 'nw-resize';
        /** Bidirectional North-West or South-East resize cursor */
        const string NWSE_RESIZE = 'nwse-resize';
        /** Row can be resized (you might see it when trying to alter the height of a row in MS Excel) */
        const string ROW_RESIZE = 'row-resize';
        /** Bottom edge resize */
        const string S_RESIZE = 's-resize';
        /** Bottom-right resize */
        const string SE_RESIZE = 'se-resize';
        /** Bottom-left resize */
        const string SW_RESIZE = 'sw-resize';
        /** Left edge resize */
        const string W_RESIZE = 'w-resize';
    }
