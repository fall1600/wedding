<?php
namespace Widget\PhotoBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as BaseConstraints;
use Widget\PhotoBundle\File\PhotoUploadFile;

class ImageValidator extends BaseConstraints\ImageValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if($value instanceof PhotoUploadFile) {
            parent::validate($value, $constraint);
        }
    }
}
