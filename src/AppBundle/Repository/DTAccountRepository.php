<?php
namespace AppBundle\Repository;

use AppBundle\Domain\Account\Account;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

/**
 * This class is used to handle the issues related to account
 *
 * @author Huong Le <tonyle.microsoft@gmail.com>
 */
class DTAccountRepository extends EntityRepository
{
    /**
     * This function is used to handle the spend
     *
     * @author Huong Le <tonyle.microsoft@gmail.com>
     */
    public function spend($account, $amount)
    {
        $currentBalance = $account->get('currentBalance');
        if ($currentBalance < $amount) {
            return false;
        }
        $newAmount = bcsub($currentBalance, $amount, 2);
        $account->set('currentBalance', $newAmount);
        try {
            $em = $this->getEntityManager();
            $em->persist($account);
            $em->flush();
            return $account;
        } catch (DBALException $ex) {
            throw new DBALException();
        }
    }

    /**
     * Exclusion strategy by JMS group name
     *
     * @author Huong Le <tonyle.microsoft@gmail.com>
     *
     * @param  Entity|Collection $data     Entity or array collection of entity
     * @param  string            $JMSGroup Name of JMS group
     *
     * @return array                       Array after the exclusion was done
     */
    public function getFinalResultByJMSGroup($data, $JMSGroup)
    {
        $serializer = SerializerBuilder::create()->build();
        $json = $serializer->serialize(
            $data,
            'json',
            SerializationContext::create()->setGroups(
                [$JMSGroup]
            )->setSerializeNull(true)->enableMaxDepthChecks()
        );
        $arr = json_decode($json, true);
        return $arr;
    }
}
