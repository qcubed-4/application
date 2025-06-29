<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Table;

use QCubed\Exception\Caller;
use QCubed\Query\Node as QQNode;
use QCubed\Query\QQ;

/**
 * Class NodeColumn
 *
 * A table column that displays the content of a database column represented by a NodeBase object.
 * The $objNodes can be a single node, or an array of nodes. If an array of nodes, the first
 * node is the display node, and the rest of the nodes will be used for sorting.
 *
 * @package QCubed\Table
 */
class NodeColumn extends PropertyColumn
{
    /**
     * Constructor method that initializes the object with a name and a set of QQNode\NodeBase nodes.
     *
     * @param string $strName The name of the object being constructed.
     * @param QQNode\NodeBase|QQNode\NodeBase[] $objNodes A single QQNode\NodeBase instance or an array of QQNode\NodeBase instances.
     *
     * @return void
     *
     * @throws Caller If the provided nodes are not instances of QQNode\NodeBase or are invalid.
     * @throws Caller If the first node is a top-level node or passes through "To Many" association nodes.
     */
    public function __construct(string $strName, $objNodes)
    {
        if ($objNodes instanceof QQNode\NodeBase) {
            $objNodes = [$objNodes];
        } elseif (empty($objNodes) || !is_array($objNodes) || !$objNodes[0] instanceof QQNode\NodeBase) {
            throw new Caller('Pass either a QQNode\\NodeBase node or an array of Nodes only');
        }

        $objNode = $objNodes[0]; // The First node is the data node, the rest are for sorting.

        if (!$objNode->_ParentNode) {
            throw new Caller('First QQNode\\NodeBase cannot be a Top Level Node');
        }
        if (($objNode instanceof QQNode\ReverseReference) && !$objNode->isUnique()) {
            throw new Caller('Content QQNode\\NodeBase cannot go through any "To Many" association nodes.');
        }

        $properties = array($objNode->_PropertyName);
        while ($objNode = $objNode->_ParentNode) {
            if (!($objNode instanceof QQNode\NodeBase)) {
                throw new Caller('QQNode\\NodeBase cannot go through any "To Many" association nodes.');
            }
            if (($objNode instanceof QQNode\ReverseReference) && !$objNode->isUnique()) {
                throw new Caller('QQNode\\NodeBase cannot go through any "To Many" association nodes.');
            }
            if ($strPropName = $objNode->_PropertyName) {
                $properties[] = $strPropName;
            }
        }
        $properties = array_reverse($properties);
        $strProp = implode('->', $properties);
        parent::__construct($strName, $strProp);

        // build sort nodes
        $objSortNodes = [];
        $objReverseNodes = [];
        foreach ($objNodes as $objNode) {
            if ($objNode instanceof QQNode\ReverseReference) {
                $objNode = $objNode->_PrimaryKeyNode;
            }
            $objSortNodes[] = $objNode;
            $objReverseNodes[] = $objNode;
            $objReverseNodes[] = false;
        }

        $this->OrderByClause = QQ::orderBy($objSortNodes);
        $this->ReverseOrderByClause = QQ::orderBy($objReverseNodes);
    }
}