<?php
    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed;

    use QCubed\Project\HtmlAttributeManager;

    /**
     * Class TagStyler
     *
     * A class that encapsulates the styles for a tag. It can be used to swap out a collection of styles for another
     * collection of styles. Note that this is pretty much just an implementation of the QHtmlAttributeManager,
     * which manages both HTML attributes and CSS styles. Modern HTML, CSS frameworks and JavaScript frameworks use
     * more that just the "style" attribute to style an HTML object.
     *
     * @was QTagStyler
     * @package QCubed
     */
    class TagStyler extends HtmlAttributeManager
    {
        /**
         * Allows the row style to be overridden with an already existing TagStyler
         *
         * @param TagStyler $objOverrideStyle
         *
         * @return TagStyler
         */
        public function applyOverride(TagStyler $objOverrideStyle): TagStyler|static
        {
            $objNewStyle = clone $this;

            $objNewStyle->override($objOverrideStyle);

            return $objNewStyle;
        }

        /**
         * Returns HTML attributes for the QDataGridLegacy row.
         * Deprecated. Please use renderHtmlAttributes().
         *
         * @return string HTML attributes
         * @deprecated
         */
        public function getAttributes(): string
        {
            return $this->renderHtmlAttributes();
        }

        /**
         * Sets the attributes to the given array.
         *
         * @param array $attributes
         */
        public function setAttributes(array $attributes): void
        {
            $this->attributes = $attributes;
        }
    }
