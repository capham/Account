<?php
namespace AppBundle\Domain\Account;

use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Account Entity
 * @author Huong Le <tonyle.microsoft@gmail.com>
 *
 * @ORM\Table(name="accounts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DTAccountRepository")
 *
 * @UniqueEntity(
 *     fields={"number"},
 *     message="Account number should be unique"
 * )
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Email should be unique"
 * )
 */
class Account
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"account", "detail"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="accountName", type="string", length=40)
     *
     * @Assert\NotBlank(
     *     message = "Account Name should not be blank"
     * )
     * @Assert\Length(
     *     min = 1,
     *     max = 40,
     *     minMessage = "Account Name must be at least 1 characters long.",
     *     maxMessage = "Account Name can not be longer than 40 characters.",
     * )
     *
     * @Groups({"account", "detail"})
     */
    private $accountName;

    /**
     * @var string
     *
     * @ORM\Column(name="accountNumber", type="string", length=16)
     *
     * @Assert\NotBlank(
     *     message = "Account Number should not be blank"
     * )
     * @Assert\Regex(
     *     pattern = "/^[0-9]{16}$/",
     *     htmlPattern = "^[0-9]{16}$",
     *     message = "Account Number must be 16 characters long."
     * )
     *
     * @Groups({"account", "detail"})
     */
    private $accountNumber;

    /**
     * @var float
     *
     * @ORM\Column(name="currentBalance", type="float", nullable=true, options={"unsigned":true, "default": 0})
     *
     * @Groups({"balance", "detail"})
     **/
    private $currentBalance = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=120)
     *
     * @Assert\NotBlank(
     *     message = "Email should not be blank"
     * )
     * @Assert\Length(
     *     min = 6,
     *     max = 120,
     *     minMessage = "Email must be at least 6 characters long.",
     *     maxMessage = "Email can not be longer than 120 characters.",
     * )
     * @Assert\Email(
     *     message = "The email is not a valid email.",
     *     checkMX = true
     * )
     *
     * @Groups({"account", "detail"})
     */
    private $email;

    public function __construct(array $data)
    {
        return $this->setData($data);
    }

    public function setData($data)
    {
        if (!empty($data)) {
            foreach ($data as $fieldName => $value) {
                $this->set($fieldName, $value);
            }
        }

        return $this;
    }

    public function get($fieldName)
    {
        $realFieldName = lcfirst(Inflector::classify($fieldName));
        return $this->$realFieldName;
    }

    public function set($fieldName, $value)
    {
        $realFieldName = lcfirst(Inflector::classify($fieldName));
        $this->$realFieldName = $value;
    }
}
