<?php
namespace App\Doctrine;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;

 class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface{

    private $security;
    private $auth;


    public function __construct(Security $security,AuthorizationCheckerInterface $cheker)
    {
        $this->security = $security;
        $this->auth = $cheker;

    }
     private function addWhere(QueryBuilder $queryBuilder, string $resourceClass)
     {
           $user = $this->security->getUser();

        if(($resourceClass === Customer:: class || $resourceClass === Invoice::class)&& !$this->auth->$this->isGranted("ROLE_ADMIN") && $user instanceof User)  
        {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            if($resourceClass === Customer::class){
                $queryBuilder->andWhere("$rootAlias.user = :user");
            }elseif($resourceClass === Invoice::class){
                    $queryBuilder  ->join("$rootAlias.customer","c")
                                    ->andWhere("c.user = :user");
            }
            $queryBuilder->setParameter("user",$user);
            
        }
     }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
      $this->addWhere($queryBuilder,$resourceClass);
    }   

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = [])
    {
        $this->addWhere($queryBuilder,$resourceClass);
    }

} 