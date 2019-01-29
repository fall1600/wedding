<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/1/23
 * Time: 下午 2:26
 */

namespace Backend\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("case_convert")
 */
class CaseConvertService
{
    public function convert($column)
    {
        $data = explode('_', $column);
        foreach ($data as $key => $value) {
            $output[] = ucfirst($value);
        }
        return implode($output);
    }
}