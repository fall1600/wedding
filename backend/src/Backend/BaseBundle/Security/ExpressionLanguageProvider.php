<?php
namespace Backend\BaseBundle\Security;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;

/**
 * @Service("backend_security_expression_language_provider")
 * @Tag("security.expression_language_provider")
 */
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return array(
            new ExpressionFunction(
                'has_role_or_superadmin',
                function ($role) {
                    return sprintf('in_array(%s, $roles) || $user->isSuperAdmin()', $role);
                },
                function (array $variables, $role) {
                    return in_array($role, $variables['roles']) || $variables['user']->isSuperAdmin();
                }
            ),
            new ExpressionFunction(
                'enabled',
                function ($function) {
                    return sprintf('$user->isSiteFunctionEnabled(%s)', $function);
                },
                function (array $variables, $function) {
                    return $variables['user']->isSiteFunctionEnabled($function);
                }
            ),
        );
    }
}