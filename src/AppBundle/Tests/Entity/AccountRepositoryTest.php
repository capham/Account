<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Domain\Account\Account;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AccountRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $sql = 'truncate accounts;';
        $connection = $this->em->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
        $account = new Account(
            [
                'accountName' => 'LE XUAN HUONG',
                'accountNumber' => '1234567890987654',
                'currentBalance' => '10000',
                'email' => 'tonyle.microsoft@gmail.com',
            ]
        );
        $this->em->persist($account);
        $this->em->flush();
    }

    public function testSpend()
    {
        $accountEntity = static::$kernel->getContainer()
            ->get('account.repository')
            ->find(1);

        $account = static::$kernel->getContainer()
            ->get('account.repository')
            ->spend($accountEntity, 80000);
        $this->assertEquals(false, $account);}

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
