<?php
declare(strict_types=1);

namespace BastSys\GraphQLBundle\Security\Voter;

use BastSys\GraphQLBundle\Field\ISecuredField;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;

/**
 * Class SecuredFieldVoter
 * @package BastSys\GraphQLBundle\Security\Voter
 * @author mirkl
 */
class SecuredFieldVoter extends Voter
{
    /** @var ISecuredField[] [fieldName => FieldInterface] */
    private $securedFields = [];

    /**
     * @internal
     *
     * @param ISecuredField $field
     */
    public function addSecuredField(ISecuredField $field) {
        $this->securedFields[$field->getName()] = $field;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject)
    {
        if($subject instanceof Query || $subject instanceof Mutation) {
            return isset($this->securedFields[$attribute]);
        }

        return false;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $field = $this->securedFields[$attribute];

        return $field->isGranted($token);
    }

}
