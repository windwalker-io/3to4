<?php
/**
 * Part of Phoenix project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later;
 */

namespace Phoenix\Form;

use Windwalker\Legacy\Form\FieldDefinitionInterface;
use Windwalker\Legacy\Form\Form;

/**
 * The NullFiledDefinition class.
 *
 * @since  1.0
 * @deprecated Legacy code
 */
class NullFiledDefinition implements FieldDefinitionInterface
{
    /**
     * Define the form fields.
     *
     * @param Form $form The Windwalker form object.
     *
     * @return  void
     */
    public function define(Form $form)
    {
    }
}
