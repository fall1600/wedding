<?php
namespace Backend\BaseBundle\Security;

use Sensio\Bundle\FrameworkExtraBundle\Security\ExpressionLanguage as BaseExpressionLanguage;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * @Service("backend_security_expression_language")
 */
class ExpressionLanguage extends BaseExpressionLanguage
{

    /**
     * @InjectParams({
     *     "languageProvider" = @Inject("backend_security_expression_language_provider")
     * })
     */
    public function injectExpressionLanguageProvider(ExpressionFunctionProviderInterface $languageProvider)
    {
        foreach($languageProvider->getFunctions() as $function){
            $this->addFunction($function);
        }
    }

}