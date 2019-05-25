<?php
/**
 * OAuth Repository
 * @category Repository
 * @author <jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Repository;

/**
 * OAuthRepository
 **/
class OAuthRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Fetch oauth data for the user
     * @param $user
     * @param $appType
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function fetchOAuthData($user, $appType)
    {
        $qb = $this->createQueryBuilder('oauth')
            ->select('oauth.clientId as client_id')
            ->addSelect('oauth.clientSecret as client_secret')
            ->addSelect('oauth.redirectUri as redirect_uri')
            ->where('oauth.appType = :appType')
            ->andWhere('oauth.user = :user')
            ->setParameters([
                'appType' => $appType,
                'user' => $user
            ])
        ;

        return $qb->getQuery()->getArrayResult();
    }
}
