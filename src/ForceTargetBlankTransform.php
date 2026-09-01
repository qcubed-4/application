<?php

    /**
     *
     * Part of the QCubed PHP framework.
     *
     * @license MIT
     *
     */

    namespace QCubed;

    use HTMLPurifier_AttrTransform;
    use HTMLPurifier_Config;
    use HTMLPurifier_Context;

    /**
     * Class responsible for transforming anchor target attributes to "_blank".
     *
     * This class ensures that any existing target attributes on anchor elements
     * are set to "_blank", enforcing that links open in a new browser tab or
     * window. Links without a target attribute are not modified.
     */
    class ForceTargetBlankTransform extends HTMLPurifier_AttrTransform
    {
        /**
         * Forces an existing anchor target attribute to "_blank".
         *
         * Links without a target attribute are left unchanged.
         *
         * @param array $attr
         * @param HTMLPurifier_Config $config
         * @param HTMLPurifier_Context $context
         *
         * @return array
         * @throws \Exception
         */
        public function transform($attr, $config, $context): array
        {
            $token = $context->get('CurrentToken', true);

            if (
                $token &&
                $token->name === 'a' &&
                isset($attr['target'])
            ) {
                $attr['target'] = '_blank';
            }

            return $attr;
        }
    }